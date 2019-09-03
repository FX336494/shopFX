<?php
namespace api\modules\v1\controllers\goods;
use api\modules\v1\controllers\CoreController;
use api\modules\models\goods\GoodsCommon;
use api\modules\models\goods\Goods;
use api\modules\models\goods\Favorites;
use api\modules\models\goods\Category;
use api\modules\models\goods\GoodsBrowse;
use api\modules\models\order\Evaluate;
use Yii;
/*
	产品相关控制器
*/

class GoodsController extends CoreController
{
	/*
		商品列表
	*/
	public function actionList()
	{
		$where = $this->goodsWhere($this->request);
		$page  = $this->request('page','1');
		$field = "goods_commonid,goods_name,gc_id,goods_image,goods_storage,goods_price,goods_marketprice";
		$params = array(
			'field'	=> $field,
			'page'	=> $page,
			'order' => 'goods_commonid desc',
			'limit' => $this->request('page_size'),
		);
		$goodsList = GoodsCommon::goodsList($where,$params);
		$this->out('产品列表',$goodsList);
	}

	/*
		商品显示条件
	*/
	private function goodsWhere($params)
	{
		$where = [];
		//分类查询
		if(isset($this->request['cate_id']))
		{
			$cateId = $this->request['cate_id'];
			$allChild = Category::allGoodsCates($cateId);
			$catesId  = array($cateId);
			if($allChild){
				foreach($allChild as $child){
					$catesId[] = $child['id'];
				}
			}
			$where = ['in','gc_id',$catesId]; 
		}

		if(isset($this->request['keyword']))
		{
			$where = ['like','goods_name',$this->request['keyword']]; 
		}
		return $where;
	}


	//商品详情
	public function actionGoods_detail() 
	{
		$goodsCommonid = $this->request('goods_commonid');
		if(!$goodsCommonid) $this->error('商品不存在');

		$goodsData = GoodsCommon::getGoodsDetail($goodsCommonid);

		//处理规格
		$goodsData['spec_list'] = $this->goodsSpecList($goodsData);
		$goodsData['goods_body'] = htmlspecialchars_decode($goodsData['goods_body']);
		//是否收藏
		$goodsData['is_collect'] = Favorites::isFovarites($goodsCommonid,$this->_memberId);
		//增加访问量
		GoodsCommon::addGoodsViews($goodsCommonid,$this->_memberId); 

		//获取产品评价
		$ealuateNums = Evaluate::goodsEvaluateCount($goodsCommonid);
		$goodsData['evaluate_nums'] = $ealuateNums;

		$this->out('商品详情',$goodsData);

	}

	/*
		产品规格
	*/
	private function goodsSpecList($goodsData)
	{
		$list = array();
		if($goodsData['spec_name'] && $goodsData['spec_value'])
		{
			$spceName  = json_decode($goodsData['spec_name'],1);
			$specValue =  json_decode($goodsData['spec_value'],1);	
			foreach($spceName as $specId=>$name)
			{
				$list[$specId]['name'] = $name;
				$list[$specId]['value'] = $specValue[$specId];
			}		
		}	
		return $list;	
	}


	//通过goods_commonid获取一个默认的产品
	public function actionGet_default_goods()
	{
		$goodsCommonid = $this->request['goods_commonid'];
		if(!$goodsCommonid) $this->error('商品不存在');		

		$goods = Goods::getDefaultGoods($goodsCommonid);
		$this->out('产品信息',$goods);
	}


	//获取规格产品
	public function actionGet_spec_goods()
	{
		if(!$specName = $this->request['spec_name']) $this->error('请选择规格');
		if(!$specVal  = $this->request['spec_val']) $this->error('请选择规格');
		if(!$goodsCommonid = $this->request['goods_commonid']) $this->error('商品不存在') ;

		$newSpecName = $newspecVal = array();
		foreach(json_decode($specName,1) as $val)
		{
			foreach($val as $k=>$v){
				$newSpecName[$k] = $v;
			}
		}

		foreach(json_decode($specVal,1) as $val)
		{
			foreach($val as $k=>$v){
				$newspecVal[$k] = $v;
			}
		}		

		$where = [];
		$where['goods_commonid'] = $goodsCommonid;
		$where['spec_name'] = json_encode($newSpecName,JSON_UNESCAPED_UNICODE);
		$where['goods_spec'] = json_encode($newspecVal,JSON_UNESCAPED_UNICODE);
		$goods = Goods::getGoods($where);
		$this->out('产品信息:',$goods);
	}

	/*
		* 收藏产品
		* goods_commonid  商品id
	*/
	public function actionFavorites_goods()
	{
		$params = array(
			'goods_commonid'	=> $this->request('goods_commonid'),
			'member_id'			=> $this->_memberId,
			'member_name' 		=> $this->_member['member_name'],
		);
		$res = Favorites::addFav($params);
		if($res['status']) $this->out($res['msg'].'成功');
		$this->error($res['msg'].'失败');
	}

	/*
		获取收藏列表
	*/
	public function actionFavorites_goods_list()
	{
		$page = $this->request('page',1);
		$data = Favorites::fovaritesList($this->_memberId,$page);

		$this->out('收藏的商品',$data);
	}

	//浏览的商品列表
	public function actionBrowse_goods_list()
	{
		$page = $this->request('page',1);
		$data = GoodsBrowse::browseList($this->_memberId,$page);

		$this->out('收藏的商品',$data);		
	}

	//商品评价列表
	//type 0全部 1好评 2中评 3差评 
	public function actionGoods_evaluate_list()
	{
		if(!$goods_commonId = $this->request('goods_commonid'))
			$this->error('参数错误');
		$type = $this->request('type','0');

		$where = [];
		$where['goods_commonid'] = $goods_commonId;
		if($type=='1') $where['scores'] = array('>=','4');
		if($type=='2') $where['scores'] = '3';
		if($type=='3') $where['scores'] = array('<=','2');

		$whereArr = array();
		$whereArr['where'] = $where;
		$params = array(
			'order' => 'id DESC',
			'page'  => $this->request('page',1),
			'limit'	=> $this->request('page_size',10),
		);
		$data = Evaluate::getGoodsEvaluteList($whereArr,$params);
		foreach($data as &$val){
			$val['eval_image'] = json_decode($val['eval_image'],1);
		}
		$this->out('评价信息',$data);
	}



}