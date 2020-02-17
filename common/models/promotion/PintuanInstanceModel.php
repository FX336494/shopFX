<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;
use common\models\promotion\PintuanGoodsModel;

/*
	拼团实例模型
*/
class PintuanInstanceModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%pintuan_instance}}';
	}

	public function rules()
	{
		return [
			[['title','image','end_goods_status','state','is_end'],'string'],
			[['start_time','end_time','trailer_time','min_nums','update_time','time_limit'],'integer'],
			[['title','start_time','end_time','trailer_time','time_limit'],'required'],
		];
	}

	/*
		* 列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function getList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['and'])?$whereArr['and']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();
		if(!$extends) return $data;


		//扩展信息
		$curTime = time();
		foreach($data as &$val)
		{
			foreach($extends as $type)
			{
				if($type=='goods')
				{
					$field = ['a.*','b.goods_name','b.goods_image','b.goods_price'];
					$goods = PintuanGoodsModel::find()
							->select($field)->alias('a')
							->leftJoin('goods_common as b','a.goods_commonid=b.goods_commonid')
							->where(['a.instance_id'=>$val['instance_id']])
							->limit($params['goods_limit'])
							->groupBy('a.goods_commonid')
							->asarray()->all();
					$val['goods'] = $goods;
				}
			}
			$val['state_text'] = $val['start_time']<$curTime?'未开始':'进行中';
			//未开始就是 开始倒计时   进行中就是结束倒计时
			$val['countdown'] = $val['start_time']<$curTime?$curTime - $val['start_time']:$val['end'] - $curTime;
		}
		return $data;				
	}	

	/*
		* 获取实例
		* instanceId
	*/
	public static function getInstance($instanceId)
	{
		return self::find()->where(['instance_id'=>$instanceId])->asarray()->one();
	}

}