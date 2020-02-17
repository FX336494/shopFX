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
use api\modules\models\promotion\PintuanGoods;
use api\modules\models\promotion\PintuanOpenOrder;
use api\modules\models\promotion\SeckillGoods;
use api\modules\models\promotion\Coupons;

class BuyController extends CoreController
{

	/*
		购买第一步，生成订单 并支付
		* cart_ids 购物车ID 以 | 隔开
		* address_id 收货地址ID
		* message 留言
		* buy_type 下单方式 1加入购物车 2立即购买 3拼团购买
		* freight 运费
	*/
	public function actionCreate_order()
	{
		 
		$cartIds = explode('|', $this->request['cart_ids']);
		$addressId = $this->request['address_id'];  
		$extra = array('mess'=>$this->request['message']);
		$freight = $this->request('freight',0);
		$couponId = $this->request('coupon_id',0);

		//返回商品信息和地址
		$data = $this->buyCondition($addressId,$cartIds);

		//商品数据格式化
		$listData = $this->formatGoodsData($data['cart_goods'],$freight);

		//优惠券信息
		$couponInfo = Coupons::calculateCouponMoney($couponId,$listData);
		$extra['coupon_info'] = $couponInfo;

		//生成订单
		$res = $this->createOrder($listData,$data['address_info'],$extra);

		//删除购物车
		Cart::deleteAll(['in','cart_id',$cartIds]);

		$couponMoney = isset($couponInfo['coupon_money'])?$couponInfo['coupon_money']:0;
		$res['total_fee'] = $listData['order_amount'] - $couponMoney;

		$this->out('下单成功',$res);
	}


	private function formatGoodsData($cartGoods,$freight)
	{
		$list = array();
		$totalFee = 0;
		$list['shopping_total'] = $freight;
		foreach($cartGoods as $goods)
		{
			$list['goods'][] = $goods;
			if(isset($list['goods_amount']))
				$list['goods_amount'] += $goods['goods_price']*$goods['goods_num'];
			else
				$list['goods_amount'] = $goods['goods_price']*$goods['goods_num'];
		}	
		$list['order_amount'] = $list['goods_amount'] + $list['shopping_total'];

		return $list;
	}

	//购买条件判断 并处理数据
	private function buyCondition($addressId,$cartIds)
	{
		//地址判断
		$addressInfo = Address::getAddress(['address_id'=>$addressId]);
		if(!$addressInfo) $this->error('收货地址有误');

		//库存判断
		$cartGoods = Cart::getGoods($cartIds);
		if(!$cartGoods) $this->error('订单已经失效');

		if($this->request('order_type')=='2')
		{
			if($this->request('join_order_id')>0)
			{
				//判断 此开团订单是否已经完成拼团
				$openOrder = PintuanOpenOrder::getOpenOrder(['order_id'=>$this->request('join_order_id')]);
				if(!$openOrder) $this->error('开团订单有误');
				if($openOrder['state'] == '2')
					$this->error('此团已完成，请选择其它团');
				if($openOrder['end_time']<time())
					$this->error('此团已结束，请选择其它团');
			}
		}

		//秒杀  ** 这里默认秒杀只能直接购买
		if($this->request('order_type')=='3')
		{
			$seckillInstance = SeckillGoods::getInstanceByGoods($cartGoods[0]['goods_id']);
			if($seckillInstance['state']!='1' || $seckillInstance['end_time']<time()){
				$this->error('此此秒杀已失效');
			}
			if($seckillInstance['start_time']>time()){
				$this->error('此秒杀还未开始');
			}
		}

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

			if($this->request('order_type')=='2' && $this->request('join_order_id')<=0)
			{
				//创建拼团开团订单 这时默认拼团只能购买单个产品
				if(!$this->createPintuanOpenOrder($cartGoods['goods'][0],$orderId))
					throw new \Exception("开团订单创建失败");
			}
			//更新优惠券
			if($extra['coupon_info']){
				//更新优惠券
				$where = ['id'=>$extra['coupon_info']['coupon_id']];
				if(!Coupons::updateAll(['state'=>'2','update_time'=>time()],$where))
					throw new \Exception("优惠券更新失败");
			}

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
		$list['order_type']	 = $this->request('order_type');
		$list['join_order_id'] = $this->request('join_order_id',0);
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
		$data['coupon_info']  			= json_encode($extra['coupon_info']);
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


	//创建开团订单
	private function createPintuanOpenOrder($goods,$orderId)
	{
		$pintuanInstance = PintuanGoods::getInstanceByGoods($goods['goods_id']);
		if($pintuanInstance['state']!='1' || $pintuanInstance['end_time']<time()){
			throw new \Exception("此拼团已失效");
			return false;
		}
		if($pintuanInstance['start_time']>time()){
			throw new \Exception("此拼团还未开始");
			return false;
		}
		$data = array();
		$data['order_id'] = $orderId;
		$data['open_member_id'] = $this->_memberId;
		$data['goods_id'] = $goods['goods_id'];
		$data['goods_commonid'] = $goods['goods_commonid'];
		$data['start_time'] = time();
		$data['end_time'] = time() + ($pintuanInstance['time_limit']*60); //这里先记录，支付之后再更新
		$data['open_nums'] = $pintuanInstance['min_nums'];
		$data['join_nums'] = 1;
		$data['join_order_ids'] = $orderId;
		$data['instance_id'] = $pintuanInstance['instance_id'];
		$model = new PintuanOpenOrder;
		if($model->load($data,'') && $model->save()) return true;
		// var_dump($model->errors);
		return false;
	}

}