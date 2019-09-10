<?php
/*
	产品产品分类
*/
namespace api\modules\v1\controllers\goods;
use Yii;
use api\modules\v1\controllers\CoreController;
use api\modules\models\goods\Category;

class CategoryController extends CoreController
{

	/*
		获取一级产品分类
	*/
	public function actionCategroy_root()
	{
		// var_dump('expression');die;
		$query = (new \yii\db\Query());
		$data  = $query->from('goods_category')
					   ->where(['parent_id'=>0])
					   ->all();

		$this->out('一级分类',$data);
	}

	//获取子分类
	// pid  父级ID
	// is_sub  是否获取所有子类 1是 0否
	public function actionGet_child()
	{
		$query = (new \yii\db\Query());
		if(!$pid = $this->request['pid'])
			$this->error('参数错误');

		if($this->request['is_sub'])   
		{
			$data = Category::allGoodsCates($pid);
		}else
		{
			$data = Category::getChildreCategory($pid);
		}

		$this->out('子分类',$data);
	}
}