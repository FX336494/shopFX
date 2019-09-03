<?php
/*
	商品SKU 模型
*/
namespace common\models\goods;
use Yii;
use common\models\BaseModel;

class GoodsModel extends BaseModel
{

	public static function tableName()
	{
		return "{{%goods}}";
	}

	public function rules()
	{
		return [
			[['goods_id','goods_commonid','goods_name','gc_id','gc_id1','goods_image','goods_storage','goods_price'],'required'],
			[['goods_name','goods_image','spec_name','goods_spec','goods_body','mobile_body'],'string'],
			[['gc_id','gc_id1','gc_id2','gc_id3','goods_storage','goods_state','create_time','color_id','goods_commend'],'integer'],
			[['goods_price','goods_marketprice','goods_costprice','goods_freight','goods_storage_alarm'],'number'],
		];
	}	

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_id' => '主键',
            'goods_name' => '商品名称',
            'gc_id1' => '一级分类',
            'gc_id' => '分类ID',
            'goods_image' => '商品主图',
            'goods_price' => '商品价格',
            'goods_storage' => '商品库存',
        ];
    }

    //获取单个商品
    public static function getGoods($where,$field=['*'],$order=['goods_id'=> SORT_DESC])
    {
    	$data = self::find()->select($field)->where($where)->asarray()->orderBy($order)->one();
    	return $data;

    }

}