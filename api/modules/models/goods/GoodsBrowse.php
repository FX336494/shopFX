<?php
/*
	*商品浏览相关
*/
namespace api\modules\models\goods;
use Yii;
use common\models\DB;

class GoodsBrowse extends \yii\db\ActiveRecord
{
	static $pageSize = 15; //每页面显示数量 

	public static function tableName()
	{
		return '{{%goods_browse}}';
	}

	/*
		添加浏览记录
		*goods_commonid 
		*member_id
		*category_id
	*/
	public static function add($data)
	{
		$date = date("Y-m-d");
		$where = ['member_id'=>$data['member_id'],'date'=>$date,'goods_commonid'=>$data['goods_commonid']];
		$browse = self::find()->where($where)->asArray()->one();
		
		if($browse)
		{
			$updateData = array(
				'update_time'	=> time(),
				'views'			=> $browse['views']+1,
			);
			$flag = DB::update('goods_browse',$updateData,$where);
		}else
		{
			$data['update_time'] = time();
			$data['date'] = $date;
			$flag = DB::insert('goods_browse',$data);
		}
		return $flag;
	}



	/*
		浏览列表
	*/
	public static function browseList($memberId,$page=0)
	{
		$offset = $page>0?($page-1)*self::$pageSize:'';
		$limit  = $page>0?self::$pageSize:0;
		$data =  self::find()
				->where(['member_id'=>$memberId])
				->offset($offset)
				->limit($limit)
				->orderBy('update_time desc')
				->asArray()
				->all();
		return $data;		
	}
}
