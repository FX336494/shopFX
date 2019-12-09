<?php
/*
	商品SKU 模型
*/
namespace apiadmin\modules\models\goods;
use Yii;
use common\models\goods\GoodsModel;

class Goods extends GoodsModel
{
    //保存商品
    public function saveGoods($data,$specValueArr)
    {
    	try{
	    	$goodsData = $this->_initGoodsData($data);
	    	$goodsIds  = [];
	    	if($specValueArr)
	    	{
				foreach($specValueArr as $k=>$specValue)
				{
					$goodsModel = clone $this;
					$data = $goodsData;
					$data['color_id'] = 0;
					$specValTmp = array();
					foreach($specValue as $val)
					{
						if($val['spec_id']){
							$specValTmp[$val['val_id']] = $val['spec_val'];
						}
						// var_dump($val);
						if($val['spec_id']==1){  //系统默认1为颜色规格
							$data['color_id'] = (int)$val['val_id'];
						}							
						if($val['price']){
							$data['goods_price'] = $val['price'];
						}
						if($val['storage']){
							$data['goods_storage'] = $val['storage'];
						}		
	
					}
					$valueName = implode(' ',array_values($specValTmp));
					$data['goods_name'].= ' '.$valueName;
					$data['goods_spec'] = json_encode($specValTmp,JSON_UNESCAPED_UNICODE);
					//是否已经存在
					$where = ['goods_commonid'=>$data['goods_commonid'],'goods_spec'=>$data['goods_spec']];
					$oldGoods = self::getGoods($where,['goods_id']);
					if($oldGoods){
						$goodsModel->goods_id  = $oldGoods['goods_id'];
						$goodsModel->isNewRecord = false;
						$goodsIds[] = $oldGoods['goods_id'];
					}else{
						$goodsModel->isNewRecord = true; 
						$goodsModel->goods_id = 0;						
						$goodsModel->create_time = time();
					}
					if(!$goodsModel->load($data,'') || !$goodsModel->validate() || !$goodsModel->save(false)){
						$errors = $this->getErrors();
						$error =  self::outError($errors);
						throw new \Exception($error['msg']);
					}
				}
			}else
			{
				//是否已经存在
				$where = ['goods_commonid'=>$goodsData['goods_commonid']];

				$oldGoods = self::getGoods($where,['goods_id']);

				if($oldGoods){
					$goodsModel = self::findOne($oldGoods['goods_id']);
				}else{
					$goodsModel = $this;
					$goodsModel->goods_id = 0;	
					$goodsData['create_time'] = time();
				}

				$goodsData['color_id'] = 0;
				$goodsData['goods_spec'] = json_encode(array());
				if(!$goodsModel->load($goodsData,'') || !$goodsModel->validate() || !$goodsModel->save(false)){
					$errors = $this->getErrors();
					$error =  self::outError($errors);
					throw new \Exception($error['msg']);
				}else{
					if(!$goodsModel->goods_id){
						$goodsId = Yii::$app->db->getLastInsertID();
						$goodsIds[] = $goodsId;
					}
				}				
			}

			//删除不存在的规格
			if($goodsIds){
				$where = ['and',['goods_commonid'=>$goodsData['goods_commonid']],['not in', 'goods_id',$goodsIds]];
				$this->deleteAll($where);  
			}

			return array('state'=>true,'msg'=>'ok');	
		}catch(\Exception $e){
			$errMsg = $e->getMessage();
			return array('state'=>false,'msg'=>$errMsg);
		}

    }

    //初始化商品信息
    private function _initGoodsData($commonGoods)
    {
   		$goodsData = array();
   		$goodsData['goods_commonid']		= $commonGoods['goods_commonid'];
   		$goodsData['goods_name']	 		= $commonGoods['goods_name'];
   		$goodsData['gc_id1'] 		 		= $commonGoods['gc_id1'];
   		$goodsData['gc_id2'] 		 		= $commonGoods['gc_id2'];
   		$goodsData['gc_id3'] 		 		= $commonGoods['gc_id3'];
   		$goodsData['gc_id'] 		 		= $commonGoods['gc_id'];
   		$goodsData['goods_price'] 			= $commonGoods['goods_price'];
   		$goodsData['goods_costprice']		= $commonGoods['goods_costprice'];
   		$goodsData['goods_marketprice'] 	= $commonGoods['goods_marketprice'];
   		$goodsData['spec_name'] 			= $commonGoods['spec_name'];
   		$goodsData['goods_storage']         = $commonGoods['goods_storage'];
   		$goodsData['goods_storage_alarm'] 	= $commonGoods['goods_storage_alarm'];
   		$goodsData['goods_image'] 			= $commonGoods['goods_image'];
   		$goodsData['goods_edittime'] 		= time();   
   		$goodsData['goods_state'] 			= $commonGoods['goods_state'];  
   		$goodsData['goods_freight'] 		= $commonGoods['goods_freight']; 
   		$goodsData['goods_commend'] 		= $commonGoods['goods_commend'];

   		return $goodsData;
    }    

    //组装商品的规格 (用于商品修改)
    public static function formatSpecVal($goodsCommonId,$specName,$specValue)
    {
    	$where = ['goods_commonid'=>$goodsCommonId];
    	$field = ['goods_id','spec_name','goods_spec','goods_price','goods_storage'];
    	$data  = self::find()->select($field)->where($where)->asarray()->all();

    	//规格值 对应规格
    	$specValueArr = array();
    	foreach($specValue as $specId=>$valueArr)
    	{
    		foreach($valueArr as $valId=>$val){
    			$specValueArr[$valId] = $specId;
    		}
    	}

    	$checkSpecValArr = array();
    	foreach($data as $val)
    	{
    		$goodsSpec  = json_decode($val['goods_spec'],1);
    		$goodsSpecList = array();
    		foreach($goodsSpec as $valId=>$nameVal)
    		{
    			$tmp = array();
    			$tmp['val_id']  = (String)$valId;
    			$tmp['spec_val'] = $nameVal;
    			$tmp['spec_id'] = (String)$specValueArr[$valId];
    			$goodsSpecList[] = $tmp;
    		}
    		$goodsSpecList[] = array('price'=>$val['goods_price']);
    		$goodsSpecList[] = array('storage'=>$val['goods_storage']);

    		$checkSpecValArr[] = $goodsSpecList;
    	}
    	return $checkSpecValArr;
    }


}