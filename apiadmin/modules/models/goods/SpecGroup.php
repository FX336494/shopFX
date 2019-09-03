<?php
/*
	商品规格分组 模型
*/
namespace apiadmin\modules\models\goods;
use apiadmin\modules\models\goods\TypeSpec;

class SpecGroup extends \yii\db\ActiveRecord  
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

	//获取规格对应的值
	public static function specGroupList()
	{
		$data  = self::find()->asarray()->all();
		return $data;
	}


	//获取当前分组所对应的 规格id
	public static function getGroupSpec($typeId)
	{
		$data = TypeSpec::getGroupSpec($typeId);
		$list = array();
		if(!$data) return $list;
		foreach($data as $val)
		{
			$list[] = $val['spec_id'];
		}
		return $list;
	}


}