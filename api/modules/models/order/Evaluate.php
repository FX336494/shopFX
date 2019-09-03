<?php
namespace api\modules\models\order;
use common\models\order\EvaluateModel;
use Yii;
/*
	评价模型
*/
class Evaluate extends EvaluateModel
{


	//获取商品评价数量
	public static function goodsEvaluateCount($goodsCommonId)
	{
		$data = self::find()->where(['goods_commonid'=>$goodsCommonId])->count();
		return $data;
	}


}