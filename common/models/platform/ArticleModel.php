<?php
namespace common\models\platform;
use Yii;
use common\models\BaseModel;
use common\models\platform\ArticleClassModel;
/*
	文章模型
*/
class ArticleModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%article%}}';
	}

	public function rules()
	{
		return [
			[['title','desc','logo','content','state'],'string'],
			[['create_time','id','class_id','update_time'],'integer'],
			[['title','class_id','content'],'required'],
		];
	}

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
        	'id' => '主键ID',
            'title' => '标题',
            'desc' => '描述',
            'content' => '内容',
            'logo' => 'logo图',
            'class_id' => '分类ID',
            'state' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }	

	/*
		* 数据列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function articleList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();

		//扩展信息
		if(!$extends) return $data;
		foreach($data as &$val)
		{
			foreach($extends as $eType)
			{
				if($eType=='class')
				{
					$class = ArticleClassModel::getClass(['id'=>$val['class_id']]);
					$val['class_name'] = $class['class_name'];
				}
			}
		}
		return $data;				
	}

	//获取文章
	public static function getArticleById($id)
	{
		return self::find()->where(['id'=>$id])->asarray()->one();
	}		

}