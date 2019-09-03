<?php
/*
	商品规格相关控制器
*/
namespace apiadmin\modules\v1\controllers\goods; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\goods\GoodsSpec;
use apiadmin\modules\models\goods\SpecValue;
use apiadmin\modules\models\goods\SpecGroup;
use apiadmin\modules\models\goods\TypeSpec;


class GoodsSpecController extends CoreController
{

	//获取所有规格
	public function actionList()
	{
		$list = GoodsSpec::specList();
		$this->out('规格列表',$list);
	}


	//添加 修改 规格
	public function actionEdit()
	{
		$goodsSpec = new GoodsSpec();
		$params = $this->request;
		if($id=$this->request('id'))
		{
			$goodsSpec = $goodsSpec::findOne($id);
		}

		if($goodsSpec->load($params,''))
		{	
			if($goodsSpec->validate() == true && $goodsSpec->save()){
				if(!$params['id']) $goodsSpec->id = Yii::$app->db->getLastInsertID();
				$this->out('操作成功',$goodsSpec->attributes);
			}
			$error = $goodsSpec->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);

		}else{
			$error = $goodsSpec->getErrors();
			$errMsg = $this->model_errors($errMsg);
			$this->error($errMsg);
		}
	}


	//删除规格
	//id
	public function actionDel()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');
		$ids = [$id];
		$where = ['in','id',$ids];
		$res = GoodsSpec::deleteAll($where);

		if($res) $this->out('删除成功');
		$this->error('删除失败');

	}	





	//获取规格值
	public function actionSpec_value_list()
	{
		if(!$specId = $this->request('spec_id'))
			$this->error('参数错误');

		$list = SpecValue::specValueList($specId);
		$this->out('规格值列表',$list);
	}


	//添加 修改 规格值
	public function actionEdit_spec_val()
	{
		$SpecValue = new SpecValue();
		$params = $this->request;
		if($id=$this->request('id'))
		{
			$SpecValue = $SpecValue::findOne($id);
		}
		
		if($SpecValue->load($params,''))
		{	
			if($SpecValue->validate() == true && $SpecValue->save()){
				if(!$params['id']) $SpecValue->id = Yii::$app->db->getLastInsertID();
				$this->out('操作成功',$SpecValue->attributes);
			}
			$error = $SpecValue->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);

		}else{
			$error = $SpecValue->getErrors();
			$errMsg = $this->model_errors($errMsg);
			$this->error($errMsg);
		}
	}	


	//删除规格值
	//id
	public function actionDel_spec_val()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');
		$ids = [$id];
		$where = ['in','id',$ids];
		$res = SpecValue::deleteAll($where);

		if($res) $this->out('删除成功');
		$this->error('删除失败');

	}



	//****************规格分组*****************

	//分组列表
	public function actionSpec_group_list()
	{
		$data = SpecGroup::specGroupList();
		$this->out('分组列表',$data);
	}


	//添加 修改 规格分组
	public function actionEdit_spec_group()
	{
		$SpecGroup = new SpecGroup();
		$params = $this->request;
		if($id=$this->request('id'))
		{
			$SpecGroup = $SpecGroup::findOne($id);
		}

		if($SpecGroup->load($params,''))
		{	
			if($SpecGroup->validate() == true && $SpecGroup->save()){
				if(!$params['id']) $SpecGroup->id = Yii::$app->db->getLastInsertID();
				$this->out('操作成功',$SpecGroup->attributes);
			}
			$error = $SpecGroup->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);

		}else{
			$error = $SpecGroup->getErrors();
			$errMsg = $this->model_errors($errMsg);
			$this->error($errMsg);
		}
	}

	//删除规格分组
	//id
	public function actionDel_spec_group()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');
		$ids = [$id];
		$where = ['in','id',$ids];
		$res = SpecGroup::deleteAll($where);

		if($res) $this->out('删除成功');
		$this->error('删除失败');

	}	


	//获取规格分组的 规格
	public function actionSpec_group_spec()
	{
		if(!$typeId = $this->request('type_id'))
			$this->error('参数错误');

		//此分组拥有的规格
		$ownerSpec = SpecGroup::getGroupSpec($typeId);

		//获取所有规格
		$speces = GoodsSpec::getAllSpec();

		$data = array('owner_spec'=>$ownerSpec,'speces'=>$speces);

		$this->out('规格分组显示规格',$data);
	}

	//修改规格分组 的规格
	public function actionEdit_group_spec()
	{
		if(!$typeId = $this->request('type_id'))
			$this->error('参数错误');

		try{
			//此分组拥有的规格
			$ownerSpec = SpecGroup::getGroupSpec($typeId);

			//选中的规格
			$checkedSpec = json_decode($this->request('checked_spec',[]),1);

			$transaction = Yii::$app->db->beginTransaction();

			//删除没选中的
			$where = ['and','type_id='.$typeId,['not in','spec_id',$checkedSpec]];
			$flag  = TypeSpec::deleteAll($where);

			//添加新增的
			foreach($checkedSpec as $val)
			{
				if(!in_array($val, $ownerSpec))
				{
					$typeSpec = new TypeSpec;
					$typeSpec->type_id = $typeId;
					$typeSpec->spec_id = $val;
					if(!$typeSpec->save()) throw new Exception('规格添加失败');
					
				}
			}

			$transaction->commit();
			$this->out('操作成功',$checkedSpec,$ownerSpec);
		}catch(\Exception $e){
			$err = $e->getMessage();
			$transaction->rollback();
			$this->error($err);
		}
	}


}