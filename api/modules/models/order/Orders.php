<?php
namespace api\modules\models\order;
use common\models\order\OrdersModel;
use api\modules\models\order\OrderGoods;
use Yii;
use api\modules\models\promotion\PintuanOpenOrder;

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
	public static function updateOrdersByOrderId($order,$params)
	{
		$data = array(
			'payment_time'	=> time(),
			'payment_code'	=> $params['payment_code'],
			'order_state'	=> '2',
		);

		if($order['order_type']==2)
		{
			$data['pintuan_state'] = 1;
			//更新拼团相关信息
			$res = self::updatePintuanInfo($order);
			if(!$res['state']) return false;
				
			if($res['data']['state']=='2')
			{
				//拼团完成
				$data['pintuan_state'] = '2';
				//更新所有参与拼团的订单
				$where = ['or','join_order_id='.$order['join_order_id'] ,'order_id='.$order['join_order_id']];
				if(!self::updateAll(['pintuan_state'=>'2'],$where)) return false;
			}
		}
		return self::updateAll($data,['order_id'=>$order['order_id']]);
	}


	/*
		更新拼团相关数据
	*/
	public static function updatePintuanInfo($order)
	{
		if($order['join_order_id']>0)
		{
			//更新开团订单
			$where = ['order_id'=>$order['join_order_id']];
			$openOrder = PintuanOpenOrder::getOpenOrder($where);
			if(!$openOrder) return ['state'=>false,'msg'=>'开团订单不存在'];
			$data = array();
			$data['join_nums'] = $openOrder['join_nums']+1;
			$data['state'] = '1';
			$data['join_order_ids'] = $openOrder['join_order_ids'].','.$order['order_id'];
			if($data['join_nums']==$openOrder['open_nums'])
			{
				$data['state'] = '2';
				$data['complete_time'] = time();
			}
			$res = PintuanOpenOrder::updateAll($data,['open_order_id'=>$openOrder['open_order_id']]);
			if(!$res) return ['state'=>false,'msg'=>'更新开团订单失败'];
			return ['state'=>true,'msg'=>'ok','data'=>$data];
		}else
		{
			//更新开团订单
			$where = ['order_id'=>$order['order_id']];
			$openOrder = PintuanOpenOrder::getOpenOrder($where);
			$data = array();
			$data['start_time'] = time();
			$data['end_time'] = ($openOrder['end_time'] - $openOrder['start_time']) + time();
			$data['state'] = '1';
			$data['pay_state'] = '1';
			$data['join_nums'] = 1;
			$res = PintuanOpenOrder::updateAll($data,['open_order_id'=>$openOrder['open_order_id']]);
			if(!$res) return ['state'=>false,'msg'=>'更新开团订单失败'];
			return ['state'=>true,'msg'=>'ok','data'=>$data];			
		}
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