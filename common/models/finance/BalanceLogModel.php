<?php
/*
   帐户余额日志模型
*/
namespace common\models\finance;
use common\models\BaseModel;
use common\models\member\MemberModel;

class BalanceLogModel extends BaseModel
{
	public static function tableName()
	{
		return "{{%balance_log}}";
	}

	public function rules()
	{
		return [
			[['member_id','balance_type','balance','balance_id'],'required'],
			[['note','bonus_from'],'string'],
			[['balance','old_balance'],'double'],  
			[['member_id','create_time','balance_type','source_type','balance_id','log_type','source_id'],'integer'],
		];
	}


	/*
		*日志列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息 
	*/
	public static function balanceLogList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = isset($whereArr['where'])?$whereArr['where']:[];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();	

		//操作类型 //账本类型名
		$typeText = \Yii::$app->params['sourceTypeText'];

		
		foreach($data as &$val)
		{
			$val['type_text'] = $typeText[$val['source_type']];
			foreach($extends as $eType)
			{
				if($eType=='member')
				{
					$field = ['member_name','member_avatar','member_mobile'];
					$member = MemberModel::getMemberById($val['member_id'],$field);
					$val = array_merge($member,$val);
				}
			}
		}			
		return $data;
	}

}