<?php
/*
	促销相关控制器
*/
namespace api\modules\v1\controllers\goods;
use Yii;
use api\modules\v1\controllers\CoreController;
use common\models\promotion\PintuanActivityModel as PintuanActivity;
use api\modules\models\promotion\PintuanInstance;
use common\models\promotion\SeckillActivityModel as SeckillActivity;
use api\modules\models\promotion\SeckillInstance;
use api\modules\models\promotion\SeckillGoods;
use api\modules\models\promotion\PintuanGoods;

class PromotionController extends CoreController
{

	/*
		*获取促销活动信息
	*/
	public function actionGet_promotion_info()
	{
		$data = array();
		//拼团信息
		$pintuanInfo = PintuanActivity::getActivity(['state'=>'1']);
		$data['pintuan_info'] = $pintuanInfo;

		$data['seckill_info'] = SeckillActivity::getActivity(['state'=>'1']);

		$this->out('促销信息',$data);
	}


	/*
		获取拼团实例 及产品
	*/
	public function actionGet_pintaun_instance()
	{
		$whereArr = $where = $and = [];
		$curTime = time();
		$where['state'] = '1';
		$and[] = ['>','end_time',$curTime];
		$whereArr['where'] = $where;
		$whereArr['and'] = $and;

		$params = array(
			'page'	=> $this->request('page',1),
			'limit'	=> $this->request('page_size',10),
			'goods_limit' => 2,
		);
		$data = PintuanInstance::getList($whereArr,$params,['goods']);
		$this->out('拼团实例信息',$data);
	}

	/*
		获取秒杀实例 及产品
	*/
	public function actionGet_seckill_instance()
	{
		$type = $this->request('type',1);
		$whereArr = $where = $and = [];
		$curTime = time();
		
		//即将开始
		if($type==1){
			$where['state'] = '1';
			$and[] = ['<','trailer_time',$curTime];
			$and[] = ['>','start_time',$curTime];
		}
		//进行中
		if($type==2){
			$where['state'] = '1';
			$and[] = ['<=','start_time',$curTime];
			$and[] = ['>','end_time',$curTime];
		}

		//已结束
		if($type==3){
			$and[] = ['<','end_time',$curTime];
		}		

		$whereArr['where'] = $where;
		$whereArr['and'] = $and;

		$params = array(
			'page'	=> $this->request('page',1),
			'limit'	=> $this->request('page_size',10),
			'goods_limit' => 2,
		);
		$data = SeckillInstance::getList($whereArr,$params,['goods']);
		$this->out('秒杀实例信息',$data);
	}


	/*
		获取秒杀实例产品
		*　instance_id  实例id
	*/
	public function actionSeckill_instance_goods()
	{
		if(!$insId = $this->request('instance_id')) $this->error('参数有误');
		$params = array(
			'instance_id'	=> $insId,
			'page'	=> $this->request('page',1),
			'limit'	=> $this->request('page_size',10)
		);
		$data = SeckillGoods::instanceGoodsList($params);
		$this->out('实例产品',$data);
	}


	/*

	*/

	/*
		获取拼团实例产品
		*　instance_id  实例id
	*/
	public function actionPintuan_instance_goods()
	{
		if(!$insId = $this->request('instance_id')) $this->error('参数有误');
		$params = array(
			'instance_id'	=> $insId,
			'page'	=> $this->request('page',1),
			'limit'	=> $this->request('page_size',10)
		);
		$data = PintuanGoods::instanceGoodsList($params);
		$this->out('实例产品',$data);
	}


}
