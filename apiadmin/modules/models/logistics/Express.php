<?php

/*
	物流公司相关
*/
namespace apiadmin\modules\models\logistics;
use Yii;
use common\models\BaseModel;

class Express extends BaseModel
{
	public static function tableName()
	{
		return '{{%express%}}';
	}


	//获取物流公司
	public static function getExpress($where)
	{
		$order = 'e_order asc';
		$data = self::find()->where($where)->orderBy($order)->asarray()->all();
		return $data;
	}


}