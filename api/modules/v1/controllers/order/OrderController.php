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

class OrderController extends CoreController
{

	//订单列表
	//state   订单状态
	//page    当前页码
	//perpage  每页显示数量 

	public function actionOrder_list()
	{
		$page = $this->request('page',1);
		$perpage = $this->request('perpage',10);
		$state = $this->request('state');
		$orderBy = 'order_id DESC';

		$where = [];
		$where['buyer_id'] = $this->_memberId; 
		if($state) $where['order_state'] = $state;
		if($state>=2) $orderBy = 'payment_time DESC';
		if($state==4) $where['evaluation_state'] = '0';
		$field 	= ['order_id','buyer_id','goods_amount','shipping_fee','order_amount','order_state','pay_sn','evaluation_state'];
		$offset = ($page-1)*$perpage;
		$orders = Orders::getOrders($where,$field,$offset,$perpage,$orderBy);

		//获取产品信息
		$orders = $this->getOrderGoods($orders);

		//订单状态
		$stateText = Yii::$app->params['orderState'];

		$this->out('订单列表',$orders,array('stateText'=>$stateText));
	}


	//获取产品信息
	private function getOrderGoods($orders)
	{
		$list = array();
		$field = ['goods_id','goods_name','goods_price','goods_num','goods_image','goods_spec'];
		foreach($orders as $order)
		{
			$goods = OrderGoods::getOrderGoods($order['order_id'],$field);
			$order['goods'][] = $goods;
			$list[] = $order;
		}
		return $list;
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
			$field = ['goods_id','goods_num'];
			$goods = OrderGoods::getOrderGoods($orderId,$field);
			//删除订单
			$where = ['order_id'=>$orderId,'buyer_id'=>$this->_memberId];
			if(!Orders::deleteAll($where)) throw new \Exception("订单删除失败");
			if(!OrderGoods::deleteAll($where)) throw new \Exception("产品订单删除失败");
			if(!OrderCommon::deleteAll(['order_id'=>$orderId])) throw new \Exception("订单common删除失败");

			//更新销量 和 库存
			foreach($goods as $val)
			{
				if(!GoodsCommon::updateStorage($val['goods_id'],$val['goods_num'],'0'))
					throw new \Exception("库存更新失败");
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
		if(!$orderId = $this->request('order_id'))
			$this->error('参数错误');		
		$field = ['order_id'];
		$where = ['order_id'=>$orderId];
		$extend = array();
		$extend[] = array(		
			'tb'=>'order_goods',
			'field' => array('goods_id','goods_name','goods_price','goods_num','goods_image','goods_spec'),
		);
		$order = Orders::getOrderInfo($where,$field,$extend);
		$this->out('订单信息',$order);
	}


}