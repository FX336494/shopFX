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
	public function getGoods($where)
	{
		$field = "*";
		$data = (new \yii\db\Query()) 
				->select($field)  
	    		->from('cart')  
	    		->leftJoin('goods', 'cart.goods_id = goods.goods_id')
	    		->where($where)
	    		->all();
	    return $data;
	}

}