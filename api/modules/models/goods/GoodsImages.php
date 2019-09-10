<?php

/*
	* 商品图片
*/
namespace api\modules\models\goods;
use Yii;
use common\models\goods\GoodsImagesModel;

class GoodsImages extends GoodsImagesModel
{
	/*
		通过产品ID获取图片
	*/
	public static function getImages($goodscCommonId)
	{
		return self::find()->select('image_url')
							->where(['goods_commonid'=>$goodscCommonId])
							->orderBy('sort ASC')
							->asArray()
							->all();
	}
}