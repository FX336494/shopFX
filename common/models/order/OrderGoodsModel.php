<?php
namespace common\models\order;
use Yii;
use common\models\BaseModel;

class OrderGoodsModel extends BaseModel
{

	public static function tableName()
	{
		return '{{%order_goods%}}';
	}


	public static function getOrderGoods($orderId,$field=["*"])
	{
		$where = ['order_id'=>$orderId];
		return self::find()->select($field)->where($where)->asarray()->all();
	}


}