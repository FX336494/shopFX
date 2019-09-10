<?php

/*
	* 地址相关控制器
*/
namespace api\modules\v1\controllers\order;
use api\modules\v1\controllers\CoreController;
use Yii;
use api\modules\models\order\Address;

class AddressController extends CoreController
{
	
	//获取地区列表
	public function actionArea_list()
	{
		$parentId = $this->request['parent_id']?$this->request['parent_id']:0;
		$deep 	  = $this->request['deep']?$this->request['deep']:1;
		$list     = Address::areaList($parentId,$deep);
		$this->out('地区列表',$list);
	}


	//地址添加
	public function actionAdd()
	{
		if($this->request['is_default'])
		{
			$data = array('is_default'=>'0');
			$where = ['is_default'=>'1','member_id'=>$this->_memberId];
			Address::updateAddress($data,$where);
		}

		$model = $this->model('order\Address',$this->request,'Reg');
		$model->member_id = $this->_memberId;
		if(!$model->save()){
			$this->error('添加失败');
		}
		$this->out('添加成功');
	}

	//地址修改
	public function actionEdit()
	{
		if($this->request['is_default'])
		{
			$data = array('is_default'=>'0');
			$where = ['is_default'=>'1','member_id'=>$this->_memberId];
			$flag = Address::updateAddress($data,$where);
		}

		$addressId = $this->request['address_id']?$this->request['address_id']:$this->error('参数错误');
		$model = new Address();
		$model = $model::findOne($addressId);
		$model->scenario = 'Edit';
		$model = $this->parameter($this->request,$model);
		if($model->save()) $this->out('修改成功');
		$this->error('修改失败');
	}

	//删除
	public function actionDelete()
	{
		$addressId = $this->request['address_id']?$this->request['address_id']:$this->error('参数错误');
		$model = new Address();
		$flag = $model::deleteAll(['address_id'=>$addressId]);
		if($flag) $this->out('删除成功');
		$this->error('删除失败');		
	}


	//收货地址列表
	public function actionAddress_list()
	{
		$data = Address::addressList($this->_memberId);
		$this->out('收货地址列表',$data);
	}

	//获取单个收货地址
	// address_id 
	public function actionAddress_info()
	{
		$addressId = $this->request['address_id']?$this->request['address_id']:$this->error('参数错误');
		$data = Address::find()->where(['address_id'=>$addressId])->asArray()->one();
		$this->out('地址信息',$data);
	}

}