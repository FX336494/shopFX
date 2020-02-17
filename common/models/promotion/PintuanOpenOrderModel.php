<?php
namespace common\models\promotion;
use Yii;
use common\models\BaseModel;
use common\models\promotion\PintuanGoodsModel;
use common\models\member\MemberModel;
use common\models\goods\GoodsModel;

/*
	拼团开团订单模型
*/
class PintuanOpenOrderModel extends BaseModel
{
	public static function tableName()
	{
		return '{{%pintuan_open_order}}';
	}

	public function rules()
	{
		return [
			[['state','pay_state','join_order_ids'],'string'],
			[['order_id','open_member_id','goods_id','start_time','end_time','open_nums','join_nums','instance_id','goods_commonid','complete_time'],'integer'],
			[['order_id','open_member_id','goods_id','open_nums','join_nums'],'required'],
		];
	}

	/*
		* 列表
		* whereArr 条件
		* params 基本参数 包含 field order page limit
		* extends  扩展信息 一些相关的信息
		* 
	*/
	public static function getList($whereArr,$params,$extends=array())
	{
		$model  = self::find();
		$where  = $whereArr['where'];
		$whereAnd = isset($whereArr['whereAnd'])?$whereArr['whereAnd']:[];
		$models = self::queryFormart($model,$where,$params,$whereAnd);
		$model  = $models['model'];
		self::$pages = $models['pages'];

		$data  = $model->asarray()->all();
		if(!$data) return array();
		if(!$extends) return $data;
		$stateText = \Yii::$app->params['pintuanState'];
		foreach($data as &$val)
		{
			foreach($extends as $type)
			{
				if($type=='member')
				{
					$field = ["member_name",'member_mobile'];
					$member = MemberModel::getMemberById($val['open_member_id'],$field);
					$val = array_merge($val,$member);
					$val['desc'] = $stateText[$val['state']];
					if($val['state']=='1')
						$val['desc'] = '还差'.($val['open_nums']-$val['join_nums']).'人成团';
				}else if($type=='goods')
				{
					$field = ['goods_name','goods_image'];
					$goods = GoodsModel::getGoods(['goods_id'=>$val['goods_id']],$field);
					$val = array_merge($val,$goods);
				}
			}
		}
		return $data;				
	}

	/*
		获取开团订单
	*/
	public static function getOpenOrder($where,$field=['*'])
	{
		return self::find()->select($field)->where($where)->asarray()->one();
	}

}