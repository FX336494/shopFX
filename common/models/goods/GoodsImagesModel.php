<?php
/*
	商品共用信息 模型
*/
namespace common\models\goods;
use Yii;
use common\models\BaseModel;

class GoodsImagesModel extends BaseModel
{
	public static $pages;

	public static function tableName()
	{
		return "{{%goods_images}}";
	}

	public function rules()
	{
		return [
			[['goods_commonid','color_id','image_url'],'required'],
			[['image_url'],'string'],
			[['goods_commonid','color_id','sort','is_default'],'integer'],
		];
	}	

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'goods_commonid' => '商品ID',
            'image_url' => '图片地址',
            'is_default' => '是否默认',
            'sort' => '排序',
        ];
    }



    

}