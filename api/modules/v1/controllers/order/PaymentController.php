<?php

//支付相关控制器

namespace api\modules\v1\controllers\order; 
use api\modules\v1\controllers\CoreController;
use api\modules\models\order\Orders;
use api\modules\models\order\Cart;
use api\modules\models\finance\Balance;
use Yii;

class PaymentController extends CoreController
{
	/*
		* 余额支付
		* 在订单列表的余额支付
		* order_id  订单id
	*/
	public function actionBalance_pay()
	{
		$orderId = $this->request['order_id'];
		$field   = ['order_id','order_amount','order_state'];
		$order   = Orders::getOrderByOrderId($orderId,$field);
		$btype   = $this->request['btype'];
		if($order['order_state']>='2') $this->error('此订单已经支付');

		//扣除余额
		$res = $this->payCondition($this->request);
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
			$flag = Orders::updateOrdersByOrderId($orderId,$params);
			if(!$flag) throw new \Exception('订单更新失败');

			$transaction->commit();
			$this->out('支付成功');

		}catch(\Exception $e){
			// var_dump($e->getMessage());  
			// $this->write_log($e);
			$transaction->rollBack();
			$this->error($e->getMessage());			
		}
	}

	private function payCondition($params)
	{
		if($this->_member['paypwd']!=md5($params['paypass']))
			return array('status'=>false,'msg'=>'密码错误');


		return array('status'=>true,'msg'=>'ok');
	}


	/*
		* 余额支付
		* 下单后直接支付
		* pay_sn 订单支付id号
	*/

	public function actionBalance_paysn()
	{
		$paySn = $this->request['paysn'];
		$field   = ['order_id','order_amount','order_state'];
		$orders  = Orders::getOrdersByPaysn($paySn,$field);
		$btype   = $this->request['btype'];
		$totalFee = 0;
		foreach($orders as $order){
			if($order['order_state']>='2') $this->error('此订单已经支付');
			$totalFee += $order['order_amount'];
		}
		
		//扣除余额
		$res = $this->payCondition($this->request);
		if(!$res['status'])
			$this->error($res['msg']);

		//开启事务
		$transaction = Yii::$app->db->beginTransaction();

		try{
			$from = '用户【'.$this->_member['member_name'].'('.$this->_memberId.')】购买产品';
			$res = Balance::reduceBalance($this->_memberId,$btype,$totalFee,1,$paySn,$from);
			if(!$res['state']) throw new \Exception($res['msg']);

			//更新订单
			$params = array('payment_code'=>'balance_pay');
			$flag = Orders::updateOrdersByPaysn($paySn,$params);
			if(!$flag) throw new \Exception('订单更新失败');

			$transaction->commit();
			$this->out('支付成功');

		}catch(\Exception $e){
			$transaction->rollBack();
			$this->error($e->getMessage());			
		}		
	}







	public function actionTest()
	{
		$res = Balance::addBalance(58,1,10000,1,10,'测试添加');
		//$res = Balance::reduceBalance($aid,1,10,1,10,'测试减少');
		var_dump($res);
	}

}
