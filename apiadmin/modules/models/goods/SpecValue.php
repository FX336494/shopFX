<?php
/*
	商品规格值 模型
*/
namespace apiadmin\modules\models\goods;

class SpecValue extends \yii\db\ActiveRecord  
{
	public static function tableName()
	{
		return "{{%goods_spec_value}}";
	}

	public function rules()
	{
		return [
			[['value_name','spec_id'],'required'],
			[['show_name','value_name'],'string'],
			[['category_id','sort'],'integer'],
		];
	}		

	//获取规格对应的值
	public static function specValueList($specId)
	{
		$where = ['spec_id'=>$specId];
		$data  = self::find()->where($where)->orderBy('sort asc')->asarray()->all();
		return $data;
	}


}