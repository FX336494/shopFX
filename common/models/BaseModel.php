<?php
/*
	基础模型
*/
namespace common\models;
use \Yii;

class BaseModel extends \yii\db\ActiveRecord
{
	//总记录数
	public static $pages;
	
	/*
		组装查询 针对是数据的分页查询
		$model  对象
		$where  条件
		$params  参数
	*/
	public static function queryFormart($model,$where,$params,$whereAnd=array())
	{
		$field = ['*'];
		$order = '';		
		
		if(isset($params['field']))
			$field = $params['field'];
		else
			$field = ['*'];

		if(isset($params['order']))
			$order = $params['order'];

		//分页
		if(isset($params['page']) && $params['page']>0){
			$page   = $params['page'];
			$limit  = $params['limit']?$params['limit']:10;
			$offset = ($page-1)*$limit;
		}else{
			$offset = '';
			$limit  = '';
		}
		$model = $model->select($field)->where($where);

		if($whereAnd){
			foreach($whereAnd as $and){
				$model = $model->andFilterWhere($and);
			}
		}

		//总数量
		$totalNums = $model->count();

		$models = $model->orderBy($order)->offset($offset)->limit($limit);
					
		return array(
			'model'	=> $models,
			'pages'	=> $totalNums?$totalNums:0
		);
	}	


	//返回错误
	public static function outError($errors)
	{
		if(!is_array($errors)) return array('state'=>false,'msg'=>'');
		$firstError = array_shift($errors);
		if(!is_array($firstError)) return array('state'=>false,'msg'=>'');
		$errMsg =  array_shift($firstError);
		return array('state'=>false,'msg'=>$errMsg);
	}

}
