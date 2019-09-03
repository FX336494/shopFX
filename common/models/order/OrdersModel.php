<?php
namespace common\models\order;
use Yii;
use common\models\BaseModel;
use common\models\order\OrderGoodsModel;
use common\models\order\OrderCommonModel;
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
				}
			}
		}

		return $data;				
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