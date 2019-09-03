<?php
/*
	* 购物车控制器
*/
namespace api\modules\v1\controllers\order;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\order\Cart;
use api\modules\models\order\Address;
use api\modules\models\goods\Goods;
use common\models\DB;

class CartController extends CoreController
{

	/*
		*购物车列表
	*/
	public function actionCart_list()
	{
		$where = array('buyer_id'=>$this->_memberId);
		$data = Cart::getCartList($where);
		if(!$data) $this->out('购物车列表',$data);		
		$this->out('购物车列表',$data);
	}


	/*
		*添加购物车
	*/
	public function actionAdd_cart()
	{
		$goods = $this->cartValidate($this->request);

		$flag = $this->addCart($this->request,$goods);
		if($flag) $this->out('添加成功',array('cart_id'=>$flag));
		$this->error('添加失败');

	}


	/*
		*添加购物车条件验证
	*/
	private function cartValidate($params)
	{
		if($params['goods_id']<=0 || $params['nums']<=0) $this->error('参数错误');

		$field = "goods_id,goods_name,goods_price,goods_marketprice,goods_storage,goods_image";
		$goods = Goods::getGoodsById($params['goods_id'],$field);
		if(empty($goods) || !$goods) $this->error('商品不存在或已经下架');

		if($params['nums']>$goods['goods_storage']) $this->error('库存不足');

		return $goods; 
	}


	//加入购物车
	private function addCart($params,$goods)
	{

		//先删除那些直接购买生成的订单
		Cart::deleteAll(array('type'=>'2','buyer_id'=>$this->_memberId));   

		$cart = Cart::getCartByGoodsid($goods['id'],$this->_memberId);
		if($cart){
			$data = array('goods_num'=>$cart['goods_num']+$params['nums']);
			$flag =  DB::update('cart',$data,['cart_id'=>$cart['cart_id']]);
			if(!$flag) return false;
			return $cart['cart_id'];
		}else
		{
			$data = array(
				'buyer_id'		=> $this->_memberId,
				'goods_id'		=> $goods['goods_id'],
				'goods_name'	=> $goods['goods_name'],
				'goods_price'	=> $goods['goods_price'],
				'goods_num'		=> $params['nums'],
				'goods_image'	=> $goods['goods_image'],
				'type'			=> $params['buy_type'],
			);
			return DB::insert('cart',$data);			
		}
	}



	//删除购物车
	//cart_id 用户逗号隔开
	public function actionDelete_cart()
	{
		$cartIds = $this->request['cart_ids']?$this->request['cart_ids']:$this->error('参数错误');
		$cartIds = trim($cartIds,',');
		$flag    = Cart::deleteAll(array(['in','cart_id',$cartIds]));

		//重新返回购物车数据
		$this->actionCart_list();
	}

	//修改购物车数量
	// cart_id 购物车id
	// nums 数量
	public function actionEdit_cart()
	{
		$cartId = $this->request['cart_id']?$this->request['cart_id']:$this->error('参数错误');
		$nums   = $this->request['nums']?$this->request['nums']:$this->error('参数错误');
		$data   = array('goods_num'=>$nums);
		$flag   = DB::update('cart',$data,['cart_id'=>$cartId]);
		if($flag) $this->out('修改成功');
		$this->error('修改失败');
	}


	//获取购物车数量
	public function actionCart_num()
	{
		$where = ['buyer_id'=>$this->_memberId,'type'=>'1'];
		$cart = Cart::find()->select("sum(goods_num) as num")->where($where)->asarray()->one();
		$nums = $cart['num']?$cart['num']:0;
		$this->out('购物车数量',$nums);
	}


	//选择的购物车产品
	public function actionSelect_cart()
	{
		$CartIds = $this->request['cart_ids']?$this->request['cart_ids']:$this->error('参数错误');
		$ifcart   = $this->request['ifcart'];
		$nums     = $this->request['nums'];

		//获取商品信息
		$data = $this->getGoodsInfo($CartIds,$ifcart,$nums);
		if($data){
			$cartInfo = $data['data'];
			$goodsIds = $data['goods_ids'];
		} 

		//获取用户收货地址
		$address = $this->getMemberAddress();

		//计算运费
		$freight = $this->getFreight($goodsIds);

		$extend  = array('freight'=>$freight,'address'=>$address);
		$this->out('产品信息',$cartInfo,$extend);
	}


	//获取商品信息
	private function getGoodsInfo($cartIds,$ifcart,$nums)
	{
		$cartIds = explode('|',$cartIds);
		$where = ['in','cart_id',$cartIds];
		$where['buyer_id'] = $this->_memberId;
		$data = Cart::getCartList($where);	
		if(!$data) $this->error('产品信息');
		$goodsIds = array();
		foreach($data as $val)
		{
			$goodsIds[] = $val['goods_id'];
		}


		return array('data'=>$data,'goods_ids'=>$goodsIds);		
	}

	//获取用户收货地址
	private function getMemberAddress()
	{
		$data = Address::addressList($this->_memberId);
		if($data) return $data['0'];
		return false;
	}

	//获取运费
	private function getFreight($goodsIds)
	{
		$where = ['in','goods_id',$goodsIds];

		$field = "goods_freight";
		$data = Goods::getGoods($where,$field,['goods_freight'=> SORT_DESC]);
		$freight = $data['goods_freight'];
		return $freight;		
	}

}