<?php
/*
	后台帐本操作模型
*/
namespace apiadmin\modules\models\finance;
use common\models\BaseModel;

class BalanceOperate extends BaseModel
{

	public static function tableName()
	{
		return "{{%balance_operate}}";
	}

	public function rules() 
	{
		return [
			[['member_id','balance_type','nums'],'required'],  
			[['member_name','type','operator'],'string'],
			[['nums'],'double'],
			[['member_id','create_time','update_time'],'integer'],
		];
	}

	/*
		*列表
		* where 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息 
	*/
	public static function opList($where,$params,$extends=array())
	{
		$model  = self::find();
		$models = self::queryFormart($model,$where,$params);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();		
		return $data;
	}	


}