<?php
namespace apiadmin\modules\v1\controllers\order; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\order\Orders;
use apiadmin\modules\models\order\OrderCommon;
use common\utils\OutputExecl;

/**
* 订单相关控制器
*/

class OrderController extends CoreController
{
	//订单列表
	public function actionOrder_list()
	{
		$where = $this->formartWhere();
		$page  = $this->request('page','1');
		$params = array(
			'field'	=> ['order_id','buyer_id','buyer_name','buyer_phone','create_time','order_amount','order_state','order_type'],
			'order' => 'order_id desc',
			'page'	=> $page,
			'limit' => $this->request('page_size',10),
		);
		$list = Orders::getOrderList($where,$params);
		$pages = Orders::$pages;
		$orderState = Yii::$app->params['orderState'];
		$orderType = Yii::$app->params['orderType'];
		$this->out('订单列表',$list,array('pages'=>$pages,'orderState'=>$orderState,'orderType'=>$orderType));
	}

	/*
		订单列表 条件组装
	*/
	public function formartWhere()
	{
		$where = [];
		$whereAnd = [];
		$searchKeys = json_decode($this->request('search'),1);
		foreach($searchKeys as $k=>$val)
		{
			if(!$val) continue;
			if($k=='date')
			{
				if(!$val['0'] || !$val['1']) continue;
				$whereAnd[] = ['between', 'create_time', strtotime($val[0]),strtotime($val[1])]; 
			}else
			{
				$where[$k] = $val;
			}
		}

		return array('where'=>$where,'whereAnd'=>$whereAnd);
	}

	//订单详情
	public function actionOrder_detail()
	{
		if(!$orderId = $this->request('order_id'))
			$this->error('参数错误');

		$where = ['order_id'=>$orderId];
		$field = ['*'];
		$extend= array('order_goods','order_common');
		$data = Orders::getOrderDetail($where,$field,$extend);
		$this->out('订单详情',$data);
	}

	/*
		发货
		order_id 订单ID
		express_id 物流公司ID
		logistics_nums 物流单号
	*/
	public function actionDelivery() 
	{
		if(!$orderId = $this->request('order_id')) $this->error('参数错误');
		if(!$expressId = $this->request('express_id')) $this->error('参数错误');
		if(!$logisticsNums = $this->request('logistics_nums')) $this->error('参数错误');

		try{

			$transaction = Yii::$app->db->beginTransaction();

			//更新orders
			$data = array('order_state'=>'3','shipping_time'=>time());
			$where = ['order_id'=>$orderId];
			if(!Orders::updateAll($data,$where))
				throw new \Exception('发货失败1');
				
			$data = array('shipping_time'=>time(),'shipping_express_id'=>$expressId,'shipping_code'=>$logisticsNums);
			if(!OrderCommon::updateAll($data,$where))
				throw new \Exception('发货失败2');

			$transaction->commit();
			$this->out('发货成功');
		}catch(\Exception $e)
		{
			$transaction->rollback();
			$this->error($e->getMessage());
		}
	
	}


	//删除订单
	public function actionOrder_del()
	{
		if(!$orderId = $this->request('order_id')) $this->error('参数错误');

		if(Orders::delOrders($orderId))
			$this->out('删除成功');
		else
			$this->error('删除失败');
	}



	//订单导出
	public function actionExport()
	{

		$where = $this->formartWhere();
		$page  = $this->request('page','1');
		$params = array(
			'field'	=> ['order_id','buyer_id','buyer_name','buyer_phone','create_time','order_amount','order_state'],
			'order' => 'order_id desc',
			'page'	=> $page,
			'limit' => $this->request('page_size',1000),
		);
		$list = Orders::OrderList($where,$params,array('order_goods','order_common'));
		$orderState = Yii::$app->params['orderState'];

		//组织导出数据
		$exportData = array();
		foreach($list as $val)
		{
			$goodsStr = '';
			foreach($val['order_goods'] as $goods)
			{
				$goodsStr .= $goods['goods_name'].',数量：'.$goods['goods_num'].',';
			}
			$orderCommon = $val['order_common'];

			$temp = [];
			$temp[] = $val['id'];
			$temp[] = $val['buyer_id'];
			$temp[] = $val['buyer_name'];
			$temp[] = $val['buyer_phone'];
			$temp[] = trim($goodsStr,',');
			$temp[] = $val['order_amount'];
			$temp[] = $orderCommon['reciver_info']['area_info'].$orderCommon['reciver_info']['address'];
			$temp[] = $orderCommon['reciver_info']['true_name'].$orderCommon['reciver_info']['mob_phone'];
			$temp[] = $orderState[$val['order_state']];
			$temp[] = date("Y-m-d H:i:s",$val['create_time']);
			$exportData[] = $temp;
		}
		$headData = array('A1'=>'订单ID','B1'=>'会员ID','C1'=>'会员姓名','D1'=>'会员电话','E1'=>'购买产品','F1'=>'订单金额','G1'=>'收货地址','H1'=>'收货人','I1'=>'订单状态','J1'=>'下单时间');

		$fileName = 'order-'.date('Y-m-d').'.xls';

		$execlObj = new OutputExecl();
		$res = $execlObj->output($headData,$exportData,$fileName);
		if($res)
			$this->out('下载地址',array('url'=>$res));
		else
			$this->error('导出失败');
	}



}