<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;

/*
	拼团活动模型
*/
class CouponsModel extends BaseModel
{
	public static $couponStateText = array(
		'1'	=> '未使用',
		'2' => '已使用',
		'3'	=> '已过期',
		'4' => '已失效',
	);

	public static function tableName()
	{
		return '{{%coupons}}';
	}

	public function rules()
	{
		return [
			[['coupons_instance_id','member_id','end_time','order_id','update_time','create_time'],'integer'],
			[['state'],'safe'],
			[['coupons_money'],'number'],
		];
	}
}