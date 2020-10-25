<?php
namespace wstmart\common\model;
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

class Carts extends Base{
	protected $pk = 'cartId';
	/**
	 * 加入购物车
	 */
	public function addCart($uId = 0){
		$userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
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
		$goods = model('Goods')->where(['goodsStatus'=>1,'dataFlag'=>1,'isSale'=>1,'goodsId'=>$goodsId])->field('goodsId,isSpec,goodsStock,goodsType')->find();
		if(empty($goods))return WSTReturn("添加失败，无效的商品信息", -1);
		$goodsStock = (int)$goods['goodsStock'];
		//有规格的话查询规格是否正确
		if($goods['isSpec']==1){
			$specs = Db::name('goods_specs')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->field('id,isDefault,specStock')->select();
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
	 * 获取session中购物车列表
	 */
	public function getQuickCarts($uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$tmp_carts = session('TMP_CARTS');
		$where = [];
		$where['goodsId'] = $tmp_carts['goodsId'];
		$rs = Db::name('goods')->alias('g')
		           ->join('__SHOPS__ s','s.shopId=g.shopId','left')
		           ->where($where)
		           ->field('s.userId,s.shopId,s.shopName,g.goodsId,s.shopQQ,shopWangWang,g.goodsName,g.shopPrice,g.shopPrice defaultShopPrice,g.goodsStock,g.goodsImg,g.goodsCatId,g.isFreeShipping,s.isInvoice')
		           ->find();
		if(empty($rs))return ['carts'=>[],'goodsTotalMoney'=>0,'goodsTotalNum'=>0]; 
		$rs['cartNum'] = $tmp_carts['cartNum'];
		$carts = [];
		$cartShop = [];
		$goodsTotalNum = 1;
		$goodsTotalMoney = 0;
		//勿删！为插件促销活动做准备接口
		$rs['promotion'] = [];//商品要优惠的活动
		$cartShop['promotion'] = [];//店铺要优惠的活动
		$cartShop['promotionMoney'] = 0;//店铺要优惠的金额
		//---------------------------
		$cartShop['isFreeShipping'] = true;
		$cartShop['shopId'] = $rs['shopId'];
		$cartShop['isInvoice'] = $rs['isInvoice'];
		$cartShop['shopName'] = $rs['shopName'];
		$cartShop['shopQQ'] = $rs['shopQQ'];
		$cartShop['userId'] = $rs['userId'];
		$cartShop['shopWangWang'] = $rs['shopWangWang'];
		//判断能否购买，预设allowBuy值为10，为将来的各种情况预留10个情况值，从0到9
		$rs['allowBuy'] = 10;
		if($rs['goodsStock']<=0){
			$rs['allowBuy'] = 0;//库存不足
		}else if($rs['goodsStock']<$tmp_carts['cartNum']){
			//$rs['allowBuy'] = 1;//库存比购买数小
            $rs['cartNum'] = $rs['goodsStock'];
		}
		$cartShop['goodsMoney'] = $rs['shopPrice'] * $rs['cartNum'];
		$goodsTotalMoney = $goodsTotalMoney + $rs['shopPrice'] * $rs['cartNum'];
		$rs['specNames'] = [];
		$rs['cartId'] = $rs['goodsId'];
		unset($rs['shopName']);
		$cartShop['list'][] = $rs;
		$carts[$cartShop['shopId']] = $cartShop;
		$cartData = ['carts'=>$carts,'shopId'=>$cartShop['shopId'],'goodsTotalMoney'=>$goodsTotalMoney,'goodsTotalNum'=>$goodsTotalNum,'promotionMoney'=>0];
		//店铺优惠活动监听
		hook("afterQueryCarts",["carts"=>&$cartData,'isSettlement'=>true,'isVirtual'=>true,'uId'=>$userId]); 
		return $cartData; 
	}
	
	/**
	 * 获取购物车列表
	 */
	public function getCarts($isSettlement = false, $uId=0){
		$userId = ($uId==0)?(int)session('WST_USER.userId'):$uId;
		$where = [];
		$where['c.userId'] = $userId;
        $prefix = config('database.prefix');
		if($isSettlement)$where['c.isCheck'] = 1;
		$rs = Db::table($prefix.'carts')
		           ->alias([$prefix.'carts'=>'c',$prefix.'goods' => 'g',$prefix.'shops' => 's',$prefix.'goods_specs' => 'gs'])
		           ->join($prefix.'goods','c.goodsId=g.goodsId','inner')
		           ->join($prefix.'shops','s.shopId=g.shopId','left')
		           ->join($prefix.'goods_specs','c.goodsSpecId=gs.id','left')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,s.userId,s.shopId,s.shopName,g.goodsId,g.shippingFeeType,g.shopExpressId,s.shopQQ,shopWangWang,g.goodsName,g.shopPrice,g.shopPrice defaultShopPrice,g.goodsStock,g.goodsWeight,g.goodsVolume,g.isSpec,gs.specPrice,gs.specStock,gs.specWeight,gs.specVolume,g.goodsImg,c.isCheck,gs.specIds,c.cartNum,g.goodsCatId,g.isFreeShipping,s.isInvoice')
		           ->select();		
		$carts = [];
		$goodsIds = [];
		$goodsTotalNum = 0;
		$goodsTotalMoney = 0;
		foreach ($rs as $key =>$v){
			if(!isset($carts[$v['shopId']]['goodsMoney']))$carts[$v['shopId']]['goodsMoney'] = 0;
			if(!isset($carts[$v['shopId']]['isFreeShipping']))$carts[$v['shopId']]['isFreeShipping'] = true;
            //勿删！为插件促销活动做准备接口
			$v['promotion'] = [];//商品优惠活动
			$carts[$v['shopId']]['promotion'] = [];//店铺优惠活动
			$carts[$v['shopId']]['promotionMoney'] = 0;//店铺要优惠的金额
			//----------------------------
			$carts[$v['shopId']]['shopId'] = $v['shopId'];
			$carts[$v['shopId']]['shopName'] = $v['shopName'];
			$carts[$v['shopId']]['shopQQ'] = $v['shopQQ'];
			$carts[$v['shopId']]['userId'] = $v['userId'];
			$carts[$v['shopId']]['isInvoice'] = $v['isInvoice'];
			//如果店铺一旦不包邮了，那么就不用去判断商品是否包邮了
			if($v['isFreeShipping']==0 && $carts[$v['shopId']]['isFreeShipping'])$carts[$v['shopId']]['isFreeShipping'] = false;
			$carts[$v['shopId']]['shopWangWang'] = $v['shopWangWang'];
			if($v['isSpec']==1){
				$v['shopPrice'] = $v['specPrice'];
				$v['defaultShopPrice'] = $v['specPrice'];
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
				$carts[$v['shopId']]['goodsMoney'] = $carts[$v['shopId']]['goodsMoney'] + $v['shopPrice'] * $v['cartNum'];
				$goodsTotalMoney = $goodsTotalMoney + $v['shopPrice'] * $v['cartNum'];
				$goodsTotalNum++;
			}
			$v['specNames'] = [];
			unset($v['shopName']);
			// app端处理
			if($uId>0 && isset($v['goodsName'])){
				$v['goodsName'] = htmlspecialchars_decode($v['goodsName']);
			}

			$carts[$v['shopId']]['list'][] = $v;
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
		}

		//加载规格值
		if(count($goodsIds)>0){
		    $specs = DB::name('spec_items')->alias('s')->join('__SPEC_CATS__ sc','s.catId=sc.catId','left')
		        ->where([['s.goodsId','in',$goodsIds],['s.dataFlag','=',1]])->field('catName,itemId,itemName')->select();
		    if(count($specs)>0){ 
		    	$specMap = [];
		    	foreach ($specs as $key =>$v){
		    		$specMap[$v['itemId']] = $v;
		    	}
			    foreach ($carts as $key =>$shop){
			    	foreach ($shop['list'] as $skey =>$v){
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
		//店铺优惠活动监听
		hook("afterQueryCarts",["carts"=>&$cartData,'isSettlement'=>$isSettlement,'isVirtual'=>false,'uId'=>$userId]); 
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
		$rs = $this->alias('c')->join('__GOODS__ g','c.goodsId=g.goodsId','inner')
		           ->join('__GOODS_SPECS__ gs','c.goodsSpecId=gs.id','left')
		           ->where($where)
		           ->field('c.goodsSpecId,c.cartId,g.goodsId,g.goodsName,g.shopPrice,g.goodsStock,g.isSpec,gs.specPrice,gs.specStock,g.goodsImg,c.isCheck,gs.specIds,c.cartNum')
		           ->select();
		$goodsIds = []; 
		$goodsTotalMoney = 0;
		$goodsTotalNum = 0;
		foreach ($rs as $key =>$v){
			if(!in_array($v['goodsId'],$goodsIds))$goodsIds[] = $v['goodsId'];
			if($v['isSpec']==1){
				$v['shopPrice'] = $v['specPrice'];
				$v['goodsStock'] = $v['specStock'];
			}
			if($v['goodsStock']<$v['cartNum']){
				$v['cartNum'] = $v['goodsStock'];
			}
			$goodsTotalMoney = $goodsTotalMoney + $v['shopPrice'] * $v['cartNum'];
			$rs[$key]['goodsImg'] = WSTImg($v['goodsImg']);
		}
	    //加载规格值
		if(count($goodsIds)>0){
		    $specs = DB::name('spec_items')->alias('s')->join('__SPEC_CATS__ sc','s.catId=sc.catId','left')
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
		$data = ['shops'=>[],'totalMoney'=>0,'totalGoodsMoney'=>0,'orderScore'=>0];
        $areaId = input('post.areaId2/d',-1);
		//计算各店铺运费及金额
		
		$carts = $this->getCarts(true,$uId);
		foreach ($carts['carts'] as $key =>$v){
			$shopFreight = 0;
			if($v['isFreeShipping']){
                $data['shops'][$v['shopId']]['freight'] = 0;
			}else{
				$deliverType = (int)input('deliverType_'.$v['shopId']);
				if($areaId>0){
					$shopFreight = ($deliverType==1)?0:WSTOrderFreight($v['shopId'],$areaId,$v);
				}else{
					$shopFreight = 0;
				}
                $data['shops'][$v['shopId']]['freight'] = $shopFreight;
			}
			$data['shops'][$v['shopId']]['oldGoodsMoney'] = $v['goodsMoney'];
			$data['shops'][$v['shopId']]['goodsMoney'] = $v['goodsMoney']+$shopFreight-$v['promotionMoney'];
			$data['shops'][$v['shopId']]['orderScore'] = WSTMoneyGiftScore($data['shops'][$v['shopId']]['goodsMoney']);
			$data['totalGoodsMoney'] += $v['goodsMoney']-$v['promotionMoney'];
			$data['totalMoney'] += $v['goodsMoney'] + $shopFreight-$v['promotionMoney'];
			$data['orderScore'] += $data['shops'][$v['shopId']]['orderScore'];
		}
		//此处放钩子计算商家使用优惠券后的金额-根据优惠券ID计算
		hook("afterCalculateCartMoney",["data"=>&$data,'carts'=>$carts,'isVirtual'=>false,'uId'=>$uId]);
		$data['totalGoodsMoney'] = ($data['totalGoodsMoney']>$data['totalMoney'])?$data['totalMoney']:$data['totalGoodsMoney'];
		$data['maxScore'] = 0;
		$data['maxScoreMoney'] = 0;
		$data['useScore'] = 0;
		$data['scoreMoney'] = 0;
		//计算最大可用积分
		$maxScoreMoney = $data['totalGoodsMoney'];
		$maxScore = WSTScoreToMoney($data['totalGoodsMoney'],true);
		//最大可用积分不能大于用户积分
        $userId = $uId==0?(int)session('WST_USER.userId'):$uId;
		$user = model('users')->getFieldsById($userId,'userScore');
		if($maxScore>$user['userScore']){
			$maxScore = $user['userScore'];
			$maxScoreMoney = WSTScoreToMoney($maxScore);
		}
		$data['maxScore'] = $maxScore;
		$data['maxScoreMoney'] = $maxScoreMoney;
        //判断是否使用积分
		$isUseScore = (int)input('isUseScore');
		if($isUseScore==1){
			//不能比用户积分还多
			$useScore = (int)input('useScore');
			if($useScore>$maxScore)$useScore = $maxScore;
			$data['useScore'] = $useScore;
            $data['scoreMoney'] = WSTScoreToMoney($useScore);
		}
		$data['realTotalMoney'] = WSTPositiveNum($data['totalMoney'] - $data['scoreMoney']);
		return WSTReturn('',1,$data);
	}

	public function getQuickCartMoney($uId=0){
		$data = ['shops'=>[],'totalMoney'=>0,'totalGoodsMoney'=>0,'orderScore'=>0];
        $areaId = input('post.areaId2/d',-1);
		//计算各店铺运费及金额
		$carts = $this->getQuickCarts($uId);
		$cart = current($carts['carts']);
		$data['shops'][$cart['shopId']]['freight'] = 0;
		$data['shops'][$cart['shopId']]['goodsMoney'] = $cart['goodsMoney'];
		$data['shops'][$cart['shopId']]['orderScore'] = WSTMoneyGiftScore($cart['goodsMoney']);
		$data['totalGoodsMoney'] = $cart['goodsMoney'];
		$data['totalMoney'] += $cart['goodsMoney'];
		$data['orderScore'] += WSTMoneyGiftScore($cart['goodsMoney']);
		//此处放钩子计算商家使用优惠券后的金额-根据优惠券ID计算
		hook("afterCalculateCartMoney",["data"=>&$data,'carts'=>$carts,'isVirtual'=>true,'uId'=>$uId]);
		$data['totalGoodsMoney'] = ($data['totalGoodsMoney']>$data['totalMoney'])?$data['totalMoney']:$data['totalGoodsMoney'];
        $data['maxScore'] = 0;
		$data['maxScoreMoney'] = 0;
		$data['useScore'] = 0;
		$data['scoreMoney'] = 0;

		//计算最大可用积分
		$maxScoreMoney = $data['totalGoodsMoney'];
		$maxScore = WSTScoreToMoney($data['totalGoodsMoney'],true);
		//最大可用积分不能大于用户积分
        $userId = $uId==0?(int)session('WST_USER.userId'):$uId;
		$user = model('users')->getFieldsById($userId,'userScore');
		if($maxScore>$user['userScore']){
			$maxScore = $user['userScore'];
			$maxScoreMoney = WSTScoreToMoney($maxScore,true);
		}
		$data['maxScore'] = $maxScore;
		$data['maxScoreMoney'] = $maxScoreMoney;
        //判断是否使用积分
		$isUseScore = (int)input('isUseScore');
		if($isUseScore==1){
			//不能比用户积分还多
			$useScore = (int)input('useScore');
			if($useScore>$maxScore)$useScore = $maxScore;
			$data['useScore'] = $useScore;
            $data['scoreMoney'] = WSTScoreToMoney($useScore);
		}
		$data['realTotalMoney'] = WSTPositiveNum($data['totalMoney'] - $data['scoreMoney']);
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
    public function getShopFreight($shopId,$cityId,$carts=[]){
    
        $calculatePrice = 0;
        if(isset($carts['list'])){
        	foreach ($carts['list'] as $key => $goods) {
	        	$shopExpressId = (int)$goods["shopExpressId"];
		        $shippingFeeType = (int)$goods["shippingFeeType"];
		        $where = [];
		        $where[] = ["shopId",'=',$shopId];
		        $where[] = ["shopExpressId",'=',$shopExpressId];
		        $where[] = ["tempType",'=',1];
		        $where[] = ["dataFlag",'=',1];
		        $freightTemp = Db::name("shop_freight_template")->where($where)->where("FIND_IN_SET(".$cityId.",cityIds)")->find();
		       	if(empty($freightTemp)){
		       		$where = [];
			        $where[] = ["shopId",'=',$shopId];
			        $where[] = ["shopExpressId",'=',$shopExpressId];
		       		$where[] = ["tempType",'=',0];
		       		$where[] = ["dataFlag",'=',1];
		       		$freightTemp = Db::name("shop_freight_template")->where($where)->find();
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
     * 将购物车里选择的商品移入我的关注
     */
    public function moveToFavorites($uId = 0){
        $userId = ($uId>0)?$uId:(int)session('WST_USER.userId');
        $goodsIds = input('post.goodsIds');
        $goodsIds = explode(',',WSTFormatIn(",",$goodsIds));
        $goodsIds = array_filter($goodsIds);
        $cartIds = input('post.cartIds');
        $cartIds = explode(',',WSTFormatIn(",",$cartIds));
        $cartIds = array_filter($cartIds);
        Db::startTrans();
        try{
            for($i=0;$i<count($goodsIds);$i++){
                $favoriteId = Db::name('favorites')->where(['userId'=>$userId,'favoriteType'=>0,'targetId'=>$goodsIds[$i]])->value('favoriteId');
                if(empty($favoriteId)){
                    $data = [
                        'userId'=>$userId,
                        'favoriteType'=>0,
                        'targetId'=>$goodsIds[$i],
                        'createTime'=>date('Y-m-d H:i:s')
                    ];
                    Db::name('favorites')->insert($data);
                }
            }
            $this->where("userId = ".$userId." and cartId in(".implode(',', $cartIds).")")->delete();
            Db::commit();
            return WSTReturn("关注成功", 1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('关注失败',-1);
        }
    }
}
