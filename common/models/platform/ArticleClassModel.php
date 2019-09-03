<?php
namespace common\models\platform;
use Yii;
use common\models\BaseModel;

/*
	文章分类模型
*/
class ArticleClassModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%article_class%}}';
	}

	public function rules()
	{
		return [
			[['class_name','img'],'string'],
			[['update_time','id'],'integer'],
			[['class_name'],'required'],
		];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        	'id' => '主键ID',
            'class_name' => '分类名称',
            'img' => '分类图',
            'update_time' => '更新时间',
        ];
    }	

	public static function articleClassList($where=[])
	{
		return self::find()->where($where)->asarray()->all();
	}

	public static function getClass($where)
	{
		return self::find()->where($where)->asarray()->one();
	}
}