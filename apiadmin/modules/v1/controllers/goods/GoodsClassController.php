<?php
/*
	产品分类相关控制器
*/
namespace apiadmin\modules\v1\controllers\goods; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\goods\GoodsClass;
use apiadmin\modules\models\goods\SpecTypeGroup;
use apiadmin\modules\models\goods\TypeSpec;
use apiadmin\modules\models\goods\SpecValue;
use apiadmin\modules\models\goods\GoodsCommon;

class GoodsClassController extends CoreController
{

	//获取所有分类
	public function actionClass_list()
	{
		$list = GoodsClass::getClassTree();

		//获取规格分组
		$specGroup = SpecTypeGroup::getFormatGroup();

		$extend = array('spec_group'=>$specGroup);
		$this->out('分类列表',$list,$extend);
	}


	//添加 修改分类
	public function actionEdit_class()
	{
		$goodsClass = new GoodsClass();
		$params = $this->request;
		if($id=$this->request('id'))
		{
			$goodsClass = $goodsClass::findOne($id);
		}

		if($goodsClass->load($params,''))
		{	
			if($goodsClass->validate() == true && $goodsClass->save()){
				if(!$params['id']) $goodsClass->id = Yii::$app->db->getLastInsertID();
				$this->out('操作成功',$goodsClass->attributes);
			}
			$error = $goodsClass->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);

		}else{
			$error = $goodsClass->getErrors();
			$errMsg = $this->model_errors($errMsg);
			$this->error($errMsg);
		}
	}


	//删除分类
	//id
	public function actionDel_class()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');

		$delClass = GoodsClass::getClassTree($id);
		$ids = [$id];
		if($delClass)
		{
			foreach($delClass as $class)
			{
				$ids[] = $class['id'];  
			}			
		}
		$where = ['in','id',$ids];
		$res = GoodsClass::deleteAll($where);

		if($res) $this->out('删除成功');
		$this->error('删除失败');

	}	


	/*
		* 获取分类
		* parent_id 父级分类
	*/
	public function actionGet_class()
	{
		$pid = $this->request('parent_id',0);
		$where = array('parent_id'=>$pid);
		$list = GoodsClass::getCateList($where,['category_name','id','type_id','parent_id']);
		$this->out('分类信息',$list);
	}

	/*
	 	* 获取分类对应的规格 和 规格值
	 	* gc_id 分类id 
	*/
	 public function actionGet_class_spec()
	 {
	 	if(!$gcId = $this->request('gc_id'))
	 		$this->error('参数错误');
	 	$goodsCommonId = $this->request('goods_commonid',0);

	 	$class = GoodsClass::getCateById($gcId);
	 	if(!$class) $this->error('分类不存在');
	 	if($class['type_id']<=0) $this->out('没有规格',[]);

	 	//通过分组类型 获取对应规格
	 	$specNames = TypeSpec::getTypeSpecDetail($class['type_id']);

	 	$goodsSpecName = $goodsSpecVal = [];
	 	if($goodsCommonId){
	 		$where = ['goods_commonid'=>$goodsCommonId];
	 		$goods = GoodsCommon::getCommonGoods($where,['spec_name','spec_value']);
	 		$goodsSpecName = json_decode($goods['spec_name'],1);
	 		$goodsSpecVal  = json_decode($goods['spec_value'],1);
	 	}


	 	//获取每个规格的值
	 	foreach($specNames as &$val)
	 	{
	 		if(isset($goodsSpecName[$val['spec_id']]) && $goodsSpecName[$val['spec_id']])
	 			$val['checked'] = true;
	 		$specValue = SpecValue::specValueList($val['spec_id']);
	 		if($specValue)
	 		{
		 		foreach($specValue as &$v)
		 		{
		 			$v['checked'] = isset($goodsSpecVal[$val['spec_id']])&&$goodsSpecVal[$val['spec_id']][$v['id']]?true:false;
		 		}	 			
	 		}

	 		$val['spec_value'] = $specValue;
	 	}

	 	$this->out('分组规格信息',$specNames,array('type_id'=>$class['type_id']));

	 }



}