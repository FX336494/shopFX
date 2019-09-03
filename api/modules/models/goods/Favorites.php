<?php
namespace api\modules\models\goods;
use Yii;
use api\modules\models\goods\GoodsCommon;
use common\models\DB;

/**
* 收藏模型  （商品，店铺）
*/
class Favorites extends yii\db\ActiveRecord
{

	public static function tableName()
	{
		return '{{%favorites}}';
	}

	static $pageSize = 15; //每页面显示数量 

	/*
		新增收藏
		* type 收藏类型 1商品 2店铺
		* goods_commonid   对应收藏的id
		* member_id  收藏人
	*/
	public static function addFav($params)
	{
		$data = array(
			'member_id'		=> $params['member_id'],
			'member_name' 	=> $params['member_name'],
			'goods_commonid'=> $params['goods_commonid'],
			'create_time'	=> time(),
		);

		//是否已经收藏
		$favLog = self::isFovarites($params['goods_commonid'],$params['member_id']);
		$where  = ['goods_commonid'=>$params['goods_commonid'],'member_id'=>$params['member_id']];

		//已经收藏了
		if($favLog){
			// var_dump($where);
			$flag = self::deleteAll($where);
			$type = '0';
		}else{
			$goods = GoodsCommon::getGoodsCommonById($params['goods_commonid'],"goods_name,goods_image,goods_price");
			if(!$goods) return array('status'=>false,'msg'=>'收藏的商品不存在'.$params['id']);
			$data = array_merge($data,$goods);
			$flag = DB::insert('favorites',$data);	
			$type = '1';			
		}
		//更新收藏量
		GoodsCommon::updateGoodsCollect($params['goods_commonid'],$type);

		$msg = $favLog?'取消':'收藏';
		return array('status'=>$flag,'msg'=>$msg); 
	}

	/*
		用户收藏状态
		* id
		* memberId
		* type
	*/

	public static function isFovarites($id,$memberId)
	{
		$data = self::find()->where(['goods_commonid'=>$id,'member_id'=>$memberId])->asArray()->one();
		if($data) return 1;
		return 0;
	}


	/*
		收藏列表
		* page  0获取所有 
	*/
	public static function fovaritesList($memberId,$page='0')
	{
		$offset = $page>0?($page-1)*self::$pageSize:'';
		$limit  = $page>0?self::$pageSize:0;
		$data =  self::find()
				->where(['member_id'=>$memberId])
				->offset($offset)
				->limit($limit)
				->asArray()
				->all();
		return $data;
	}

}