<?php
/*
	商品共用信息 模型
*/
namespace common\models\goods;
use Yii;
use common\models\BaseModel;
use common\models\goods\GoodsClassModel; 

class GoodsCommonModel extends BaseModel
{
	public static $pages;

	public static function tableName()
	{
		return "{{%goods_common}}";
	}

	public function rules()
	{
		return [
			[['goods_name','gc_id','gc_id1','goods_image','goods_storage','goods_price'],'required'],
			[['goods_name','goods_image','spec_name','spec_value','goods_body','mobile_body'],'string'],
			[['type_id','gc_id','gc_id1','gc_id2','gc_id3','goods_storage','goods_state','create_time','update_time','goods_commend','promotion_type'],'integer'],
			[['goods_price','goods_marketprice','goods_costprice','goods_freight','goods_storage_alarm'],'number'],
		];
	}	

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_commonid' => '主键',
            'goods_name' => '商品名称',
            'gc_id1' => '一级分类',
            'gc_id' => '分类ID',
            'goods_image' => '商品主图',
            'goods_price' => '商品价格',
            'goods_storage' => '商品库存',
            'promotion_type' => '商品类型',
        ];
    }


	/*
		* 商品列表 （通用）
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些与产品相关的信息
		* 
	*/
	public static function goodsList($whereArr,$params=array(),$extend=array())
	{
		$where = isset($whereArr['where'])?$whereArr['where']:[];
		$and = isset($whereArr['and'])?$whereArr['and']:[];

		$model  = self::find();
		$models = self::queryFormart($model,$where,$params,$and);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();

		//商品扩展信息
		if(!$extend) return $data;

		foreach($data as &$val)
		{
			foreach($extend as $eType)
			{
				//获取分类
				if($eType=='category'){
					$cate = GoodsClassModel::getCateById($val['gc_id']);
					$val['category_name'] = $cate?$cate['category_name']:'无';
				}
			}
		}

		return $data;
	}

	/*
		获取商品
	*/
	public static function getCommonGoods($where,$field=['*']) 
	{
		$data = self::find()->select($field)->where($where)->asarray()->one();
		return $data;
	}










}