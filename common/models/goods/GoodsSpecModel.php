<?php
/*
	商品规格 模型
*/
namespace common\models\goods;
use common\models\BaseModel;

class GoodsSpecModel extends BaseModel
{
	public static function tableName()
	{
		return "{{%goods_spec}}";
	}

	public function rules()
	{
		return [
			[['spec_name'],'required'], 
			[['sort'],'integer'],
		];
	}		

	public static function specList()
	{
		return self::find()->asArray()->orderBy('sort asc')->all();
	} 


	public static function getAllSpec()
	{
		return self::find()->asArray()->orderBy('sort asc')->all();
	}


}