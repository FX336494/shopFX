<?php
/*
	帐本模型
*/
namespace apiadmin\modules\models\finance;
use common\models\finance\BalanceModel;
use apiadmin\modules\models\member\Member;

class Balance extends BalanceModel
{

	/*
		*会员帐本列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息 
	*/
	public static function balanceList($whereArr,$params,$extends=[])
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();

		//账本类型名
		$typeName = self::balanceTypeName();
		foreach($data as &$val)
		{
			$val['type_name'] = $typeName[$val['balance_type']];
			foreach($extends as $eType)
			{
				if($eType=='member')
				{
					$field = ['member_name','member_avatar','member_mobile'];
					$member = Member::getMemberById($val['member_id'],$field);
					$val = array_merge($member,$val);
				}
			}
		}
		return $data;		
	}	

}