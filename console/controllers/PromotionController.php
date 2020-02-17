<?php
namespace console\controllers; 
use Yii;
use common\models\promotion\PintuanInstanceModel;
use common\models\promotion\SeckillInstanceModel;
use api\modules\models\goods\GoodsCommon;
use api\modules\models\goods\Goods;
/*
	* 促销相关定时处理 
*/
class PromotionController extends CoreController
{

	/*
		* 促销活动结束
		* 1分钟执行一次
		0/1 * * * * php yii promotion/promotion-end
	*/
	public function actionPromotionEnd()
	{
		//拼团活动
		$this->PintuanEnd();

		//秒杀活动
		$this->SeckillEnd();
	}


	/*
		* 拼团活动结束
	*/
	private function PintuanEnd()
	{
		$curTime = time();
		$where = ['is_end'=>'0'];
		$and = ['<','a.end_time',$curTime];
		$field = ['a.instance_id','a.end_goods_status','b.goods_id','b.goods_commonid'];
		$data = PintuanInstanceModel::find()->select($field)->alias('a')
				->leftJoin('pintuan_goods as b','a.instance_id=b.instance_id')
				->where($where)->andFilterWhere($and)
				->asarray()->all();
		if(!$data) return true;

		foreach($data as $val)
		{
			$transaction = \Yii::$app->db->beginTransaction();
			if($val['end_goods_status']=='1')
			{
				//变成普通产品
				$temp = array('promotion_type'=>'0','update_time'=>$curTime);
			}else
			{
				//下架
				$temp = array('goods_state'=>'0','update_time'=>$curTime);
			}
			$where = ['goods_commonid'=>$val['goods_commonid']];
			$flag1 = GoodsCommon::updateAll($temp,$where);
			$flag2 = Goods::updateAll($temp,$where);

			//更新实例
			$temp2 = array('is_end'=>'1','update_time'=>time());
			$flag3 = PintuanInstanceModel::updateAll($temp2,['instance_id'=>$val['instance_id']]);


			$flag  = $flag1 && $flag2 && $flag3;
			if($flag){
				$transaction->commit();
			}
			$transaction->rollback();				
		}
	}

	/*
		* 秒杀活动结束
	*/
	private function SeckillEnd()
	{
		$curTime = time();
		$where = ['is_end'=>'0'];
		$and = ['<','a.end_time',$curTime];
		$field = ['a.instance_id','a.end_goods_status','b.goods_id','b.goods_commonid'];
		$data = SeckillInstanceModel::find()->select($field)->alias('a')
				->leftJoin('seckill_goods as b','a.instance_id=b.instance_id')
				->where($where)->andFilterWhere($and)
				->asarray()->all();
		if(!$data) return true;

		foreach($data as $val)
		{
			$transaction = \Yii::$app->db->beginTransaction();
			if($val['end_goods_status']=='1')
			{
				//变成普通产品
				$temp = array('promotion_type'=>'0','update_time'=>$curTime);
			}else
			{
				//下架
				$temp = array('goods_state'=>'0','update_time'=>$curTime);
			}
			$where = ['goods_commonid'=>$val['goods_commonid']];
			$flag1 = GoodsCommon::updateAll($temp,$where);
			$flag2 = Goods::updateAll($temp,$where);

			//更新实例
			$temp2 = array('is_end'=>'1','update_time'=>time());
			$flag3 = SeckillInstanceModel::updateAll($temp2,['instance_id'=>$val['instance_id']]);


			$flag  = $flag1 && $flag2 && $flag3;
			if($flag){
				$transaction->commit();
			}
			$transaction->rollback();				
		}
	}
}