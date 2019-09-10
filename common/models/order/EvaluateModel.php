<?php
namespace common\models\order;
use Yii;
use common\models\BaseModel;

/*
	商品评价基本模型
*/

class EvaluateModel extends BaseModel
{
	public static function tableName()
	{
		return "{{%evaluate_goods%}}";
	}	

	/*
		* 评价列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function getGoodsEvaluteList($whereArr,$params,$extends=array())
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
				//此处可加扩展信息
			}
		}

		return $data;				
	}	
}