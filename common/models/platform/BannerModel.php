<?php
namespace common\models\platform;
use Yii;
use common\models\BaseModel;

/*
	轮播图
*/
class BannerModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%banner%}}';
	}

	public function rules()
	{
		return [
			[['img_url','type','link'],'string'],
		];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        	'id' => '主键ID',
            'img_url' => '图片地址',
            'link' => '跳转地址',
            'type' => '类型',
        ];
    }	

	public static function bannerList($where=[])
	{
		return self::find()->where($where)->asarray()->all();
	}
}