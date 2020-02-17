<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;

/*
	拼团实例模型
*/
class PintuanGoodsModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%pintuan_goods}}';
	}

	public function rules()
	{
		return [
			[['instance_id','goods_commonid','goods_id','pintuan_price'],'required'],			
			[['instance_id','goods_commonid','goods_id'],'integer'],
			[['pintuan_price'],'number'],	
		];
	}

	/*
		拼团实例商品列表
	*/
	public static function instanceGoodsList($params)
	{
		$where = ['a.instance_id'=>$params['instance_id']];
		$field = ['a.*','b.goods_name','b.goods_image','b.goods_price'];
		$offset = ($params['page']-1)*$params['limit'];
		$model = self::find()->select($field)->alias('a')
							->leftJoin('goods_common as b','a.goods_commonid=b.goods_commonid')
							->where($where);
		self::$pages = $model->count();
		$data = $model->groupBy('a.goods_commonid')->offset($offset)->limit($params['limit'])->asarray()->all();
		return $data;	
	}

	/*
		获取拼团的产品
	*/
	public static function getPintuanGoods($goodsId)
	{
		return self::find()->where(['goods_id'=>$goodsId])->asarray()->one();
	}

	//获取一个默认拼团产品
	public static function getMinPintuanGoods($goodsCommonid)
	{
		return self::find()->where(['goods_commonid'=>$goodsCommonid])
				->orderBy('pintuan_price asc')
				->asarray()->one();
	}


	/*
		通过商品获取所属的拼团实例
	*/
	public static function getInstanceByGoods($goodsId)
	{
		$field = ["a.goods_id",'b.*'];
		$data = self::find()->select($field)->alias('a')
				->leftJoin('pintuan_instance as b','a.instance_id=b.instance_id')
				->where(['a.goods_id'=>$goodsId])
				->asarray()->one();
		return $data;
	}

}