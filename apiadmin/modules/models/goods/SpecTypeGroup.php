<?php
/*
	商品规格分组 模型
*/
namespace apiadmin\modules\models\goods;

class SpecTypeGroup extends \yii\db\ActiveRecord  
{
	public static function tableName()
	{
		return "{{%spec_type_group}}";
	}

	public function rules()
	{
		return [
			[['type_name'],'required'],
			[['type_name'],'string'],
		];
	}		

	//获取所有规格分组
	public static function getAllGroup()
	{
		return self::find()->asArray()->all();
	}

	//格式化规格  id=>name
	public static function getFormatGroup()
	{
		$data = self::getAllGroup();
		$list = array('0'=>'无');
		foreach($data as $val)
		{
			$list[$val['id']] = $val['type_name'];
		}
		return $list;
	} 


}