<?php

//支付相关控制器

namespace api\modules\v1\controllers\order; 
use api\modules\v1\controllers\CoreController;
use api\modules\models\order\Orders;
use api\modules\models\order\OrderGoods;
use api\modules\models\order\Cart;
use api\modules\models\finance\Balance;
use Yii;
use api\modules\models\promotion\PintuanOpenOrder;
use api\modules\models\promotion\SeckillGoods;

class PaymentController extends CoreController
{

	//获取订单支付信息
	public function actionGet_pay_order_info()
	{
		if(!$orderId = $this->request['order_id']) $this->error('参数有误');
		$where = ['order_id'=>$orderId];
		$orderInfo = Orders::getOrderDetail($where,['*'],['order_common']);	

		$data = array();
		$data['total_fee'] = $orderInfo['order_amount'];		

		$orderCommon = $orderInfo['order_common'];
		$couponInfo = $orderCommon['coupon_info'];
		if($couponInfo){
			$data['total_fee'] -= $couponInfo['coupon_money'];
		}
		$data['order_id'] = $orderId;
		$this->out('支付信息',$data);
	}

	/*
		* 余额支付
		* 在订单列表的余额支付
		* order_id  订单id
	*/
	public function actionBalance_pay()
	{
		$orderId = $this->request['order_id'];
		$field   = ['*'];
		$order   = Orders::getOrderByOrderId($orderId,$field);
		$btype   = $this->request['btype'];
		if($order['order_state']>='2') $this->error('此订单已经支付');

		//扣除余额
		$res = $this->payCondition($this->request,$order);
		if(!$res['status'])
			$this->error($res['msg']);
		//开启事务
		$transaction = Yii::$app->db->beginTransaction();

		try{
			$totalFee = $order['order_amount'];
			$from = '用户【'.$this->_member['member_name'].'('.$this->_memberId.')】购买产品';
			$res = Balance::reduceBalance($this->_memberId,$btype,$totalFee,1,$orderId,$from);
			if(!$res['state']) throw new \Exception($res['msg']);

			//更新订单
			$params = array('payment_code'=>'balance_pay');
			$flag = Orders::updateOrdersByOrderId($order,$params);
			if(!$flag) throw new \Exception('订单更新失败');

			$transaction->commit();
			$this->out('支付成功');

		}catch(\Exception $e){
			$transaction->rollBack();
			$this->error($e->getMessage());			
		}
	}

	private function payCondition($params,$order)
	{
		if($this->_member['paypwd']!=md5($params['paypass']))
			return array('status'=>false,'msg'=>'密码错误');

		//**拼团订单
		if($order['order_type']=='2' && $order['join_order_id']>0)
		{
			//判断 此开团订单是否已经完成拼团
			$openOrder = PintuanOpenOrder::getOpenOrder(['order_id'=>$order['join_order_id']]);
			if(!$openOrder) return array('status'=>false,'msg'=>'开团订单有误');

			if($openOrder['join_nums'] >= $openOrder['open_nums'])
				return array('status'=>false,'msg'=>'此团已完成，请选择其它团');
		}

		//**秒杀订单 ** 这里默认秒杀只能直接购买 即只有一个商品
		if($order['order_type']=='3')
		{
			$orderGoods = OrderGoods::getOrderGoods($order['order_id']);
			$seckillInstance = SeckillGoods::getInstanceByGoods($orderGoods[0]['goods_id']);
			if($seckillInstance['state']!='1' || $seckillInstance['end_time']<time()){
				return array('status'=>false,'msg'=>'此此秒杀已失效');
			}
			if($seckillInstance['start_time']>time()){
				return array('status'=>false,'msg'=>'此秒杀还未开始');
			}
		}
		return array('status'=>true,'msg'=>'ok');
	}



}
