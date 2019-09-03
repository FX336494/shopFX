<?php
namespace apiadmin\modules\v1\controllers\platform; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\platform\Banner;

class BannerController extends CoreController
{

	public function actionBanner_list()
	{
		$data = Banner::bannerList();
		$this->out('轮播图',$data);
	}

	/*
		修改
	*/
	public function actionBanner_edit()
	{
		$id = $this->request('id');
		$model = new Banner;

		if($id) $model = $model::findOne($id);    
		$model->img_url = $this->request('img_url');
		$model->link = $this->request('link');
		if($model->save()) $this->out('操作成功');
		$this->error('操作失败');
	}

	/*
		删除轮播图片
	*/
	public function actionBanner_del()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');

		if(!$res = Banner::deleteAll(['id'=>$id]))
			$this->error('删除失败');
		else
			$this->out('删除成功');
	}

}