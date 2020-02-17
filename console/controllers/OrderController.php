<?php
namespace console\controllers; 
use Yii;
use \common\models\order\OrdersModel;
use \common\models\order\OrderCommonModel;
use common\models\order\OrderGoodsModel;
use api\modules\models\goods\GoodsCommon;
use common\models\promotion\PintuanOpenOrderModel;
use common\models\finance\BalanceModel;
use common\models\promotion\CouponsModel;
/*
	* 订单相关定时处理 
*/
class OrderController extends CoreController
{

	/*
		* 未付款订单处理
		* 2小时未支付自动取消  
		* 10分钟执行一次
		* 拼团订单另外处理
		0/10 * * * * php yii order/non-pay-order 
	*/
	public function actionNonPayOrder()
	{
		$t = time() - 2*86400;
		$where 	= ['order_state'=>'1'];
		$and 	= ['in','order_type',['1','3']];
		$and1   = ['<=','create_time',$t];
		$orders = OrdersModel::find()->select(['order_id'])
				->where($where)->andFilterWhere($and)->andFilterWhere($and1)
				->asarray()->all();
		if(!$orders) return false;

		foreach($orders as $order)
		{
			$transaction = \Yii::$app->db->beginTransaction();
			$where = ['order_id'=>$order['order_id']];
			$orderInfo = OrdersModel::getOrderDetail($where,['*'],['order_goods','order_common']);
			$orderGoods = $orderInfo['order_goods'];

			if(!$orderGoods) continue;

			$flag = true;
			foreach($orderGoods as $goods)
			{
				if(!GoodsCommon::updateStorage($goods['goods_id'],$goods['goods_num'],'0'))
					$flag = false;
			}
			if(!$flag){
				$transaction->rollback();
				continue;
			}

			//更新订单
			$orderData = ['order_state'=>'0','finnshed_time'=>time()];
			if(!OrdersModel::updateAll($orderData,$where)){
				$transaction->rollback();
			}

			//退回优惠券
			$orderCommon = $orderInfo['order_common'];
			$couponInfo = json_decode($orderCommon['coupon_info'],1);
			if($couponInfo){
				$where = ['id'=>$couponInfo['coupon_id']];
				if(!CouponsModel::updateAll(['state'=>'1','update_time'=>time()],$where))
					$transaction->rollback();
			}
			$transaction->commit();
		}
	}



	/*
		* 拼团失败订单处理
		* 规定时间内未拼团成功，取消订单，并退回支付金额
		* 1分钟执行一次
		0/1 * * * * php yii order/pintuan-failure-order 
	*/
	public function actionPintuanFailureOrder()
	{
		$where = array();
		$where['state'] = '1';
		$and = ['<','end_time',time()];
		$data = PintuanOpenOrderModel::find()
				->where($where)->andFilterWhere($and)
				->asarray()->all();
		if(!$data) return true;

		$curTime = time();
		foreach($data as $val)
		{
			$transaction = \Yii::$app->db->beginTransaction();
			//更新开团订单
			$temp1 = array('state'=>'3','complete_time'=>$curTime);
			$flag1 = PintuanOpenOrderModel::updateAll($temp1,['open_order_id'=>$val['open_order_id']]);

			//更新订单
			$temp2 = array(
				'pintuan_state'	=> '3',
				'order_state'	=> '0',
				'finnshed_time'	=> $curTime
			);
			$orderIdArr = explode(',', $val['join_order_ids']);
			$where = ['in','order_id',$orderIdArr];
			$flag2 = OrdersModel::updateAll($temp2,$where);

			//更新产品库存
			$orderGoods = OrderGoodsModel::find()->where($where)->asarray()->all();
			$flag3 = true;
			foreach($orderGoods as $goods)
			{
				if(!GoodsCommon::updateStorage($goods['goods_id'],$goods['goods_num'],'0'))
					$flag3 = false;
			}

			$flag4 = true;
			foreach($orderIdArr as $orderId)
			{
				$orderCommon = OrderCommonModel::getOrderCommon($orderId);
				//退回优惠券
				$couponInfo = json_decode($orderCommon['coupon_info'],1);
				if($couponInfo){
					$where = ['id'=>$couponInfo['coupon_id']];
					if(!CouponsModel::updateAll(['state'=>'1','update_time'=>time()],$where))
						$flag4 = false;
				}			
			}
			
			$flag = $flag1 && $flag2 && $flag3 && $flag4;
			if($flag){
				$transaction->commit();
			}
			$transaction->rollback();				
		}
	}




	/*
		* 拼团失败退款
		* 5分钟执行一次
		0/5 * * * * php yii order/pintuan-failure-refund 
	*/
	public function actionPintuanFailureRefund()
	{
		$where = array();
		$where['order_type'] = '2';
		$where['pintuan_state'] = '3';
		$field = ["order_id",'order_amount','buyer_id','payment_code'];
		$data = OrdersModel::find()->select($field)->where($where)->asarray()->all();
		if(!$data) return true;
		$curTime = time();
		foreach($data as $val)
		{
			$transaction = \Yii::$app->db->beginTransaction();
			if($val['payment_code']=='balance_pay')
			{
				//余额支付 退款到余额
				$res = BalanceModel::addBalance($val['buyer_id'],1,$val['order_amount'],3,$val['order_id'],'拼团退款',true);
				$flag1 = $res['state'];
			}

			//更新订单
			$temp1 = array(
				'pintuan_state'	=> '4',
				'finnshed_time'	=> $curTime
			);			
			$flag2 = OrdersModel::updateAll($temp1,['order_id'=>$val['order_id']]);
			$flag = $flag1 && $flag2;
			if($flag){
				$transaction->commit();
			}
			$transaction->rollback();
		}
	}




}