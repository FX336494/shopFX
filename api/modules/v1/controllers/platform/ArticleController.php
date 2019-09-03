<?php
namespace api\modules\v1\controllers\platform;
use api\modules\v1\controllers\CoreController;
use api\modules\models\platform\Article;
use Yii;

/*
	文章（公告）相关
*/
class ArticleController extends CoreController
{
	/*
		文章公告列表
		* class_id  公告分类 
	*/
	public function actionArticle_list()
	{
		$classId = $this->request($classId);
		$whereArr = array();
		$where['where'] = ['state'=>'1'];
		if($classId) $whereArr['where']['class_id'] = $classId;

		$params = array(
			'field'	=> ['id','title','desc','logo','update_time'],
			'order' => 'id desc',
			'page'  => $this->request('page',1),
			'limit' => $this->request('page_size',10)
		);

		$list = Article::articleList($whereArr,$params);
		$this->out('公告列表',$list);
	}

	/*
		文章详情
	*/
	public function actionArticle_detail()
	{
		if(!$id = $this->request('id')) $this->error('参数错误');
		$data = Article::getArticleById($id);
		$this->out('文章详情',$data);
	}

}