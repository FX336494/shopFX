<?php
namespace api\modules\v1\controllers\platform;
use api\modules\v1\controllers\CoreController;
use api\modules\models\goods\Category;
use Yii;

/*
	公告相关
*/
class NoticeController extends CoreController
{
	public function actionPlatinfo()
	{
		$data = array();
		$data['banner'] = $this->getBanner();
		$data['class_nav'] = Category::getCategroy(['index_menu'=>'1'],["id",'category_name','img']);
		$this->out('平台信息',$data);
	}

	//轮播图片
	private function getBanner()
	{
		$query = new yii\db\Query();
		$data = $query->from('banner')
					  ->all();
		$list = [];
		if($data){
			foreach($data as $val)
			{
				$list[] = $val;
			}
		}
		return $list;
	}
}