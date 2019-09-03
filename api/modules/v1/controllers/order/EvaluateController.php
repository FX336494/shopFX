<?php
namespace api\modules\v1\controllers\order;
use Yii;
use api\modules\v1\controllers\CoreController;
use api\modules\models\goods\Goods;
use api\modules\models\order\Orders;
/**
* 	订单评价相关
*/
class EvaluateController extends CoreController
{
	/*
		新增评价
	*/
	public function actionAdd_eval()
	{
		// $this->error('sfa',$this->request);
		$transaction = Yii::$app->db->beginTransaction();
		try{
			$data = array(
				'order_id'	=> $this->request('order_id'),
				'member_id'	=> $this->_memberId,
				'member_name'	=> $this->_member['member_name'],
				'member_avatar' => $this->_member['member_avatar'],
				'create_time'	=> time(),
			);
			$goods =  $this->request('order_goods');
			$goodsData = json_decode($goods,1);
			if(!$goodsData) $this->error('参数有误');

			$insertData = array();
			foreach($goodsData as $val){
				$insertData['goods_id'] = $val['goods_id']; 
				$insertData['goods_commonid'] = Goods::getGoodsById($val['goods_id'],['goods_commonid'])['goods_commonid'];
				$insertData['goods_name'] = $val['goods_name'];
				$insertData['goods_price'] = $val['goods_price'];
				$insertData['goods_image'] = $val['goods_image'];
				$insertData['scores'] = $val['scores'];
				$insertData['content'] = $val['eval_text'];			
				$insertData['eval_image'] = json_encode($val['eval_image']);
				$evaluateData = array_merge($insertData,$data);
				if(!$this->db::insert('evaluate_goods',$evaluateData))
					throw new \Exception("评价失败");
			}			
			//更新订单状态
			$data = array('evaluation_state'=>'1');
			if(!Orders::updateOrder($this->request('order_id'),$data))
				throw new \Exception("你已经评价过了");

			$transaction->commit();
			$this->out('评价成功');
		}catch(\Exception $e){
			$transaction->rollback();
			$this->error($e->getMessage());
		}
	}
}