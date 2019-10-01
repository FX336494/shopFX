<?php
namespace apiadmin\modules\v1\controllers\member; 
use apiadmin\modules\v1\controllers\CoreController;
use \Yii;
use common\utils\OutputExecl;
use apiadmin\modules\models\member\Member;

/**
* 订单相关控制器
*/

class MemberController extends CoreController
{
	/*
		*会员列表
	*/
	public function actionMember_list()
	{
		$where  = $this->formartWhere();
		$params = array(
			'field'	=> ['member_id','member_name','member_mobile','invite_id','member_avatar','grade','state',
			'create_time','update_time'],
			'order' => 'member_id desc',
			'page'	=> $this->request('page','1'),
			'limit' => $this->request('page_size',10),
		);
		$list = Member::MemberList($where,$params);
		$pages = Member::$pages;
		$this->out('会员列表',$list,array('pages'=>$pages));
	}

	//组装条件
	public function formartWhere()
	{
		$where = [];
		$whereAnd = [];
		$searchKeys = json_decode($this->request('search'),1);
		if(!$searchKeys) return array('where'=>$where,'whereAnd'=>$whereAnd);
		foreach($searchKeys as $k=>$val)
		{
			if(!$val) continue;
			if($k=='date')
			{
				if(!$val['0'] || !$val['1']) continue;
				$whereAnd[] = ['between', 'create_time', strtotime($val[0]),strtotime($val[1])]; 
			}elseif ($k=='member_name') {
				$whereAnd[] = ['like',$k,$val];
			}else
			{
				$where[$k] = $val;
			}
		}

		return array('where'=>$where,'whereAnd'=>$whereAnd);
	}	


	/*
		删除会员
		member_id
	*/
	public function actionMember_del()
	{
		if(!$memberId = $this->request('member_id')) $this->error('参数错误');
		$res = Member::MemberDel($memberId);
		if($res) $this->out('删除成功');
		$this->error('删除失败');
	}

	/*
		获取单个会员信息
		* member_id 会员ID
	*/
	public function actionMember_info()
	{
		if(!$memberId = $this->request('member_id')) $this->error('参数错误');
		$field  = ['member_id','member_name','member_mobile'];
		$member = Member::getMemberById($memberId,$field);
		$this->out('会员信息',$member);
	}

	/*
		通过id或是电话 搜索会员信息
		* member 
	*/
	public function actionQuery_member()
	{
		if(!$memberKey = $this->request('member_key')) $this->error('参数错误');
		$field  = ['member_id','member_name','member_mobile'];
		$member = Member::queryMember($memberKey,$field);
		$this->out('会员信息',$member);		
	}



	//会员信息修改
	public function actionMember_edit()
	{
		$params = $this->request;

		if($params['loginpwd'])
			$params['loginpwd'] = md5($params['loginpwd']);
		else
			unset($params['loginpwd']);

		if($params['paypwd'])
			$params['paypwd'] = md5($params['paypwd']);
		else
			unset($params['paypwd']);		

		$memberModel = $this->model('member\Member',$params,'Edit',$this->request('member_id'));
		$memberModel->update_time = time();
		if(!$memberModel->save(false)) $this->error('修改失败');  
		$this->out('修改成功');
	}


	//会员添加
	public function actionMember_add()
	{
		$params = $this->request;	
		$memberModel = $this->model('member\Member',$params,'Reg',$this->request('member_id'));
		$memberModel->loginpwd = md5($memberModel->loginpwd);
		$memberModel->paypwd = md5($memberModel->paypwd);
		$memberModel->auth_key = Yii::$app->security->generateRandomString(); 
		$memberModel->create_time = time();
		if(!$memberModel->save(false)) $this->error('添加失败');  
		$this->out('添加成功');
	}


	//会员导出
	public function actionExport()
	{
		$where  = $this->formartWhere();
		$params = array(
			'field'	=> ['member_id','member_name','member_mobile','invite_id','grade','state',
			'create_time','update_time'],
			'order' => 'member_id desc',
			'page'	=> $this->request('page','1'),
			'limit' => $this->request('page_size',10),
		);
		$list = Member::MemberList($where,$params);

		//组织导出数据
		$exportData = array();
		foreach($list as $val)
		{

			$temp = [];
			$temp[] = $val['member_id'];
			$temp[] = $val['member_name'];
			$temp[] = $val['member_mobile'];
			$temp[] = $val['state']?'正常':'冻结';
			$temp[] = date("Y-m-d H:i:s",$val['create_time']);
			$exportData[] = $temp;
		}
		$headData = array('A1'=>'会员ID','B1'=>'会员姓名','C1'=>'会员电话','D1'=>'会员状态','E1'=>'注册时间');
		$fileName = 'member-'.date('Y-m-d').'.xls';
		$execlObj = new OutputExecl();
		$res = $execlObj->output($headData,$exportData,$fileName);
		if($res)
			$this->out('下载地址',array('url'=>$res));
		else
			$this->error('导出失败');
	}


}