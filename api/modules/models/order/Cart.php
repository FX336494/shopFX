<?php
namespace api\modules\models\order;
use Yii;
use api\modules\models\goods\Goods;
/*
	*购物车模型
*/

class Cart extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return '{{%cart%}}';
	}


	public static function getCartByGoodsid($goodsId,$memberId)
	{
		$where = array('goods_id'=>$goodsId,'buyer_id'=>$memberId);
		return self::find()->where($where)->asarray()->one();
	}

	//按店铺显示商品
	public static function getCartList($where)
	{
		$data  = self::find()->where($where)->asarray()->all();		
		return $data;
	}

	//获取购物车里的商品
	public static function getGoods($cartIds)
	{
		$where = ['in','cart_id',$cartIds];
		$field = ['b.*','a.*'];
		$data = self::find()->select($field)->alias('a')
				->leftJoin('goods as b','a.goods_id=b.goods_id')
				->where($where)
				->asarray()->all();
							
	    return $data;
	}

}