<?php
/*
	促销相关控制器
*/
namespace api\modules\v1\controllers\goods;
use Yii;
use api\modules\v1\controllers\CoreController;
use api\modules\models\promotion\Coupons;
use api\modules\models\promotion\CouponsInstance;


class CouponsController extends CoreController
{

	/*
		领取优惠券
		* instance_id  优惠券实例id
	*/
	public function actionGet_coupons()
	{
		if(!$instanceId = $this->request('instance_id')) $this->error('参数有误');
		$couponsInstance = CouponsInstance::getInstance($instanceId);
		if(!$couponsInstance || !$couponsInstance['state']) $this->error("优惠券信息有误");
		$where = ['coupons_instance_id'=>$instanceId,'member_id'=>$this->_memberId];
		$wasGetNums = Coupons::find()->where($where)->count();
		if($wasGetNums>=$couponsInstance['limit_nums']) $this->error('已领取');

		try{
			$transaction = Yii::$app->db->beginTransaction();
			$model = new Coupons;

			$model->coupons_instance_id = $instanceId;
			$model->member_id = $this->_memberId;
			$model->create_time = time();
			$model->end_time = $couponsInstance['time_type']=='1'?$couponsInstance['end_time']:$couponsInstance['useful_day']*86400+time();
			if(!$model->save()) throw new \Exception("领取失败");

			//增加领取人数
			if(!CouponsInstance::updateAllCounters(['giveout_nums'=>1],['instance_id'=>$instanceId]))
				throw new \Exception("领取人数增加失败");
			
			$transaction->commit();
			$this->out('领取成功');
		}catch(\Exception $e){
			$transaction->rollback();
			$this->error($e->getMessage());
		}

	}

	/*
		优惠券列表
	*/
	public function actionCoupons_list()
	{
		$coupons = Coupons::myConpons($this->_memberId);
		$this->out('我的优惠券',$coupons);
	}

}
