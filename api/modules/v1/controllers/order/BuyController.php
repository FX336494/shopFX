<?php

/*
	用户购买（下单）相关控制器
*/
namespace api\modules\v1\controllers\order;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\order\Address;
use api\modules\models\order\Cart;
use common\utils\CommonFun;
use api\modules\models\goods\Goods;
use api\modules\models\goods\GoodsCommon;

class BuyController extends CoreController
{

	/*
		购买第一步，生成订单 并支付
		* cart_ids 购物车ID 以 | 隔开
		* address_id 收货地址ID
	*/
	public function actionCreate_order()
	{
		 
		$cartIds = explode('|', $this->request['cart_ids']);
		$addressId = $this->request['address_id'];  
		$extra = array('mess'=>$this->request['message']);

		//返回商品信息和地址
		$data = $this->buyCondition($addressId,$cartIds);

		//商品数据格式化
		$listData = $this->formatGoodsData($data['cart_goods']);

		//生成订单
		$res   = $this->createOrder($listData['list'],$data['address_info'],$extra);

		//删除购物车
		Cart::deleteAll(['in','cart_id',$cartIds]);

		$res['total_fee'] = $listData['total_fee'];

		$this->out('下单成功',$res);
	}


	private function formatGoodsData($cartGoods)
	{
		$list = array();
		$totalFee = 0;
		foreach($cartGoods as $goods)
		{

			$list['goods'][] = $goods;
			if(isset($list['goods_amount']))
				$list['goods_amount'] += $goods['goods_price']*$goods['goods_num'];
			else
				$list['goods_amount'] = $goods['goods_price']*$goods['goods_num'];

			if(isset($list['shopping_total']))
				$list['shopping_total'] += $goods['goods_freight'];
			else
				$list['shopping_total'] = $goods['goods_freight'];

			if(isset($list['order_amount']))
				$list['order_amount'] += ($goods['goods_price']*$goods['goods_num'])+$goods['goods_freight'];
			else
				$list['order_amount'] = ($goods['goods_price']*$goods['goods_num'])+$goods['goods_freight'];

			$totalFee += ($goods['goods_price']*$goods['goods_num'])+$goods['goods_freight'];

		}	

		return array('list'=>$list,'total_fee'=>$totalFee);	
	}

	//购买条件判断 并处理数据
	private function buyCondition($addressId,$cartIds)
	{
		//地址判断
		$addressInfo = Address::getAddress(['address_id'=>$addressId]);
		if(!$addressInfo) $this->error('收货地址有误');

		//库存判断
		$where = ['in','cart_id',$cartIds];
		$cartModel = new Cart;
		$cartGoods = $cartModel->getGoods($where);

		if(!$cartGoods) $this->error('订单已经失效');

		$list = array();
		foreach($cartGoods as $goods)
		{ 
			if($goods['goods_num']>$goods['goods_storage']){
				$this->error('商品'.$goods['goods_name'].'库存不足');
			} 

			if($goods['goods_state']!='1' || $goods['goods_verify']!='1')
				$this->error('商品'.$goods['goods_name'].'已下架');
		}
		return array('address_info'=>$addressInfo,'cart_goods'=>$cartGoods);
	}


	//生成订单
	private function createOrder($cartGoods,$addressInfo,$extra)
	{
		
		//支付单编号
		$paySn = CommonFun::makeRandSn($this->_memberId);

		$transaction = Yii::$app->db->beginTransaction();
		try{

			//格式化订单数据orders
			$order = $this->formatOrderData($cartGoods,$paySn);

			$orderId = $this->db::insert('orders',$order);
			if(!$orderId) throw new \Exception('orders订单添加失败');

			$flag = $this->addOrderGoods($orderId,$order,$cartGoods['goods']);
			if(!$flag) throw new \Exception('order_goods添加失败');

			$flag = $this->addOrderCommon($orderId,$order,$addressInfo,$extra);
			if(!$flag) throw new \Exception('order_common添加失败');

			//更新库存，增加销售
			$flag = $this->updateGoodsStorage($cartGoods['goods']);
			if(!$flag) throw new \Exception('库存更新失败');
			

			$transaction->commit();
			return array('pay_sn'=>$paySn,'order_id'=>$orderId);

		}catch(\Exception $e)
		{
			$transaction->rollBack();
			$this->error('下单失败:'.$e->getMessage());
		}
	}

	//格式化订单数据
	private function formatOrderData($cartGoods,$paySn)
	{
		$list = array();
		$list['pay_sn'] = $paySn;
		$list['buyer_id'] = $this->_memberId;
		$list['buyer_name'] = $this->_member['member_name'];
		$list['buyer_phone'] = $this->_member['member_mobile'];	
		$list['create_time'] = time();
		$list['goods_amount'] = $cartGoods['goods_amount'];
		$list['order_amount'] = $cartGoods['order_amount'];		
		$list['shipping_fee'] = $cartGoods['shopping_total'];
		return $list;
	}


	//生成商品订单
	private function addOrderGoods($orderId,$order,$cartGoods)
	{
		foreach($cartGoods as $goods)
		{
			$specVal = $this->getSpecValue($goods['spec_name'],$goods['goods_spec']);
			$data = [];
			$data['order_id']		= $orderId;
			$data['buyer_id']		= $this->_memberId;
			$data['goods_id']		= $goods['goods_id'];
			$data['goods_name']		= $goods['goods_name'];
			$data['goods_price']	= $goods['goods_price'];
			$data['goods_num']		= $goods['goods_num'];	
			$data['goods_image']	= $goods['goods_image'];
			$data['gc_id']			= $goods['gc_id'];		
			$data['goods_spec']		= $specVal;		

			if(!$this->db::insert('order_goods',$data)) return false;
		}
		return true;
	}


	//格式化规格
	private function getSpecValue($specName,$goodsSpec)
	{
		$list = '';
		if(!$goodsSpec) return $list;
		$specVal = array_values(json_decode($goodsSpec,1));
		$specName = array_values(json_decode($specName,1));

		foreach($specVal as $k=>$val){
			$list .= $specName[$k].'：'.$val.',';
		}
		return trim($list,',');
	}


	//添加订单拓展信息
	private function addOrderCommon($orderId,$order,$addressInfo,$extra)
	{
		$data = [];
		$data['order_id']				= $orderId;
		$data['order_message']			= $extra['mess'];
		$data['reciver_name']			= $addressInfo['true_name'];
		$data['reciver_phone']			= $addressInfo['mob_phone'];
		$data['reciver_info']			= json_encode($addressInfo,JSON_UNESCAPED_UNICODE);	
		$data['reciver_province_id']	= $addressInfo['province_id'];
		$data['reciver_city_id']		= $addressInfo['city_id'];
		$flag = $this->db::insert('order_common',$data);
		return $flag;
	}


	//更新库存和销量
	private function updateGoodsStorage($cartGoods)
	{
		foreach($cartGoods as $goods)
		{
			if(!GoodsCommon::updateStorage($goods['goods_id'],$goods['goods_num']))
				return false;
		}
		return true;
	}

}