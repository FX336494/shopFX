<?php
/*
	产品分类模型
*/
namespace api\modules\models\goods;
use Yii;

class Category extends \yii\db\ActiveRecord
{
	 public static function tableName()
	 {
		  return '{{%goods_category}}';
	 }

	  //所有分类
   	public static function allGoodsCates($parentId=0)
   	{
   		 $cates = self::find()->asArray()->all();
   		 $data  = self::cateTree($cates,$parentId);
   		 return $data;
   	}

   	//格式化分类
   	public static function  cateTree($cates,$parentId=0,&$list=array(),$level=0)
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

    //获取一级子分类
    public static function getChildreCategory($pid=0)
    {
        $cates = self::find()->where(['parent_id'=>$pid])->asArray()->all();
        return $cates;
    }


      //获取父分类
      public static function getParentCategory($cateId,&$list=array())
      {
          $cate = self::find()->where(['id'=>$cateId])->asArray()->one();
          if($cate)
          {
            $list[] = $cate;
            self::getParentCategory($cate['parent_id'],$list);
          }
          return $list;
      }

      //按条件获取分类
      public static function getCategroy($where,$select=['*'])
      {
          $cates = self::find()->select($select)->where($where)->asArray()->all();
          return $cates;        
      }
      

}
