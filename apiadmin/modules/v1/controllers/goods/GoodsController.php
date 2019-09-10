<?php
/*
	产品分类相关控制器
*/
namespace apiadmin\modules\v1\controllers\goods; 
use \Yii;
use apiadmin\modules\v1\controllers\CoreController;
use apiadmin\modules\models\goods\GoodsCommon;
use apiadmin\modules\models\goods\Goods;
use apiadmin\modules\models\goods\GoodsImages;

class GoodsController extends CoreController
{
	/*
		商品列表
	*/
	public function actionGoods_list()
	{
		$where = [];
		$params = array(
			'field' => ['goods_commonid','goods_name','gc_id','goods_image','goods_storage','goods_state','create_time','goods_price'],
			'limit' => $this->request('page_size',10),
			'page'	=> $this->request('page',1),
		);
		$extends = array('category');
		$list  = GoodsCommon::goodsList($where,$params,$extends);
		$pages = GoodsCommon::$pages;

		$this->out('商品列表',$list,array('pages'=>$pages));
	}

	/*
		获取商品信息
		* goods_commonid 
	*/
	public function actionGet_goodscommon_detail()
	{
		$goodsCommonId = $this->request('goods_commonid');
		if(!$goodsCommonId) $this->error('参数错误');

		$where = ['goods_commonid'=>$goodsCommonId];
		$goodsCommon = GoodsCommon::getCommonGoods($where);
		$goodsCommon['spec_name'] = json_decode($goodsCommon['spec_name'],1);

		$goodsCommon['checked_spec_value'] = '';
		if($goodsCommon['spec_name']){
			$specName = $goodsCommon['spec_name'];
			$specValue = json_decode($goodsCommon['spec_value'],1);
			$specValueArr = Goods::formatSpecVal($goodsCommonId,$specName,$specValue);
			$goodsCommon['checked_spec_value'] =$specValueArr;
			$goodsCommon['checked_spec'] = $this->getCheckedSpec($specName);
		}
		
		$this->out('商品信息',$goodsCommon);
	}

	private function getCheckedSpec($specName)
	{
		$list = array();
		foreach($specName as $specId=>$name){
			$list[] = array('spec_id'=>$specId,'spec_name'=>$name);
		}
		return $list;
		return json_encode($list,JSON_UNESCAPED_UNICODE);
	}


	/*
		添加商品第一步 基本信息添加
	*/
	public function actionAdd_step1()
	{
		$params   = Yii::$app->request->post();

		//处理规格数据
		$specName = json_decode($params['spec_name'],1);
		$specName = $this->formatSpecName($specName);
		$params['spec_name'] = json_encode($specName,JSON_UNESCAPED_UNICODE);
		
		//处理规格值数据
		$specValue = json_decode($params['spec_value'],1);
		$formatSpecValue = $this->formatSpecValue($specValue);
		$params['spec_value'] = json_encode($formatSpecValue,JSON_UNESCAPED_UNICODE);

		$GoodsCommon = new GoodsCommon;
		$res = $GoodsCommon->addGoods($params,$specValue);
		
		if($res['state']) $this->out('添加成功',$res);
		$this->error($res['msg']);
	}

	//处理规格数据
	private function formatSpecName($specNameArr)
	{
		$specName = array();
		foreach($specNameArr as $val)
		{
			if(!$val) continue;
			$specName[$val['spec_id']] = $val['spec_name'];
		}
		return  $specName;
	}

	//处理规格值数据
	private function formatSpecValue($specValueArr)
	{
		$valData = array();
		$temp = array();
		if(!$specValueArr) return [];
		foreach($specValueArr as $k=>$specValue)
		{
			foreach($specValue as $val)
			{
				if(!$val['spec_id']) continue;
				$temp[$val['spec_id']][$val['val_id']] = $val['spec_val'];
			}
			$valData = $temp;
		}
		return $valData;
	}

	//获取商品详情
	public function actionGet_goods_body()
	{
		$goodsCommonId = $this->request('goods_commonid');
		if(!$goodsCommonId) $this->error('参数错误');

		$where = ['goods_commonid'=>$goodsCommonId];
		$goodsCommon = GoodsCommon::getCommonGoods($where,['goods_body']);
		$this->out('商品详情',$goodsCommon);		
	}

	/*
		添加商品第二步 详细信息添加
	*/
	public function actionAdd_step2()
	{
		$goodsCommonId = $this->request('goods_commonid');
		if(!$goodsCommonId) $this->error('参数错误');
		$where = ['goods_commonid'=>$goodsCommonId];

		$goodsCommon = new GoodsCommon;
		$goodsCommon = $goodsCommon::findOne($where);
		$goodsCommon->goods_body = $this->request('goods_body');
		$goodsCommon->update_time = time();

		if(!$goodsCommon->save(false)) $this->error('操作失败');

		$this->out('操作成功');
	}

	/*
		获取商品图片信息
	*/
	public function actionGet_goods_images()
	{
		$goodsCommonId = $this->request('goods_commonid');
		if(!$goodsCommonId) $this->error('参数错误');
		
		$where = ['goods_commonid'=>$goodsCommonId];
		$goodsCommon = GoodsCommon::getCommonGoods($where,['goods_image']);

		$images = GoodsImages::getColorGroup($goodsCommonId,$goodsCommon['goods_image']);
		$this->out('图片信息',$images);
	}
	/*
		添加商品第二步 图片添加
	*/
	public function actionSave_images()
	{
		if(!$goodsCommonId = $this->request('goods_commonid'))
			$this->error('参数错误');

		$data = $this->request('data',[]);
		$data = json_decode($data,1);	

		$imagesData = array();
		foreach($data as $val)
		{
			$imagesData[$val['color_id']] = $val['images'];
		}
		unset($data);


		$goodsImage = new GoodsImages;
		$res = $goodsImage->saveGoodsImages($imagesData,$goodsCommonId);

		if($res['state']) $this->out('操作成功');
		$this->error($res['msg']);

	}


	//删除商品
	public function actionDel_goods()
	{
		if(!$goodsCommonId = $this->request('goods_commonid'))
			$this->error('参数错误');

		$flag = GoodsCommon::delGoods($goodsCommonId);
		if($flag) $this->out('操作成功');
		$this->error('删除失败');
	}

}
