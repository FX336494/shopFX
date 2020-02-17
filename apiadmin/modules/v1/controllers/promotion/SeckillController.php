<?php
namespace apiadmin\modules\v1\controllers\promotion; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use common\models\promotion\SeckillActivityModel as SeckillActivity;
use apiadmin\modules\models\promotion\SeckillInstance;
use apiadmin\modules\models\promotion\SeckillGoods;
use apiadmin\modules\models\goods\GoodsCommon;
use apiadmin\modules\models\goods\Goods;
/*
	秒杀相关控制器
*/

class SeckillController extends CoreController
{

	public function actionGet_activity()
	{
		$data = SeckillActivity::getActivity();
		$this->out('秒杀活动',$data);
	}

	/*
		编辑活动
	*/
	public function actionEdit_activity()
	{
		$id = $this->request('id');
		$model = new SeckillActivity;
		if($id>0) $model = SeckillActivity::findOne($id);
		$this->request['update_time'] = time();

		if($model->load($this->request,'') && $model->validate() && $model->save())
		{
			// if($id<=0) $id = Yii::$app->db->getLastInsertId();
			$this->out('操作成功');
		}else{
			$error = $model->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);			
		}
	}

	/*
		* 拼团活动实例列表
	*/
	public function actionInstance_list()
	{
		$params = array(
			'page'	=> $this->request('page',1),
			'limit'	=> $this->request('page_size',10),
			'order'	=> 'instance_id desc',
		);
		$where = array('where'=>[],'and'=>[]);
		$list = SeckillInstance::getList($where,$params);
		$pages = SeckillInstance::$pages;
		$this->out('拼团实例',$list,['pages'=>$pages]);
	}


	/*
		创建拼团活动实例
	*/
	public function actionEdit_seckill_instance()
	{
		$params = json_decode($this->request('data'),1);
		$params['start_time'] = strtotime($params['start_time']);
		$params['end_time'] = strtotime($params['end_time']);
		$params['trailer_time'] = strtotime($params['trailer_time']);
		$this->intanceValidata($params);

		$id = $params['instance_id'];
		$model = new SeckillInstance;
		if($id>0) $model = SeckillInstance::findOne($id);
		$params['update_time'] = time();

		if($model->load($params,'') && $model->validate() && $model->save())
		{
			$this->out('操作成功');
		}else{
			$error = $model->getErrors();
			$errMsg = $this->model_errors($error);
			$this->error($errMsg);			
		}
	}

	private function intanceValidata($params)
	{
		if($params['start_time']>=$params['end_time']) $this->error('开始时间应大于结束时间');
		if($params['trailer_time']>$params['start_time']) $this->error('预告时间应小于开始时间');
	}

	/*
		* 获取拼团实例
		* instance_id  实例id
	*/
	public function actionGet_instance()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		$data = SeckillInstance::getInstance($instanceId);
		$data['start_time'] = date('Y-m-d H:i:s',$data['start_time']);
		$data['end_time'] = date('Y-m-d H:i:s',$data['end_time']);
		$data['trailer_time'] = date('Y-m-d H:i:s',$data['trailer_time']);
		$this->out('实例数据',$data);
	}

	/*
		删除拼团实例
		* instance_id
	*/
	public function actionDel_instance()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		try{
			$transaction = Yii::$app->db->beginTransaction();
			if(!SeckillInstance::deleteAll(['instance_id'=>$instanceId]))
				throw new \Exception("实例删除失败");

			$goods = SeckillGoods::find()->where(['instance_id'=>$instanceId])->asarray()->one();
			if($goods)
			{
				//删除拼团商品
				if(!SeckillGoods::deleteAll(['instance_id'=>$instanceId]))
					throw new \Exception("实例商品删除失败");

				//将促销商品变成正常
				$where = ['goods_commonid'=>$goods['goods_commonid']];
				if(!Goods::updateAll(['promotion_type'=>0,'update_time'=>time()],$where))
					throw new \Exception("商品修改类型失败1");
				if(!GoodsCommon::updateAll(['promotion_type'=>0,'update_time'=>time()],$where))
					throw new \Exception("商品修改类型失败2");					
			}

			$transaction->commit();
			$this->out('操作成功');
		}catch(\Exception $e){

			$transaction->rollback();
			$this->error($e->getMessage());
		}

	}	


	/*
		* 拼团实例下的商品列表
		* instance_id
	*/
	public function actionInstance_goods_list()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		$params = array(
			'instance_id'	=> $instanceId,
			'page'			=> $this->request('page',1),
			'limit'			=> $this->request('page_size',10)
		);
		$data = SeckillGoods::instanceGoodsList($params);		
		$pages = SeckillGoods::$pages;
		$this->out('拼团商品列表',$data,['pages'=>$pages]);
	}


	/*
		添加拼团产品
		instance_id  实例id
		goods  商品信息
	*/
	public function actionAdd_instance_goods()
	{
		if(!$goods = $this->request('goods')) $this->error('参数有误');
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		$goodsInfo = json_decode($goods,1);
		
		try{
			$transaction = Yii::$app->db->beginTransaction();
			foreach($goodsInfo as $goods)
			{
				$model = SeckillGoods::findOne(['instance_id'=>$instanceId,'goods_id'=>$goods['goods_id']]);
				if(!$model) $model = new SeckillGoods;
				$model->instance_id = $instanceId;
				$model->goods_commonid = $goods['goods_commonid'];
				$model->goods_id = $goods['goods_id'];
				$model->seckill_price = $goods['goods_price'];
				if(!$model->save()) throw new \Exception("操作失败");
			}
			//将产品修改为促销产品
			$where = ['goods_commonid'=>$model->goods_commonid];
			if(!Goods::updateAll(['promotion_type'=>2,'update_time'=>time()],$where))
				throw new \Exception("商品修改类型失败1");
			if(!GoodsCommon::updateAll(['promotion_type'=>2,'update_time'=>time()],$where))
				throw new \Exception("商品修改类型失败2");								

			$transaction->commit();
			$this->out('操作成功');
		}catch(\Exception $e)
		{
			$transaction->rollback();
			$this->error($e->getMessage());
		}
	}

	/*
		删除拼团商品
		* instance_id 
		* goods_commonid
	*/
	public function actionDel_instance_goods()
	{
		if(!$goodsCommonid = $this->request('goods_commonid')) $this->error('参数有误');
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');	
		$transaction = Yii::$app->db->beginTransaction();
		try{
			if(!SeckillGoods::deleteAll(['instance_id'=>$instanceId,'goods_commonid'=>$goodsCommonid]))
				throw new \Exception("删除失败");

			//将产品修改为普通产品
			$where = ['goods_commonid'=>$goodsCommonid];
			if(!Goods::updateAll(['promotion_type'=>0,'update_time'=>time()],$where))
				throw new \Exception("商品修改类型失败1");
			if(!GoodsCommon::updateAll(['promotion_type'=>0,'update_time'=>time()],$where))
				throw new \Exception("商品修改类型失败2");				
			$transaction->commit();
			$this->out('操作成功');				
		}catch(\Exception $e)
		{
			$transaction->rollback();
			$this->error($e->getMessage());
		}

		
		if($res) $this->out('删除成功');
		$this->error('删除失败');	
	}

}