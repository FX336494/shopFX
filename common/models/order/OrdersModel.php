<?php
namespace common\models\order;
use Yii;
use common\models\BaseModel;
use common\models\order\OrderGoodsModel;
use common\models\order\OrderCommonModel;
use common\models\promotion\PintuanOpenOrderModel;
/**
	* 订单 共用模型
 */

class OrdersModel extends BaseModel
{

	public static function tableName()
	{
		return '{{%orders%}}';
	}

	/*
		* 订单列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function getOrderList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();

		//扩展信息
		if(!$extends) return $data;
		foreach($data as &$val)
		{
			foreach($extends as $eType)
			{
				if($eType=='order_goods')
				{
					$val['order_goods'] = OrderGoodsModel::getOrderGoods($val['order_id']);
				}else if($eType=='order_common')
				{
					$val['order_common'] = OrderCommonModel::getOrderCommon($val['order_id']);
				}else if($eType=='promotion')
				{
					//获取拼团信息
					$val['promotion'] = [];
					if($val['order_type']>='2') $val['promotion'] = self::getPromotion($val);
				}
			}
		}
		return $data;				
	}	

	/*
		获取订单促销信息
	*/
	private static function getPromotion($order)
	{
		
		$promotionInfo = array();
		if($order['order_type']=='2')
		{
			//拼团
			$pintuanState = \Yii::$app->params['pintuanState'];
			$promotionInfo['promotion_text'] = '拼团';
			$promotionInfo['promotion_state'] = $pintuanState[$order['pintuan_state']];
			$joinOrderId = $order['join_order_id']?$order['join_order_id']:$order['order_id'];
			$openOrder = PintuanOpenOrderModel::getOpenOrder(['order_id'=>$joinOrderId]);
			$desc = '';
			if($openOrder['pay_state']=='1' && $openOrder['state']=='1'){
				$desc = '还差'.($openOrder['open_nums'] - $openOrder['join_nums']).'人';
			}
			$promotionInfo['promotion_desc'] = $desc;
			if($openOrder['state']=='2') $promotionInfo['promotion_text'] = $promotionInfo['promotion_state'];
		}

		if($order['order_type']=='3') 
		{
			$promotionInfo['promotion_text'] = '秒杀订单';
		}
		return $promotionInfo;
	}



	/*
		获取订单详情信息
		where 条件
		field orders 的字段
		extend 要查询订单的其它信息
	*/
	public static function getOrderDetail($where,$field=['*'],$extend=array())
	{
		$order = self::find()->select($field)->where($where)->asarray()->one();

		$order['order_state_text'] =  \Yii::$app->params['orderState'][$order['order_state']];
		$order['pay_state_text'] = $order['order_state']>=2?'已付款':'未付款';
		$order['order_type_text'] = \Yii::$app->params['orderType'][$order['order_type']];
		if($order['order_type']=='2'){
			$order['pintuan_state_text'] = Yii::$app->params['pintuanState'][$order['pintuan_state']];
		}
		//能否发货
		$order['is_deliver'] = 0;		
		if(($order['order_state']=='2' && $order['order_type']!='2') || ($order['order_type']=='2' && $order['pintuan_state']=='2')) $order['is_deliver'] = 1;


		if(!$extend) return $order;
		$orderId = $order['order_id'];
		foreach($extend as $val)
		{
			switch ($val) {
				case 'order_goods':
					$goods = OrderGoodsModel::getOrderGoods($orderId);
					$order['order_goods'] = $goods;
					break;
				case 'order_common':
					$orderCommon = OrderCommonModel::getOrderCommon($orderId);
					$order['order_common'] = $orderCommon;
				default:
					break;
			}
		}
		return $order;
	}




}