<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;

/*
	优惠券实例模型
*/
class CouponsInstanceModel extends BaseModel
{

	public static $typeText = array(
			'1'	=> '通用券',
			'2' => '分类下使用',
			'3' => '单品使用'
		);

	public static function tableName()
	{
		return '{{%coupons_instance}}';
	}

	public function rules()
	{
		return [
			[['coupons_desc','coupons_name','get_type','state','coupons_image','type','time_type'],'string'],
			[['update_time','total_nums','limit_nums','type_id','create_time','giveout_nums','useful_day'],'integer'],
			[['use_type','start_time','end_time'],'safe'],
			[['coupons_money','consume_amount','discount'],'number'],
			[['coupons_name'],'required'],
		];
	}

	/*
		* 数据列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function getList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();
		if(!$extends) return $data;
		$stateText = \Yii::$app->params['pintuanState'];
		foreach($data as &$val)
		{

		}
		return $data;				
	}


	public static function getInstance($instanceId)
	{
		return self::find()->where(['instance_id'=>$instanceId])->asarray()->one();
	}

}