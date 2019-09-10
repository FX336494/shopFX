<?php
/*
	会员相关
*/
namespace apiadmin\modules\models\member;
use Yii;
use common\models\member\MemberModel;

class Member extends MemberModel
{

	/*
		会员列表显示
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息		
	*/
	public static function MemberList($whereArr,$params,$extends=array())
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

		return $data;
	}

	/*
		删除会员
		member_id
	*/
	public static function MemberDel($memberId)
	{
		$where = ['member_id'=>$memberId];
		$res   = self::deleteAll($where);
		return $res;
	}

}