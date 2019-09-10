<?php
namespace common\models\order;
use Yii;
use common\models\BaseModel;


class OrderCommonModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%order_common%}}';
	}

	public static function getOrderCommon($orderId,$field=["*"])
	{
		$where = ['order_id'=>$orderId];
		$data  =  self::find()->select($field)->where($where)->asarray()->one();
		if(isset($data['reciver_info']))
			$data['reciver_info'] = json_decode($data['reciver_info'],1);
		
		return $data;
	}

}