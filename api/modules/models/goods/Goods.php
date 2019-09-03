<?php
/*
	* 商品模型 goods
*/
namespace api\modules\models\goods;
use Yii;
use api\modules\models\goods\GoodsImages;

class Goods extends \yii\db\ActiveRecord
{

	static $whereArr = array('goods_state'=>'1');
	static $whereStr = ' and goods_state="1"';

	public static function tableName()
	{
		return '{{%goods}}';
	}



	//通过goods_commonid获取一个默认的产品
	public static function getDefaultGoods($goodsCommonId)
	{
		$where = ['goods_commonid'=>$goodsCommonId];
		$where = array_merge($where,self::$whereArr);
		$data  = self::find()->where($where)->asarray()->one();
		return $data;
	}

	//通过ID获取产品
	public static function getGoodsById($id,$field="*")
	{
		$where = ['goods_id'=>$id];
		$where = array_merge($where,self::$whereArr);
		return self::find()->select($field)->where($where)->asarray()->one();
	}

	public static function getGoods($where,$field="*")
	{
		$where = array_merge($where,self::$whereArr);
		return self::find()->select($field)->where($where)->asarray()->one();
	}	
}