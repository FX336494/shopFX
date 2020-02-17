<?php
namespace apiadmin\modules\v1\controllers\promotion; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\promotion\CouponsInstance;
use apiadmin\modules\models\promotion\Coupons;
use apiadmin\modules\models\goods\GoodsCommon;
use apiadmin\modules\models\goods\GoodsClass;
/*
	优惠券相关控制器
*/

class CouponsController extends CoreController
{

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
		$list = CouponsInstance::getList($where,$params);
		$pages = CouponsInstance::$pages;
		$this->out('优惠券实例',$list,['pages'=>$pages]);
	}



	/*
		* 获取拼团实例
		* instance_id  实例id
	*/
	public function actionGet_instance()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		$data = CouponsInstance::getInstance($instanceId);
		$data['start_time'] = date('Y-m-d H:i:s',$data['start_time']);
		$data['end_time'] = date('Y-m-d H:i:s',$data['end_time']);
		$data['type_msg'] = '';
		if($data['use_type']=='2')
		{
			//分类下使用
			$classData = GoodsClass::getParentClass($data['type_id']);
			if($classData){
				foreach($classData as $class){
					$data['type_msg'] .= $class['category_name'].' > ';
				}
				$data['type_msg'] = trim($data['type_msg'],' > ');
			}

		}
		
		if($data['use_type']=='3'){
			//单品使用
			$goods = GoodsCommon::getCommonGoods(['goods_commonid'=>$data['type_id']],['goods_name']);
			$data['type_msg'] = $goods?$goods['goods_name']:'';
		}

		$this->out('实例数据',$data);
	}


	/*
		创建优惠券实例
	*/
	public function actionEdit_coupons_instance()
	{
		$params = json_decode($this->request('data'),1);
		$params['start_time'] = strtotime($params['start_time']);
		$params['end_time'] = strtotime($params['end_time']);
		$this->intanceValidata($params);

		$id = $params['instance_id'];
		$model = new CouponsInstance;
		if($id>0){
			$model = CouponsInstance::findOne($id);
		}else{
			$params['create_time'] = time();
		}
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
		if($params['total_nums']<=0) $this->error('发放总数需大于0');
		if($params['total_nums']<$params['limit_nums']) $this->error('每人限领数不能大于总数');
		if(!isset($params['coupons_name'])) $this->error('名称不能为空');

		if($params['time_type']=='1' && $params['start_time']>=$params['end_time'])
			$this->error('开始时间应大于结束时间');

		if($params['type']=='1' && $params['coupons_money']<=0)
			$this->error('面额需大于0');		

		if($params['type']=='1' && $params['coupons_money']>=$params['consume_amount']) 
			$this->error('优惠券金额不能大于消费金额');

		if($params['type']=='2' && $params['useful_day']<0) $this->error('有效天数不能小于0');
	}	


	/*
		删除优惠券实例
		* instance_id
	*/
	public function actionDel_instance()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		try{
			$transaction = Yii::$app->db->beginTransaction();
			if(!CouponsInstance::deleteAll(['instance_id'=>$instanceId]))
				throw new \Exception("实例删除失败");

			//将已领取的未用的优惠券失效处理
			Coupons::updateAll(['state'=>'4','update_time'=>time()],['coupons_instance_id'=>$instanceId]);

			$transaction->commit();
			$this->out('操作成功');
		}catch(\Exception $e){

			$transaction->rollback();
			$this->error($e->getMessage());
		}

	}	


}
