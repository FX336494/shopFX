<?php
/*
	* 商品模型 goods_common
*/
namespace api\modules\models\goods;
use Yii;
use common\models\goods\GoodsCommonModel;
use api\modules\models\goods\GoodsClass;
use api\modules\models\goods\GoodsImages;
use api\modules\models\goods\GoodsBrowse;
use api\modules\models\goods\Goods;

class GoodsCommon extends GoodsCommonModel
{

	/*
		商品详情
		*id goods_commonid
	*/
	public static function getGoodsDetail($goodsCommonid)
	{
		$data = self::find()->where(['goods_commonid'=>$goodsCommonid])->asarray()->one();
		$data['images'] = GoodsImages::getImages($goodsCommonid);
		return $data;
	}

	//通过ID获取产品
	public static function getGoodsCommonById($goodsCommonid,$field="*")
	{
		$where = ['goods_commonid'=>$goodsCommonid];
		return self::find()->select($field)->where($where)->asarray()->one();
	}


	//增加商品访问量
	public static function addGoodsViews($goodsCommonId,$memberId)
	{
		$obj = self::findOne($goodsCommonId);
		$obj->goods_click +=1;
		$flag = $obj->save();

		//添加浏览记录
		$data = array(
			'goods_commonid'	=> $goodsCommonId,
			'goods_image'		=> $obj->goods_image,
			'goods_name'		=> $obj->goods_name,
			'member_id'			=> $memberId,
			'gc_id'				=> $obj->gc_id,
		);
		return GoodsBrowse::add($data); 
	}

	/*
		*更新收藏量
		* type 1 收藏 0取消收藏
	*/
	public static function updateGoodsCollect($goodsCommonId,$type='1') 
	{
		$obj = self::findOne($goodsCommonId);
		if($type=='1'){
			$collect = $obj->goods_collect+1;
		}else
		{
			$collect = ($obj->goods_collect-1)<=0?0:$obj->goods_collect-1;
		}
		$obj->goods_collect = $collect;
		$flag = $obj->save();
		return $flag;		
	} 


	/*
		* 更新销量和库存
		* goodsId   商品id  goods中的id
		* nums  数量
		* type  类型 1增加 0减少
	*/
	public static function updateStorage($goodsId,$nums,$type='1')
	{
		$goods = Goods::getGoodsById($goodsId,'goods_commonid,goods_salenum,goods_storage');
		if(!$goods) return false;


		if($type=='1'){
			$updateData = array(
				'goods_salenum'	=> $goods['goods_salenum'] + $nums,
				'goods_storage'	=> max($goods['goods_storage'] - $nums,0)
			);			
		}else{
			$updateData = array(
				'goods_salenum'	=> max($goods['goods_salenum'] - $nums,0),
				'goods_storage'	=> $goods['goods_storage'] + $nums
			);				
		}
		if(!Goods::updateAll($updateData,['goods_id'=>$goodsId])) return false;
		
		$goodsCommonId = $goods['goods_commonid'];
		$data = self::getGoodsCommonById($goodsCommonId,'goods_salenum,goods_storage');
		if(!$data) return false;
		if($type=='1'){
			$updateData = array(
				'goods_salenum'	=> $data['goods_salenum'] + $nums,
				'goods_storage'	=> max($data['goods_storage'] - $nums,0)
			);	
		}else
		{
			$updateData = array(
				'goods_salenum'	=> max($data['goods_salenum'] - $nums,0),
				'goods_storage'	=> $data['goods_storage'] + $nums
			);				
		}
		if(!self::updateAll($updateData,['goods_commonid'=>$goodsCommonId])) return false;

		return true;
	}


}