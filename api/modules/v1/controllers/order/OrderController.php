<?php

/*
	* 订单相关控制器
*/

namespace api\modules\v1\controllers\order;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\order\Orders;
use api\modules\models\order\OrderGoods;
use api\modules\models\order\OrderCommon;
use api\modules\models\goods\GoodsCommon;
use common\utils\Kdniao;
use api\modules\models\promotion\Coupons;

class OrderController extends CoreController
{

	//订单列表
	//state   订单状态
	//page    当前页码
	//perpage  每页显示数量 

	public function actionOrder_list()
	{
		$orderBy = 'order_id DESC';
		if($this->request('state')>=2) $orderBy = 'payment_time DESC';
	
		$whereArr = $this->formatWhere();
		$params = array(
			'page'	=> $this->request('page',1),
			'limit' => $this->request('perpage',10),
			'order'	=> 'order_id DESC',
		);	

		$orders = Orders::getOrderList($whereArr,$params,['promotion','order_goods','order_common']);

		//订单状态
		$stateText = Yii::$app->params['orderState'];

		$this->out('订单列表',$orders,array('stateText'=>$stateText));
	}

	private function formatWhere()
	{
		$whereArr = $where = $and = array();

		$state = $this->request('state');
		$where['buyer_id'] = $this->_memberId; 
		if($state) $where['order_state'] = $state;
		if($state==4) $where['evaluation_state'] = '0';

		$whereArr['where'] = $where;
		$whereArr['and'] = $and;
		return $whereArr;
	}


	/*
		取消订单
		order_id
	*/
	public function actionCancel_order()
	{
		if(!$orderId = $this->request('order_id'))
			$this->error('参数错误');

		//开启事务
		$transaction = Yii::$app->db->beginTransaction();

		try{
			$orderInfo = Orders::getOrderDetail(['order_id'=>$orderId],['*'],['order_goods','order_common']);
			$orderGoods = $orderInfo['order_goods'];
			//删除订单
			$where = ['order_id'=>$orderId,'buyer_id'=>$this->_memberId];
			if(!Orders::deleteAll($where)) throw new \Exception("订单删除失败");
			if(!OrderGoods::deleteAll($where)) throw new \Exception("产品订单删除失败");
			if(!OrderCommon::deleteAll(['order_id'=>$orderId])) throw new \Exception("订单common删除失败");

			//更新销量 和 库存
			foreach($orderGoods as $val)
			{
				if(!GoodsCommon::updateStorage($val['goods_id'],$val['goods_num'],'0'))
					throw new \Exception("库存更新失败");
			}

			//退回优惠券
			$orderCommon = $orderInfo['order_common'];
			$couponInfo = json_decode($orderCommon['coupon_info'],1);
			if($couponInfo){
				$where = ['id'=>$couponInfo['coupon_id']];
				if(!Coupons::updateAll(['state'=>'1','update_time'=>time()],$where))
					throw new \Exception("优惠券更新失败");
			}

			$transaction->commit();
			$this->out('取消成功');

		}catch(\Exception $e) {
			$transaction->rollBack();
			$this->error($e->getMessage());				
		}	
	}

	//确认收货
	public function actionOrder_receive()
	{
		if(!$orderId = $this->request('order_id'))
			$this->error('参数错误');

		$order = Orders::getOrderByOrderId($orderId);
		if(!$order) $this->error('订单不存在');

		if($order['order_state']>=4) $this->error('已操作');

		$data = array('order_state'=>'4','finnshed_time'=>time());
		$flag = Orders::updateAll($data,['order_id'=>$orderId]);
		if($flag) $this->out('操作成功');
		$this->error('操作失败');

	}

	//物流追踪
	public function actionSearch_deliver()
	{
		if(!$orderId = $this->request('order_id'))
			$this->error('参数错误');		
		$order = OrderCommon::find()->select(['shipping_express_id','shipping_code'])
									->where(['order_id'=>$orderId])
									->asarray()
									->one();
		if(!$order) $this->error('订单有误');

		$express = (new yii\db\Query())->from('express')
										->where(['id'=>$order['shipping_express_id']])
										->one();
		if(!$express) $this->error('物流信息有误');

		$kdniao = new Kdniao();
		$data = $kdniao->deliver_search($order['shipping_code'],$express['e_code_kdniao']);
		$extend = array('shopping_code'=>$order['shipping_code'],'e_name'=>$express['e_name']);
		$this->out('快递信息',$data,$extend);

	}

	//获取单个订单信息
	public function actionGet_order_info()
	{
		if(!$orderId = $this->request('order_id')) $this->error('参数错误');	
				
		$field = ['*'];
		$where = ['order_id'=>$orderId];
		$extend = array();
		$extend[] = array(		
			'tb'=>'order_goods',
			'field' => array('goods_id','goods_name','goods_price','goods_num','goods_image','goods_spec'),
		);
		$order = Orders::getOrderInfo($where,$field,$extend);
		$order['state_text'] = Yii::$app->params['orderState'][$order['order_state']];
		$order['order_type_text'] = Yii::$app->params['orderType'][$order['order_type']];

		$orderCommon = OrderCommon::getOrderCommon($orderId,['reciver_info','coupon_info']);
		$order['reciver_info'] = $orderCommon['reciver_info'];
		$order['coupon_info'] = $orderCommon['coupon_info'];
		$this->out('订单信息',$order);
	}



}