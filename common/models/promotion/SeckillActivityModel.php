<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;

/*
	拼团活动模型
*/
class SeckillActivityModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%seckill_activity}}';
	}

	public function rules()
	{
		return [
			[['intro','title','seckill_image','state'],'string'],
			[['update_time','id'],'integer'],
			[['title'],'required'],
		];
	}

	//获取拼团模块
	public static function getActivity($where=[])
	{
		return self::find()->where($where)->asarray()->one();
	}

}