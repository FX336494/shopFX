<?php
namespace apiadmin\modules\v1\controllers\platform; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use apiadmin\modules\models\platform\Article;
use apiadmin\modules\models\platform\ArticleClass;

/*
	文章相关控制器
*/

class ArticleController extends CoreController
{
	/*
		文章列表
	*/
	public function actionArticle_list()
	{
		$whereArr['where'] = [];
		$params = [
			'field'=>['id','class_id','title','desc','logo','create_time','state'],
			'order' => 'id desc',
			'page' => $this->request('page',1),
			'limit' => $this->request('page_size',10),
		];
		$data = Article::articleList($whereArr,$params,array('class'));
		$this->out('文章列表',$data,['pages'=>Article::$pages]);
	}


	/*
		获取文章信息
		*id 
	*/
	public function actionArticle_info()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');
		$data = Article::getArticleById($id);
		$this->out('文章信息:'.$id,$data);
	}

	//文章编辑
	public function actionArticle_edit()
	{
		$model = new Article;
		if($id = $this->request('id'))
			$model = $model::findOne($id);
		else
			$model->create_time = time();

		$data = $this->request;
		$model->update_time = time();
		if($model->load($data,'') && $model->save())
			$this->out('操作成功');

		$error = Article::outError($model->errors);
		$this->error($error['msg']?$error['msg']:'操作失败');
	}



	/*
		文章删除
	*/
	public function actionArticle_del()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');

		if(!$res = Article::deleteAll(['id'=>$id]))
			$this->error('删除失败');
		else
			$this->out('删除成功');

	}

	/************* 以下为文章分类 ***************/

	/*
		获取文章分类
	*/
	public function actionArticle_class()
	{
		$data = ArticleClass::articleClassList();
		$this->out('文章分类',$data);
	}


	/*
		编辑分类
	*/
	public function actionArticle_class_edit()
	{
		$model = new ArticleClass;
		if($id = $this->request('id')) $model = $model::findOne($id);
		$data = $this->request;
		if($model->load($data,'') && $model->save())
			$this->out('操作成功');

		$error = ArticleClass::outError($model->errors);
		$this->error($error['msg']?$error['msg']:'操作失败');		
	}

	/*
		删除分类 
	*/
	public function actionArticle_class_del()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');

		if(!$res = ArticleClass::deleteAll(['id'=>$id]))
			$this->error('删除失败');
		else
			$this->out('删除成功');

	}


}