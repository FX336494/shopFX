<?php
namespace api\modules\models\promotion;
use Yii;
use common\models\promotion\CouponsInstanceModel;
use api\modules\models\promotion\Coupons;

/*
	优惠券实例模型
*/
class CouponsInstance extends CouponsInstanceModel
{
	/*
		会员在产品下的优惠券信息
	*/
	public static function memberCouponsInGoods($params)
	{
		$curT = time();
		$gcIds = $params['gc_id1'];
		$gcIds .= isset($params['gc_id2'])?','.$params['gc_id2']:'';
		$gcIds .= isset($params['gc_id3'])?','.$params['gc_id3']:'';

		$sql = "select * from coupons_instance where state='1' and get_type = '1' and ((time_type='1' and start_time<=".$curT." and end_time>=".$curT.") or time_type='2') and ((use_type='2' and type_id in(".$gcIds.")) or (use_type='3' and type_id=".$params['goods_commonid'].") or (use_type='1'))";
		$data = Yii::$app->db->createCommand($sql)->queryAll();
		if(!$data) return [];

		foreach($data as &$val)
		{
			$insId = $val['instance_id'];
			$where = ['coupons_instance_id'=>$insId,'member_id'=>$params['member_id']];
			//可领取数量
			$wasDraw = Coupons::find()->where($where)->count();
			$val['was_draw'] = $wasDraw;
			$val['undraw_num'] = $val['limit_nums'] - $wasDraw; //可领取数量
			$val['coupons_desc'] = '满'.$val['consume_amount'].'使用';
			$val['time_desc'] = '';
			if($val['time_type']=='1')
				$val['time_desc'] = date("Y.m.d",$val['start_time']).'-'.date("Y.m.d",$val['end_time']);
			if($val['time_type']=='2')
				$val['time_desc'] = '领取后'.$val['useful_day'].'天内使用';

			$val['btn_desc'] = '立即领取';
			if($val['undraw_num']==0) $val['btn_desc'] = '已领取';
			if($val['undraw_num']>0 && $val['undraw_num']<$val['limit_nums'])
				$val['btn_desc'] = '继续领取';
			//预计优惠金额
			$val['expect_money'] = 0;
			if($val['type']=='1') $val['expect_money'] = $val['coupons_money'];
			if($val['type']=='2') $val['expect_money'] = $params['goods_price']*(10-$val['discount'])/10;
			$val['type_text'] = parent::$typeText[$val['type']];
		}
		return $data;
	}

}