<?php
namespace wstmart\shop\model;
use think\Db;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtao.net
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 购物车业务处理类
 */

class SupplierCarts extends Base{
	protected $pk = 'cartId';
	/**
	 * 加入购物车
	 */
	public function addCart($uId = 0){
		$userId = (int)session('WST_USER.userId');
		$goodsId = (int)input('post.goodsId');
		$goodsSpecId = (int)input('post.goodsSpecId');
		$cartNum = (int)input('post.buyNum',1);
		$cartNum = ($cartNum>0)?$cartNum:1;
		$type = (int)input('post.type');
		if($userId==0)return WSTReturn('加入购物车失败，请先登录',-2);
		//验证传过来的商品是否合法
		$chk = $this->checkGoodsSaleSpec($goodsId,$goodsSpecId);
		if($chk['status']==-1)return $chk;
		//检测库存是否足够
		if($chk['data']['stock']<$cartNum)return WSTReturn("加入购物车失败，商品库存不足", -1);
		//添加实物商品
		if($chk['data']['goodsType']==0){
			$goodsSpecId = $chk['data']['goodsSpecId'];
			$goods = $this->where(['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->select();
			if(count($goods)==0){
				$data = array();
				$data['userId'] = $userId;
				$data['goodsId'] = $goodsId;
				$data['goodsSpecId'] = $goodsSpecId;
				$data['isCheck'] = 1;
				$data['cartNum'] = $cartNum;
				$rs = $this->save($data);
			}else{
				$rs = $this->where(['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->setInc('cartNum',$cartNum);
			}
			if(false !==$rs){
				if($type==1){
					$cartId = $this->where(['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId])->value('cartId');
					$this->where("cartId = ".$cartId." and userId=".$userId)->setField('isCheck',1);
					$this->where("cartId != ".$cartId." and userId=".$userId)->setField('isCheck',0);
					$this->where(['cartId' =>$cartId,'userId'=>$userId])->setField('cartNum',$cartNum);
				}
				return WSTReturn("添加成功", 1);
			}
		}else{
			//非实物商品
            $carts = [];
            $carts['goodsId'] = $goodsId;
            $carts['cartNum'] = $cartNum;
            session('TMP_CARTS',$carts);
            return WSTReturn("添加成功", 1,['forward'=>'quickSettlement']);
		}
		return WSTReturn("加入购物车失败", -1);
	}
	/**
	 * 验证商品是否合法
	 */
	public function checkGoodsSaleSpec($goodsId,$goodsSpecId){
		$goods = Db::name("supplier_goods")->where(['goodsStatus'=>1,'dataFlag'=>1,'isSale'=>1,'goodsId'=>$goodsId])->field('goodsId,isSpec,goodsStock,goodsType')->find();
		if(empty($goods))return WSTReturn("添加失败，无效的商品信息", -1);
		$goodsStock = (int)$goods['goodsStock'];
		//有规格的话查询规格是否正确
		if($goods['isSpec']==1){
			$specs = Db::name('supplier_goods_specs')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->field('id,isDefault,specStock')->select();
			if(count($specs)==0){
				return WSTReturn("添加失败，无效的商品信息", -1);
			}
			$defaultGoodsSpecId = 0;
			$defaultGoodsSpecStock = 0;
			$isFindSpecId = false;
			foreach ($specs as $key => $v){
				if($v['isDefault']==1){
					$defaultGoodsSpecId = $v['id'];
					$defaultGoodsSpecStock = (int)$v['specStock'];
				}
				if($v['id']==$goodsSpecId){
					$goodsStock = (int)$v['specStock'];
					$isFindSpecId = true;
				}
			}
			
			if($defaultGoodsSpecId==0)return WSTReturn("添加失败，无效的商品信息", -1);//有规格却找不到规格的话就报错
			if(!$isFindSpecId)return WSTReturn("", 1,['goodsSpecId'=>$defaultGoodsSpecId,'stock'=>$defaultGoodsSpecStock,'goodsType'=>$goods['goodsType']]);//如果没有找到的话就取默认的规格
			return WSTReturn("", 1,['goodsSpecId'=>$goodsSpecId,'stock'=>$goodsStock,'goodsType'=>$goods['goodsType']]);
		}else{
			return WSTReturn("", 1,['goodsSpecId'=>0,'stock'=>$goodsStock,'goodsType'=>$goods['goodsType']]);
		}
	}
	/**
	 * 删除购物车里的商品
	 */
	public function delCart($uId = 0){
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
		$id = input('post.id');
		$id = explode(',',WSTFormatIn(",",$id));
		$id = array_filter($id);
		$this->where("userId = ".$userId." and cartId in(".implode(',', $id).")")->delete();
		return WSTReturn("删除成功", 1);
	}
	/**
	 * 取消购物车商品选中状态
	 */
	public function disChkGoods($goodsId,$goodsSpecId,$userId){
		$this->save(['isCheck'=>0],['userId'=>$userId,'goodsId'=>$goodsId,'goodsSpecId'=>$goodsSpecId]);
	}

	/**
	 * 获取购物车列表
	 */
	public function getCarts($isSettlement = false, $uId=0){
		$userId = (int)session('WST_USER.userId');
		$where = [];
		$where['c.userId'] = $userId;
        $prefix = config('database.prefix');
		if($isSettlement)$where['c.isCheck'] = 1;
		$rs = Db::table($prefix.'supplier_carts')
		           ->alias([$prefix.'supplier_carts'=>'c',$prefix.'supplier_goods' => 'g',$prefix.'suppliers' => 's',$prefix.'supplier_goods_specs' => 'gs'])
		           ->join($prefix.'supplier_goods','c.goodsId=g.goodsId','inner')
		           ->join($prefix.'suppliers','s.supplierId=g.supplierId','left')
		           ->join($prefix.'supplier_goods_specs','c.goodsSpecId=gs.id','left')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,s.userId,s.supplierId,s.supplierName,g.goodsId,g.shippingFeeType,g.supplierExpressId,s.supplierQQ,supplierWangWang,g.goodsName,g.supplierPrice,g.supplierPrice defaultSupplierPrice,g.goodsStock,g.goodsWeight,g.goodsVolume,g.isSpec,gs.specPrice,gs.specStock,gs.specWeight,gs.specVolume,g.goodsImg,c.isCheck,gs.specIds,c.cartNum,g.goodsCatId,g.isFreeShipping,s.isInvoice')
		           ->select();		
		$carts = [];
		$goodsIds = [];
		$goodsTotalNum = 0;
		$goodsTotalMoney = 0;
		foreach ($rs as $key =>$v){
			if(!isset($carts[$v['supplierId']]['goodsMoney']))$carts[$v['supplierId']]['goodsMoney'] = 0;
			if(!isset($carts[$v['supplierId']]['isFreeShipping']))$carts[$v['supplierId']]['isFreeShipping'] = true;
            //勿删！为插件促销活动做准备接口
			$v['promotion'] = [];//商品优惠活动
			$carts[$v['supplierId']]['promotion'] = [];//店铺优惠活动
			$carts[$v['supplierId']]['promotionMoney'] = 0;//店铺要优惠的金额
			//----------------------------
			$carts[$v['supplierId']]['supplierId'] = $v['supplierId'];
			$carts[$v['supplierId']]['supplierName'] = $v['supplierName'];
			$carts[$v['supplierId']]['supplierQQ'] = $v['supplierQQ'];
			$carts[$v['supplierId']]['userId'] = $v['userId'];
			$carts[$v['supplierId']]['isInvoice'] = $v['isInvoice'];
			//如果店铺一旦不包邮了，那么就不用去判断商品是否包邮了
			if($v['isFreeShipping']==0 && $carts[$v['supplierId']]['isFreeShipping'])$carts[$v['supplierId']]['isFreeShipping'] = false;
			$carts[$v['supplierId']]['supplierWangWang'] = $v['supplierWangWang'];
			if($v['isSpec']==1){
				$v['supplierPrice'] = $v['specPrice'];
				$v['defaultSupplierPrice'] = $v['specPrice'];
				$v['goodsStock'] = $v['specStock'];
				$v['goodsWeight'] = $v['specWeight'];
				$v['goodsVolume'] = $v['specVolume'];
			}
			//判断能否购买，预设allowBuy值为10，为将来的各种情况预留10个情况值，从0到9
			$v['allowBuy'] = 10;
			if($v['goodsStock']<=0){
				$v['allowBuy'] = 0;//库存不足
			}else if($v['goodsStock']<$v['cartNum']){
				//$v['allowBuy'] = 1;//库存比购买数小
				$v['cartNum'] = $v['goodsStock'];
			}
			//如果是结算的话，则要过滤了不符合条件的商品
			if($isSettlement && $v['allowBuy']!=10){
				$this->disChkGoods($v['goodsId'],(int)$v['goodsSpecId'],(int)session('WST_USER.userId'));
				continue;
			}
			if($v['isCheck']==1){
				$carts[$v['supplierId']]['goodsMoney'] = $carts[$v['supplierId']]['goodsMoney'] + $v['supplierPrice'] * $v['cartNum'];
				$goodsTotalMoney = $goodsTotalMoney + $v['supplierPrice'] * $v['cartNum'];
				$goodsTotalNum++;
			}
			$v['specNames'] = [];
			unset($v['supplierName']);
			// app端处理
			if($uId>0 && isset($v['goodsName'])){
				$v['goodsName'] = htmlspecialchars_decode($v['goodsName']);
			}

			$carts[$v['supplierId']]['list'][] = $v;
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
		}

		//加载规格值
		if(count($goodsIds)>0){
		    $specs = DB::name('supplier_spec_items')->alias('s')->join('__SPEC_CATS__ sc','s.catId=sc.catId','left')
		        ->where([['s.goodsId','in',$goodsIds],['s.dataFlag','=',1]])->field('catName,itemId,itemName')->select();
		    if(count($specs)>0){ 
		    	$specMap = [];
		    	foreach ($specs as $key =>$v){
		    		$specMap[$v['itemId']] = $v;
		    	}
			    foreach ($carts as $key =>$supplier){
			    	foreach ($supplier['list'] as $skey =>$v){
			    		$strName = [];
			    		if($v['specIds']!=''){
			    			$str = explode(':',$v['specIds']);
			    			foreach ($str as $vv){
			    				if(isset($specMap[$vv]))$strName[] = $specMap[$vv];
			    			}
			    		}
			    		$carts[$key]['list'][$skey]['specNames'] = $strName;
			    	}
			    }
		    }
		}
		//过滤无效店铺
		foreach($carts as $key => $v){
            if(!isset($v['list']))unset($carts[$key]);
		}
		$cartData = ['carts'=>$carts,'goodsTotalMoney'=>$goodsTotalMoney,'goodsTotalNum'=>$goodsTotalNum,'promotionMoney'=>0]; 
		$this->resetCarts(["carts"=>&$cartData,'isSettlement'=>$isSettlement,'isVirtual'=>false,'uId'=>$userId]);

		return $cartData;   
	}
	
	/**
	 * 获取购物车商品列表
	 */
	public function getCartInfo($isSettlement = false,$uId = 0){
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
		$where = [];
		$where['c.userId'] = $userId;
		if($isSettlement)$where['c.isCheck'] = 1;
		$rs = $this->alias('c')->join('supplier_goods g','c.goodsId=g.goodsId','inner')
		           ->join('supplier_goods_specs gs','c.goodsSpecId=gs.id','left')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,g.goodsId,g.goodsName,g.supplierPrice,g.goodsStock,g.isSpec,gs.specPrice,gs.specStock,g.goodsImg,c.isCheck,gs.specIds,c.cartNum')
		           ->select();
		$goodsIds = []; 
		$goodsTotalMoney = 0;
		$goodsTotalNum = 0;
		foreach ($rs as $key =>$v){
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
			if($v['isSpec']==1){
				$v['supplierPrice'] = $v['specPrice'];
				$v['goodsStock'] = $v['specStock'];
			}
			if($v['goodsStock']<$v['cartNum']){
				$v['cartNum'] = $v['goodsStock'];
			}
			$goodsTotalMoney = $goodsTotalMoney + $v['supplierPrice'] * $v['cartNum'];
			$rs[$key]['goodsImg'] = WSTImg($v['goodsImg']);
		}
	    //加载规格值
		if(count($goodsIds)>0){
		    $specs = DB::name('supplier_spec_items')->alias('s')->join('__SPEC_CATS__ sc','s.catId=sc.catId','left')
		        ->where([['s.goodsId','in',$goodsIds],['s.dataFlag','=',1]])->field('itemId,itemName')->select();
		    if(count($specs)>0){
		    	$specMap = [];
		    	foreach ($specs as $key =>$v){
		    		$specMap[$v['itemId']] = $v;
		    	}
			    foreach ($rs as $key =>$v){
			    	$strName = [];
			    	if($v['specIds']!=''){
			    		$str = explode(':',$v['specIds']);
			    		foreach ($str as $vv){
			    			if(isset($specMap[$vv]))$strName[] = $specMap[$vv]['itemName'];
			    		}
			    	}
			    	$rs[$key]['specNames'] = $strName;
			    }
		    }
		}
		$goodsTotalNum = count($rs);
		return ['list'=>$rs,'goodsTotalMoney'=>sprintf("%.2f", $goodsTotalMoney),'goodsTotalNum'=>$goodsTotalNum];
	}
	
	/**
	 * 修改购物车商品状态
	 */
	public function changeCartGoods($uId = 0){
		$isCheck = Input('post.isCheck/d',-1);
		$buyNum = Input('post.buyNum/d',1);
		if($buyNum<1)$buyNum = 1;
		$id = Input('post.id/d');
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
		$data = [];
		if($isCheck!=-1)$data['isCheck'] = $isCheck;
		$data['cartNum'] = $buyNum;
		$this->where(['userId'=>$userId,'cartId'=>$id])->update($data);
		return WSTReturn("操作成功", 1);
	}

	/**
	 * 批量修改购物车商品状态
	 */
	public function batchChangeCartGoods($uId = 0){
		$ids = input('ids');
		if($ids=='')return WSTReturn("操作失败");
        $ids = explode(',',WSTFormatIn(',',$ids));
        $userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
        $isCheck = ((int)input('post.isCheck/d',-1)==1)?1:0;
        $this->where([['cartId','in',$ids],['userId','=',$userId]])->update(['isCheck'=>$isCheck]);
		return WSTReturn("操作成功", 1);
	}
	/**
	 * 计算订单金额
	 */
	public function getCartMoney($uId=0){
		$data = ['suppliers'=>[],'totalMoney'=>0,'totalGoodsMoney'=>0];
        $areaId = input('post.areaId2/d',-1);
		//计算各店铺运费及金额
		
		$carts = $this->getCarts(true,$uId);
		foreach ($carts['carts'] as $key =>$v){
			$supplierFreight = 0;
			if($v['isFreeShipping']){
                $data['suppliers'][$v['supplierId']]['freight'] = 0;
			}else{
				$deliverType = (int)input('deliverType_'.$v['supplierId']);
				if($areaId>0){
					$supplierFreight = ($deliverType==1)?0:model("supplierOrders")->getOrderFreight($v['supplierId'],$areaId,$v);
				}else{
					$supplierFreight = 0;
				}
                $data['suppliers'][$v['supplierId']]['freight'] = $supplierFreight;
			}
			$data['suppliers'][$v['supplierId']]['oldGoodsMoney'] = $v['goodsMoney'];
			$data['suppliers'][$v['supplierId']]['goodsMoney'] = $v['goodsMoney']+$supplierFreight-$v['promotionMoney'];
			$data['totalGoodsMoney'] += $v['goodsMoney']-$v['promotionMoney'];
			$data['totalMoney'] += $v['goodsMoney'] + $supplierFreight-$v['promotionMoney'];
		}

		$data['totalGoodsMoney'] = ($data['totalGoodsMoney']>$data['totalMoney'])?$data['totalMoney']:$data['totalGoodsMoney'];
		
		$data['realTotalMoney'] = WSTPositiveNum($data['totalMoney']);
		return WSTReturn('',1,$data);
	}


	/**
	 * 删除购物车商品
	 */
	public function delCartByUpdate($goodsId){
		if(is_array($goodsId)){
            $this->where([['goodsId','in',$goodsId]])->delete();
		}else{
			$this->where('goodsId',$goodsId)->delete();
		}
		
	}


	/**
	 * 计算运费价格
	 */
    public function getSupplierFreight($supplierId,$cityId,$carts=[]){
    
        $calculatePrice = 0;
        if(isset($carts['list'])){
        	foreach ($carts['list'] as $key => $goods) {
	        	$supplierExpressId = (int)$goods["supplierExpressId"];
		        $shippingFeeType = (int)$goods["shippingFeeType"];
		        $where = [];
		        $where[] = ["supplierId",'=',$supplierId];
		        $where[] = ["supplierExpressId",'=',$supplierExpressId];
		        $where[] = ["tempType",'=',1];
		        $where[] = ["dataFlag",'=',1];
		        $freightTemp = Db::name("supplier_freight_template")->where($where)->where("FIND_IN_SET(".$cityId.",cityIds)")->find();
		       	if(empty($freightTemp)){
		       		$where = [];
			        $where[] = ["supplierId",'=',$supplierId];
			        $where[] = ["supplierExpressId",'=',$supplierExpressId];
		       		$where[] = ["tempType",'=',0];
		       		$where[] = ["dataFlag",'=',1];
		       		$freightTemp = Db::name("supplier_freight_template")->where($where)->find();
		       	}
		       	$cartNum = (int)$goods['cartNum'];
		       	if($shippingFeeType==1){//计件
		       		$buyNumStart = (int)$freightTemp["buyNumStart"];
			       	$buyNumStartPrice = $freightTemp["buyNumStartPrice"];
			       	$buyNumContinue = (int)$freightTemp["buyNumContinue"];
			       	$buyNumContinuePrice = $freightTemp["buyNumContinuePrice"];
			       	
			       	if($cartNum>$buyNumStart){
			       		$moreBuyNum = $cartNum-$buyNumStart;
			       		$times = 0;
			       		if($buyNumContinue>0){
			       			$times = ceil($moreBuyNum/$buyNumContinue);
			       		}
			       		$calculatePrice += $buyNumStartPrice + $buyNumContinuePrice*$times;
			       	}else{
			       		$calculatePrice += $buyNumStartPrice;
			       	}
		       	}else if($shippingFeeType==2){//重量
		       		$weightStart = (float)$freightTemp["weightStart"];
			       	$weightStartPrice = (float)$freightTemp["weightStartPrice"];
			       	$weightContinue = (float)$freightTemp["weightContinue"];
			       	$weightContinuePrice = (float)$freightTemp["weightContinuePrice"];
			       	$goodsWeight = (float)$goods['goodsWeight']*$cartNum;
			       	if($goodsWeight>$weightStart){
			       		$moreWeight = $goodsWeight-$weightStart;
			       		$times = 0;
			       		if($weightContinue>0){
			       			$times = ceil($moreWeight/$weightContinue);
			       		}
			       		$calculatePrice += $weightStartPrice + $weightContinuePrice*$times;
			       	}else{
			       		$calculatePrice += $weightStartPrice;
			       	}
		       	}else if($shippingFeeType==3){//体积
		       		$volumeStart = (float)$freightTemp["volumeStart"];
			       	$volumeStartPrice = (float)$freightTemp["volumeStartPrice"];
			       	$volumeContinue = (float)$freightTemp["volumeContinue"];
			       	$volumeContinuePrice = (float)$freightTemp["volumeContinuePrice"];
		       		$goodsVolume = (float)$goods['goodsVolume']*$cartNum;
			       	if($goodsVolume>$volumeStart){
			       		$moreVolume = $goodsVolume-$volumeStart;
			       		$times = 0;
			       		if($volumeContinue>0){
			       			$times = ceil($moreVolume/$volumeContinue);
			       		}
			       		$calculatePrice += $volumeStartPrice + $volumeContinuePrice*$times;
			       	}else{
			       		$calculatePrice += $volumeStartPrice;
			       	}
		       	}
	        }
        }
        
        return WSTBCMoney($calculatePrice,0);
    }

    /**
	 * 购物车
	 */
	public function resetCarts($params){
		$carts = $params['carts']['carts'];
		$params['carts']['goodsTotalMoney'] = 0;
		foreach ($carts as $key => $v) {
			$goodsMoney = 0;
			foreach ($carts[$key]['list'] as $gkey => $gv) {
				$goods = $this->getWholesaleGoods($gv['goodsId']);
				if($goods['isWholesale']==1){
					foreach ($goods['wholesale'] as $pfkey => $pfv) {
						if($pfv['buyNum']<=$gv['cartNum']){
							$carts[$key]['list'][$gkey]['wholesalePrice'] = $gv['supplierPrice'];
							$carts[$key]['list'][$gkey]['wholesalePrice'] = $gv['supplierPrice']-$pfv['rebate'];
						}
					}
				}
				$params['carts']['carts'][$key]['list'][$gkey]['wholesaleGoods'] = $goods;
				if(isset($carts[$key]['list'][$gkey]['wholesalePrice'])){
					$params['carts']['carts'][$key]['list'][$gkey]['supplierPrice'] = $carts[$key]['list'][$gkey]['wholesalePrice'];
				}
				$goodsMoney += round($params['carts']['carts'][$key]['list'][$gkey]['supplierPrice']* $params['carts']['carts'][$key]['list'][$gkey]['cartNum'],2);
			}
			$params['carts']['carts'][$key]['goodsMoney'] = $goodsMoney;
			$params['carts']['goodsTotalMoney'] += $goodsMoney;
		}
		return $params;
	}

	/**
	 * 获取商品批发情况
	 */
	public function getWholesaleGoods($goodsId){
		$goods = Db::name('supplier_goods')->where('goodsId',$goodsId)->field('goodsId,isWholesale,supplierPrice,goodsUnit')->find();
		if($goods['isWholesale']==1){
	        $goods['wholesale']  = Db::name('supplier_wholesale_goods')->where('goodsId',$goodsId)->order('buyNum asc')->select();
	        foreach ($goods['wholesale'] as $key => $v) {
	        	$goods['wholesale'][$key]['goodsPrice'] = $goods['supplierPrice'] - $v['rebate'];
	        }
	    }else{
	    	$goods = ['wholesale'=>[],'goodsId'=>0,'isWholesale'=>0];
	    }
	    return $goods;
	}
}
