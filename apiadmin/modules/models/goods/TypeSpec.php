<?php
/*
	商品规格组的规格 模型
	就是 规格分组有哪些规格的  一个表
*/
namespace apiadmin\modules\models\goods;

class TypeSpec extends \yii\db\ActiveRecord  
{
	public static function tableName()
	{
		return "{{%type_spec}}";
	}

	public function rules()
	{
		return [
			[['type_id','spec_id'],'required'],
			[['spec_id','type_id'],'integer'],
		];
	}		

	//获取规格对应的值
	public static function specGroupList()
	{
		$data  = self::find()->asarray()->all();
		return $data;
	}


	//获取当前分组的规格
	public static function getGroupSpec($typeId)
	{
		$data = self::find()->select(['spec_id'])->where(['type_id'=>$typeId])->asarray()->all();
		return $data;
	}

	//获取当前分组的规格详情
	public static function getTypeSpecDetail($typeId)
	{
		$data  = (new \yii\db\Query())
				->select(["type_spec.type_id","type_spec.spec_id","goods_spec.*"]) 
				->from('type_spec')
				->leftJoin('goods_spec','type_spec.spec_id=goods_spec.id')
				->where(['type_spec.type_id'=>$typeId])
				->orderBy('goods_spec.sort ASC')
				->all();
		return $data;
	}


}