<?php
/*
	商品共用信息 模型
*/
namespace apiadmin\modules\models\goods;
use Yii;
use common\models\goods\GoodsImagesModel;

use apiadmin\modules\models\goods\Goods;

class GoodsImages extends GoodsImagesModel
{

    //按颜色分组图片
    public static function getColorImages($goodsCommonId,$colorId)
    {
    	$where = ['goods_commonid'=>$goodsCommonId,'color_id'=>$colorId];
    	$data = self::find()->where($where)->asarray()->orderBy('sort')->all();
    	return $data;
    } 

   //按获取颜色规格分组 获取图片
   public static function getColorGroup($goodsCommonId,$defaultImg)
   {
		$data = Goods::find()
				->select(['goods_spec','color_id'])
				->where(['goods_commonid'=>$goodsCommonId])
				->groupBy(['color_id'])
				->asarray()
				->all();
				
		$list = array();
		if(count($data)<2 && (!$data[0]['goods_spec'] || !$data[0]['color_id']))
		{
			$list = array('color_id'=>0,'name'=>'默认','images'=>array(array('image_url'=>$defaultImg)));
			return array($list);
		}  

		foreach($data as $k=>$val)
		{
			$list[$k]['color_id'] = $val['color_id'];
			$list[$k]['name']	  = json_decode($val['goods_spec'],1)[$val['color_id']];
         	$images = self::getColorImages($goodsCommonId,$val['color_id']);
         	$list[$k]['images'] = count($images)>0?$images:array(array('image_url'=>$defaultImg));
		}
		return $list;
   }    

    /*
		保存商品规格图片
		imagesData  图片信息
		goodsCommonId  商品公共id
    */
    public function saveGoodsImages($imagesData,$goodsCommonId)
    {
    	$transaction = Yii::$app->db->beginTransaction();
    	try{

	    	if(!$imagesData) {
	    		//删除图片
	    		if(!$this->deleteAll(['goods_commonid'=>$goodsCommonId]))
	    			throw new \Exception("删除失败");
	    	}
	    	$ids = [];
	    	foreach($imagesData as $colorId=>$images)
	    	{
				foreach($images as $k=>$image)
				{
					//默认第一张为规格产品主图
					if($k==0){
						$condition = ['goods_commonid'=>$goodsCommonId,'color_id'=>$colorId];					
						Goods::updateAll(array('goods_image'=>$image['image_url']),$condition);   
					}

					$imageModel = clone $this;
					$imageModel->color_id = $colorId;
					$imageModel->goods_commonid = $goodsCommonId;					
					if(!isset($image['id']))
					{
						//新增
						$imageModel->isNewRecord = true; 
						$imageModel->id = 0;	
						$imageModel->image_url = $image['image_url'];
						$imageModel->sort = isset($image['sort'])?$image['sort']:0;

					}else
					{
						//更新
						$imageModel->isNewRecord = false; 
						$imageModel->id = $image['id'];			
						$imageModel->image_url = $image['image_url'];	
						$imageModel->sort = isset($image['sort'])?$image['sort']:0;		
					}

					if(!$imageModel->validate() || !$imageModel->save()){
						$errors = $this->getErrors();
						$error =  self::outError($errors);
						throw new \Exception($error['msg']);
					}else{
						if($imageModel->id)
							$ids[] = $imageModel->id;
						else
							$ids[] = $goodsId = Yii::$app->db->getLastInsertID();
					}
				}    		
	    	}   
	    	//删除图片
			$where = ['and',['goods_commonid'=>$goodsCommonId],['not in', 'id',$ids]];
			$this->deleteAll($where);  
			
	    	$transaction->commit();
	    	return array('state'=>true,'msg'=>'ok');		
    	}catch(\Exception $e){
    		$transaction->rollback();
    		$errMsg = $e->getMessage();
    		return array('state'=>false,'msg'=>$errMsg);
    	}

    }


}