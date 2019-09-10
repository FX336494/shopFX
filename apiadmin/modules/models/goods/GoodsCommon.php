<?php
/*
	商品共用信息 模型
*/
namespace apiadmin\modules\models\goods;
use Yii;
use common\models\goods\GoodsCommonModel;
use apiadmin\modules\models\goods\GoodsClass;
use apiadmin\modules\models\goods\Goods;
use apiadmin\modules\models\goods\GoodsImages;

class GoodsCommon extends GoodsCommonModel
{

	/*
		添加商品
	*/
	public function addGoods($data,$specValueArr)
	{
		
		$transaction = Yii::$app->db->beginTransaction();
		try
		{
			//添加商品公共表信息
			$res = $this->addCommonGoods($data);
			if(!$res['state'])
				throw new \Exception($res['msg']);
			$goodsCommonid = $res['goods_commonid'];

			//添加SKU商品信息
			$data['goods_commonid'] = $goodsCommonid;
			$goodsModel = new Goods;
			$res = $goodsModel->saveGoods($data,$specValueArr);
			if(!$res['state'])
				throw new \Exception($res['msg']);
			$transaction->commit();

			return array('state'=>'true','goods_commonid'=>$goodsCommonid);
		}catch(\Exception $e){
			$transaction->rollBack();
			$errMsg = $e->getMessage();
			return array('state'=>false,'msg'=>$errMsg);
		}

			
	}


	private function addCommonGoods($data)
	{
		if($data['goods_commonid']){
			$this->goods_commonid = $data['goods_commonid']; 
			$this->isNewRecord = false;
		}else{
			$data['create_time'] = time();
		}		
		

		if($this->load($data,'') && $this->validate() && $this->save(false))
		{	
			if(!$data['goods_commonid'])
				$this->goods_commonid = Yii::$app->db->getLastInsertID();

			return array('state'=>true,'goods_commonid'=>$this->goods_commonid);

		}
		$errors = $this->getErrors();
		return self::outError($errors);		
	}


	//删除商品
	public static function delGoods($goodsCommonid)
	{
		$transaction = Yii::$app->db->beginTransaction();
		try{
			$where = ['goods_commonid'=>$goodsCommonid];

			if(!self::deleteAll($where))
				throw new \Exception('删除失败1');
			if(!Goods::deleteAll($where))
				throw new \Exception('删除失败2');
			
			GoodsImages::deleteAll($where);

			$transaction->commit();
			return true;
		}catch(\Exception $e){
			$transaction->rollback();
			var_dump($e->getMessage());
			return false;
		}
	}


}