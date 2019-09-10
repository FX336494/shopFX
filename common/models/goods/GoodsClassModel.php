<?php
/*
	商品分类管理
*/
namespace common\models\goods;
use common\models\BaseModel;

class GoodsClassModel extends BaseModel
{
   	public static function tableName()
   	{
   		return "{{%goods_category}}";
   	}

   	public function rules()
   	{
   		return [
   			[['category_name'],'required'],
   			[['category_name','desc','index_menu','img'],'string'],
   			[['type_id','parent_id'],'integer'],
   		];
   	}	

   	//获取分类树结构
   	public static function getClassTree($pid='0')
   	{
      		$cates = self::find()->asArray()->all();
      		$data  = self::cateTree($cates,$pid);
      		return $data;
   	}

   	//格式化分类
   	protected static function  cateTree($cates,$parentId=0,&$list=array(),$level=0)
   	{
   		foreach($cates as $cate)
   		{
   			if($cate['parent_id']==$parentId)
   			{
   				$cate['level'] = $level;
   				$list[] = $cate;
   				self::cateTree($cates,$cate['id'],$list,$level+1); 
   			}
   		}
   		return $list;
   	}

   	//获取单个分类
   	public static function getCateById($cateId)
   	{
   		return self::findOne(['id'=>$cateId]);
   	}

      //获取分类
      public static function getCateList($where,$field=["*"])
      {
         $data = self::find()->select($field)->where($where)->asarray()->all();
         return $data;
      }

}