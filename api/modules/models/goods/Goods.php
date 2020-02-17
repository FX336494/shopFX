<?php
/*
	* 商品模型 goods
*/
namespace api\modules\models\goods;
use Yii;
use api\modules\models\goods\GoodsImages;
use api\modules\models\promotion\PintuanGoods;
use api\modules\models\promotion\SeckillGoods;

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

	/*
		* 获取产品的促销价格
	*/
	public static function getGoodsPromotionPrice($goods)
	{
		if($goods['promotion_type']=='1')
		{
			//拼团产品
			$pintuanGoods = PintuanGoods::getPintuanGoods($goods['goods_id']);
			if($pintuanGoods) return $pintuanGoods['pintuan_price'];
			
		}else if($goods['promotion_type']=='2')
		{
			//秒杀产品
			$seckillGoods = SeckillGoods::getSeckillGoods($goods['goods_id']);
			if($seckillGoods) return $seckillGoods['seckill_price'];
		}	
		return $goods['goods_price'];	
	}
}