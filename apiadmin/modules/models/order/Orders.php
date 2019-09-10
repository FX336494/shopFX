<?php

/*
	订单管理相关
*/
namespace apiadmin\modules\models\order;
use Yii;
use common\models\order\OrdersModel;
use apiadmin\modules\models\order\OrderCommon;
use apiadmin\modules\models\order\OrderGoods;

class Orders extends OrdersModel
{
	//删除订单
	public static function delOrders($orderId)
	{
		try{
			$transaction = Yii::$app->db->beginTransaction();

			$where = ['order_id'=>$orderId];
			if(!self::deleteAll($where))
				throw new Exception('删除失败');
			if(!OrderCommon::deleteAll($where))
				throw new Exception('删除失败');
			if(!OrderGoods::deleteAll($where))
				throw new Exception('删除失败');			
			$transaction->commit();
			return true;
		}catch(\Exception $e)
		{
			$transaction->rollback();
			return false;
		}
		

	}

}