<?php
namespace api\modules\models\order;
use common\models\order\OrdersModel;
use api\modules\models\order\OrderGoods;
use Yii;


class Orders extends OrdersModel
{

	
	public static function getOrders($where,$field=["*"],$offset="",$limit='',$order='order_id DESC')
	{
		return self::find()->select($field)->where($where)->offset($offset)->limit($limit)->orderBy($order)->asarray()->all();
	}


	public static function getOrderByOrderId($orderId,$field=['*'])
	{
		$where = ['order_id'=>$orderId];
		return self::find()->select($field)->where($where)->asarray()->one();
	}

	//通过支付码获取订单
	public static function getOrdersByPaysn($paySn,$field=['*'])
	{
		$where = ['pay_sn'=>$paySn];
		return self::find()->select($field)->where($where)->asarray()->all();
	}


	//支付成功后，更新订单
	public static function updateOrdersByOrderId($orderId,$params)
	{
		$data = array(
			'payment_time'	=> time(),
			'payment_code'	=> $params['payment_code'],
			'order_state'	=> '2',
		);
		return self::updateAll($data,['order_id'=>$orderId]);
	}

	//支付成功后，更新订单
	public static function updateOrdersByPaysn($paysn,$params)
	{
		$data = array(
			'payment_time'	=> time(),
			'payment_code'	=> $params['payment_code'],
			'order_state'	=> '2',
		);
		return self::updateAll($data,['pay_sn'=>$paysn]);
	}

	//更新订单
	public static function updateOrder($orderId,$data)
	{
		$where = ['order_id'=>$orderId];
		return self::updateAll($data,$where);
	} 

	/*
		*按用户订单状态分类并统计数量
	*/
	public static function getOrdersByGroupState($memberId)
	{
		$data = self::find()->groupBy('order_state')->select('count(*) as total,order_state')->where(['buyer_id'=>$memberId])->asarray()->all();
		return $data;
	}

	public static function getOrderCount($memberId,$state)
	{
		$where = ['buyer_id'=>$memberId,'order_state'=>$state];
		if($state=='4') $where['evaluation_state'] = '0';
		$res = self::find()->select('count(*) as total,order_state')->where($where)->asarray()->one();
		return $res;
	}

	/*
		获取订单详情信息
		where 条件
		field orders 的字段
		extend 要查询订单的其它信息
	*/
	public static function getOrderInfo($where,$field=['*'],$extend=array())
	{
		$order = self::find()->select($field)->where($where)->asarray()->one();
		if(!$extend) return $order;
		$orderId = $order['order_id'];

		foreach($extend as $val)
		{
			$field = isset($val['field'])?$val['field']:['*'];
			switch ($val['tb']) {
				case 'order_goods':
					$goods = OrderGoods::getOrderGoods($orderId,$field);
					$order['order_goods'] = $goods;
					break;
				default:
					# code...
					break;
			}
		}
		return $order;
	}



}