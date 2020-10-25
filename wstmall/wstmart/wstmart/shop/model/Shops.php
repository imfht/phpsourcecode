<?php
namespace wstmart\shop\model;
use wstmart\common\model\Shops as CShops;
use wstmart\shop\validate\Shops as VShop;
use think\Db;
use think\Loader;
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
 * 门店类
 */
class Shops extends CShops{
   
    /**
    * 获取店铺公告
    */
    public function getNotice(){
        $shopId = (int)session('WST_USER.shopId');
        return model('shops')->where(['shopId'=>$shopId])->value('shopNotice');
    }
    
    
    /**
     * 店铺街列表
     */
    public function pageQuery($pagesize){
    	$catId = input("get.id/d");
    	$keyword = input("keyword");
        $location = WSTIpLocation();
    	$userId = (int)session('WST_USER.userId');
    	$rs = $this->alias('s');
    	$where = [];
    	$where[] = ['s.dataFlag','=',1];
        $where[] = ['s.shopStatus','=',1];
    	$where[] = ['s.applyStatus','=',2];
    	if($keyword!='')$where[] = ['s.shopName','like','%'.$keyword.'%'];
    	if($catId>0){
    		$rs->join('__CAT_SHOPS__ cs','cs.shopId = s.shopId','left');
    		$where[] = ['cs.catId','=',$catId];
    	}
    	$page = $rs->join('__SHOP_SCORES__ ss','ss.shopId = s.shopId','left')
    	->join('__USERS__ u','u.userId = s.userId','left')
    	->join('__FAVORITES__ f','f.userId = '.$userId.' and f.favoriteType=1 and f.targetId=s.shopId','left')
    	->where($where)
    	->order('distince asc')
    	->field('s.shopId,s.shopImg,s.shopName,s.longitude,s.latitude,s.shopTel,s.shopQQ,s.shopWangWang,s.shopCompany,ss.totalScore,ss.totalUsers,ss.goodsScore,ss.goodsUsers,ss.serviceScore,ss.serviceUsers,ss.timeScore,ss.timeUsers,.u.loginName,u.userName,f.favoriteId,s.areaIdPath')
        ->field("round(6378.138*2*asin(sqrt(pow(sin( (".$location['latitude']."*pi()/180-s.latitude*pi()/180)/2),2)+cos(".$location['latitude']."*pi()/180)*cos(s.latitude*pi()/180)* pow(sin( (".$location['longitude']."*pi()/180-s.longitude*pi()/180)/2),2)))*1000)/1000 as distince")
        ->paginate($pagesize)->toArray();
    	if(empty($page['data']))return $page;
    	$shopIds = [];
    	$areaIds = [];
    	foreach ($page['data'] as $key =>$v){
    		$shopIds[] = $v['shopId'];
    		$tmp = explode('_',$v['areaIdPath']);
    		$areaIds[] = $tmp[1];
    		$page['data'][$key]['areaId'] = $tmp[1];
    		//总评分
    		$page['data'][$key]['totalScore'] = WSTScore($v["totalScore"], $v["totalUsers"]);
    		$page['data'][$key]['goodsScore'] = WSTScore($v['goodsScore'],$v['goodsUsers']);
    		$page['data'][$key]['serviceScore'] = WSTScore($v['serviceScore'],$v['serviceUsers']);
    		$page['data'][$key]['timeScore'] = WSTScore($v['timeScore'],$v['timeUsers']);
    		//商品列表
    		$goods = Db::name('goods')->where(['dataFlag'=> 1,'goodsStatus'=>1,'isSale'=>1,'shopId'=> $v["shopId"]])->field('goodsId,goodsName,shopPrice,goodsImg')->limit(10)->order('saleTime desc')->select();
    		$page['data'][$key]['goods'] = $goods;
    		//店铺商品总数
    		$page['data'][$key]['goodsTotal'] = count($goods);
		}
		$rccredMap = [];
		$goodsCatMap = [];
		$areaMap = [];
		//认证、地址、分类
		if(!empty($shopIds)){
			$rccreds = Db::name('shop_accreds')->alias('sac')->join('__ACCREDS__ a','a.accredId=sac.accredId and a.dataFlag=1','left')
			             ->where([['shopId','in',$shopIds]])->field('sac.shopId,accredName,accredImg')->select();
			foreach ($rccreds as $v){
				$rccredMap[$v['shopId']][] = $v;
			}
			$goodsCats = Db::name('cat_shops')->alias('cs')->join('__GOODS_CATS__ gc','cs.catId=gc.catId and gc.dataFlag=1','left')
			               ->where([['shopId','in',$shopIds]])->field('cs.shopId,gc.catName')->select();
		    foreach ($goodsCats as $v){
				$goodsCatMap[$v['shopId']][] = $v['catName'];
			}
			$areas = Db::name('areas')->alias('a')->join('__AREAS__ a1','a1.areaId=a.parentId','left')
			           ->where([['a.areaId','in',$areaIds]])->field('a.areaId,a.areaName areaName2,a1.areaName areaName1')->select();
		    foreach ($areas as $v){
				$areaMap[$v['areaId']] = $v;
			}         
		}
		foreach ($page['data'] as $key =>$v){
			$page['data'][$key]['accreds'] = (isset($rccredMap[$v['shopId']]))?$rccredMap[$v['shopId']]:[];
			$page['data'][$key]['catshops'] = (isset($goodsCatMap[$v['shopId']]))?implode(',',$goodsCatMap[$v['shopId']]):'';
			$page['data'][$key]['areas']['areaName1'] = (isset($areaMap[$v['areaId']]['areaName1']))?$areaMap[$v['areaId']]['areaName1']:'';
			$page['data'][$key]['areas']['areaName2'] = (isset($areaMap[$v['areaId']]['areaName2']))?$areaMap[$v['areaId']]['areaName2']:'';
		}
    	return $page;
    }
    /**
     * 获取卖家中心信息
     */
    public function getShopSummary($shopId){
    	$shop = $this->alias('s')->join('__SHOP_SCORES__ cs','cs.shopId = s.shopId','left')
    	           ->where(['s.shopId'=>$shopId,'dataFlag'=>1])
    	->field('s.shopMoney,s.noSettledOrderFee,s.paymentMoney,s.shopId,shopImg,shopName,shopAddress,shopQQ,shopTel,serviceStartTime,serviceEndTime,s.expireDate,cs.*')
    	->find();
    	//评分
    	$scores['totalScore'] = WSTScore($shop['totalScore'],$shop['totalUsers']);
    	$scores['goodsScore'] = WSTScore($shop['goodsScore'],$shop['goodsUsers']);
    	$scores['serviceScore'] = WSTScore($shop['serviceScore'],$shop['serviceUsers']);
    	$scores['timeScore'] = WSTScore($shop['timeScore'],$shop['timeUsers']);
    	WSTUnset($shop, 'totalUsers,goodsUsers,serviceUsers,timeUsers');
    	$shop['scores'] = $scores;
    	//认证
    	$accreds = $this->shopAccreds($shopId);
    	$shop['accreds'] = $accreds;
    	
        //查看商家钱包是否足够钱
        $USER = session('WST_USER');
        $USER['shopMoney'] = $shop['shopMoney'];
        $USER['noSettledOrderFee'] = $shop['noSettledOrderFee'];
        $USER['paymentMoney'] = $shop['paymentMoney'];
        session('WST_USER',$USER);
        
        
        $stat = array();
        $date = date("Y-m-d");
        $userId = session('WST_USER.userId');
        /**********今日动态**********/
        //待查看消息数
        $stat['messageCnt'] = Db::name('messages')->where(['receiveUserId'=>$userId,'msgStatus'=>0,'dataFlag'=>1])->count();
        //今日销售金额
        $stat['saleMoney'] = Db::name('orders')->where([['orderStatus','egt',0],['shopId','=',$shopId],['dataFlag','=',1]])->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->sum("goodsMoney");
        //今日订单数
        $stat['orderCnt'] = Db::name('orders')->where([['orderStatus','egt',0],['shopId','=',$shopId],['dataFlag','=',1]])->whereTime('createTime', 'between', [$date.' 00:00:00', $date.' 23:59:59'])->count();
        //待发货订单
        $stat['waitDeliveryCnt'] = Db::name('orders')->where(['shopId'=>$shopId,'orderStatus'=>0,'dataFlag'=>1])->count();
        //待收货订单
        $stat['waitReceiveCnt'] = Db::name('orders')->where(['shopId'=>$shopId,'orderStatus'=>1,'dataFlag'=>1])->count();
        //取消/拒收
        $stat['cancel'] = Db::name('orders')->where([['orderStatus','in',[-1,-3]],['shopId','=',$shopId],['dataFlag','=',1]])->count();
        //库存预警
        $goodsn = Db::name('goods')->where('shopId ='.$shopId.' and dataFlag = 1 and goodsStock <= warnStock and isSpec = 0 and warnStock>0')->cache('stockWarnCnt1'.$shopId,600)->count();
        $specsn = Db::name('goods_specs')->where('shopId ='.$shopId.' and dataFlag = 1 and specStock <= warnStock and warnStock>0')->cache('stockWarnCnt2'.$shopId,600)->count();
        $stat['stockWarnCnt'] = $goodsn+$specsn;

        /**********商品信息**********/
        //商品总数
        $stat['goodsCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1])->cache('goodsCnt'.$shopId,600)->count();
        //上架商品
        $stat['onSaleCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1,'goodsStatus'=>1,'isSale'=>1])->cache('onSaleCnt'.$shopId,600)->count();
        //待审核商品
        $stat['waitAuditCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1,'goodsStatus'=>0])->cache('waitAuditCnt'.$shopId,600)->count();
        //仓库中的商品
        $stat['unSaleCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1,'goodsStatus'=>1,'isSale'=>0])->cache('unSaleCnt'.$shopId,600)->count();
        //违规商品
        $stat['illegalCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1,'goodsStatus'=>-1])->cache('illegalCnt'.$shopId,600)->count();
        //今日新品
        $stat['newGoodsCnt'] = Db::name('goods')->where(['shopId'=>$shopId,'dataFlag'=>1,'goodsStatus'=>1,'isSale'=>1,'isNew'=>1])->cache('newGoodsCnt'.$shopId,600)->count();
        //待回复咨询
        $stat['consult'] = Db::name('goods_consult gc')->join('goods g','g.goodsId=gc.goodsId','inner')->where(['g.shopId'=>$shopId,'gc.dataFlag'=>1,'goodsStatus'=>1])->cache('consult'.$shopId,600)->count();
        
        /**********订单信息**********/
        //待付款订单
        $stat['orderNeedpayCnt'] = Db::name('orders')->where(['shopId'=>$shopId,'orderStatus'=>-2,'dataFlag'=>1])->count();
        //待结束订单
        $stat['orderWaitCloseCnt'] = Db::name('orders')->where(['shopId'=>$shopId,'orderStatus'=>2,'dataFlag'=>1,'isClosed'=>0])->cache('orderWaitCloseCnt'.$shopId,600)->count();
        //退货退款订单
        $stat['orderRefundCnt'] = Db::name('orders')->alias('o')->join('order_refunds orf','orf.orderId=o.orderId')->where(['shopId'=>$shopId,'refundStatus'=>0,'o.dataFlag'=>1])->count();
        //待评价订单
        $stat['orderWaitAppraisesCnt'] = Db::name('orders')->where(['shopId'=>$shopId,'orderStatus'=>2,'dataFlag'=>1,'isAppraise'=>0])->cache('orderWaitAppraisesCnt'.$shopId,600)->count();
        // 投诉订单数
        $stat['complainNum'] = Db::name('order_complains')->where(['respondTargetId'=>$shopId,'complainStatus'=>1])->count();
        // 近30天销售排行
        $start = date('Y-m-d H:i:s',strtotime("-30 day"));
        $end = date('Y-m-d H:i:s');
        $prefix = config('database.prefix');
        $stat['goodsTop'] = $rs = Db::table($prefix.'order_goods')->alias([$prefix.'order_goods'=>'og',$prefix.'orders'=>'o',$prefix.'goods'=>'g'])
                                          ->join($prefix.'orders','og.orderId=o.orderId')
                                          ->join($prefix.'goods','og.goodsId=g.goodsId')
                                          ->order('goodsNum desc')
                                          ->whereTime('o.createTime','between',[$start,$end])
                                          ->where('(payType=0 or (payType=1 and isPay=1)) and o.dataFlag=1 and o.shopId='.$shopId)->group('og.goodsId')
                                          ->field('og.goodsId,g.goodsName,goodsSn,sum(og.goodsNum) goodsNum,g.goodsImg')
                                          ->limit(10)->select();
    	return ['shop'=>$shop,'stat'=>$stat];
    }    
    
    

    /**
     * 获取店铺提现账号
     */
    public function getShopAccount(){
        $shopId = (int)session('WST_USER.shopId');
        $shops = Db::name('shops')->alias('s')->join('banks b','b.bankId=s.bankId','inner')->where('s.shopId',$shopId)->field('b.bankName,s.bankAreaId,bankNo,bankUserName')->find();
        return $shops;
    }
    /**
     * 保存入驻资料
     */
    public function saveStep2($data = []){
        $userId = (int)session('WST_USER.userId');
        //判断是否存在入驻申请
        $shops = $this->where('userId',$userId)->find();
        //新增入驻申请
        Db::startTrans();
        try{
            if(empty($shops)){
                $vshop = new VShop();
                $shop = ['userId'=>$userId,'applyStatus'=>0,'applyStep'=>2];
                $this->save($shop);
                WSTAllow($data,implode(',',$vshop->scene['applyStep1']));
                $data['shopId'] = $this->shopId;
                $result = Db::name('shop_extras')->insert($data);
                $shopId = $this->shopId;
                $WST_USER = session('WST_USER');
                $WST_USER['tempShopId'] = $shopId;
                session('WST_USER',$WST_USER);
                Db::commit();
                return WSTReturn('保存成功',1);
            }else{
                if($shops['applyStatus']>=1)return WSTReturn('请勿重复申请入驻');
                if($shops->applyStep<2){
                    $shops->applyStep = 2;
                    $shops->save();
                }
                $vshop = new VShop();
                WSTAllow($data,implode(',',$vshop->scene['applyStep1']));
                $result = Db::name('shop_extras')->where('shopId',$shops['shopId'])->update($data);
                if(false !== $result){
                    Db::commit();
                    return WSTReturn('保存成功',1);
                }else{
                    return WSTReturn('保存失败');
                }
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('保存失败',-1);
        }
    }
    public function saveStep3($data = []){
        /*
            legalCertificateImg
            businessLicenceImg
            bankAccountPermitImg
            organizationCodeImg
        */
        $shopId = (int)session('WST_USER.tempShopId');
        if($shopId==0)return WSTReturn('非法的操作');
        $shops = model('shops')->get($shopId);
        if($shops['applyStatus']>=1)return WSTReturn('请勿重复申请入驻');
        //判断是否存在入驻申请
        $vshop = new VShop();
        WSTAllow($data,implode(',',$vshop->scene['applyStep2']));
        //获取地区
        $areaIds = model('Areas')->getParentIs($data['businessAreaPath0']);
        if(!empty($areaIds))$data['businessAreaPath'] = implode('_',$areaIds)."_";
        $areaIds = model('Areas')->getParentIs($data['areaIdPath0']);
        if(!empty($areaIds))$data['areaIdPath'] = implode('_',$areaIds)."_";
        if($data['isLongbusinessDate']==1)unset($data['businessEndDate']);
        if($data['isLonglegalCertificateDate']==1)unset($data['legalCertificateEndDate']);
        if($data['isLongOrganizationCodeDate']==1)unset($data['organizationCodeEndDate']);
        Db::startTrans();
        try{
            if($shops->applyStep<3){
                $shops->applyStep = 3;
                $shops->save();
            }
            $validate = new VShop;
            if(!$validate->scene('applyStep2')->check($data))return WSTReturn($validate->getError());
            $seModel = model('ShopExtras');
            $seModel->allowField(true)->save($data,['shopId'=>$shopId]);
            $Id = $seModel->where(['shopId'=>$shopId])->value('id');// 获取主键
            //启用上传图片
            WSTUseResource(0, $Id, $data['legalCertificateImg'],'shopextras');
            WSTUseResource(0, $Id, $data['businessLicenceImg'],'shopextras');
            WSTUseResource(0, $Id, $data['bankAccountPermitImg'],'shopextras');
            WSTUseResource(0, $Id, $data['organizationCodeImg'],'shopextras');

            $this->allowField(true)->save($data,['shopId'=>$shopId]);
            Db::commit();
            return WSTReturn('保存成功',1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('保存失败',-1);
        }
    }
    public function saveStep4($data = []){
        /*
            taxRegistrationCertificateImg
            taxpayerQualificationImg
        */
        $shopId = (int)session('WST_USER.tempShopId');
        if($shopId==0)return WSTReturn('非法的操作');
        $shops = model('shops')->get($shopId);
        if($shops['applyStatus']>=1)return WSTReturn('请勿重复申请入驻');
        //判断是否存在入驻申请
        $vshop = new VShop();
        WSTAllow($data,implode(',',$vshop->scene['applyStep3']));
        $areaIds = model('Areas')->getParentIs($data['bankAreaId']);
        if(!empty($areaIds))$data['bankAreaIdPath'] = implode('_',$areaIds)."_";
        Db::startTrans();
        try{
            if($shops->applyStep<4){
                $shops->applyStep = 4;
                $shops->save();
            }
            $seModel = model('ShopExtras');
            $seModel->allowField(true)->save($data,['shopId'=>$shopId]);
            $Id = $seModel->where(['shopId'=>$shopId])->value('id');
            //启用上传图片
            WSTUseResource(0, $Id, $data['taxRegistrationCertificateImg'],'shopextras');
            WSTUseResource(0, $Id, $data['taxpayerQualificationImg'],'shopextras');

            $this->allowField(true)->save($data,['shopId'=>$shopId]);
            Db::commit();
            return WSTReturn('保存成功',1);
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('保存失败',-1);
        }
    }
    public function saveStep5($data = []){
        $shopId = (int)session('WST_USER.tempShopId');
        if($shopId==0)return WSTReturn('非法的操作');
        $shops = model('shops')->get($shopId);
        if($shops['applyStatus']>=1)return WSTReturn('请勿重复申请入驻');
        //判断是否存在入驻申请
        $vshop = new VShop();
        $filters = $vshop->scene['applyStep4'];
        $filters[] = 'shopQQ';
        $filters[] = 'shopWangWang';
        WSTAllow($data,implode(',',$filters));
        Db::startTrans();
        try{
            $data['applyStatus'] = 1;
            $data['applyTime'] = date('Y-m-d H:i:s');
            $result = $this->allowField(true)->save($data,['shopId'=>$shopId]);
            // 启用图片
            WSTUseResource(0, $shopId, $data['shopImg'],'shops','shopImg');
            if($shops->applyStep<5){
                $shops->applyStep = 5;
                $shops->save();
            }
            if(false !== $result){
                //经营范围
                $goodsCats = explode(',',$data['goodsCatIds']);
                foreach ($goodsCats as $v){
                    if((int)$v>0)Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
                }
                Db::commit();
                return WSTReturn('保存成功',1);
            }else{
                return WSTReturn('保存失败');
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('保存失败',-1);
        }
    }

    /**
     * 获取商家入驻资料
     */
    public function getShopApply(){
        $userId = (int)session('WST_USER.userId');
        $rs = $this->alias('s')->join('__SHOP_EXTRAS__ ss','s.shopId=ss.shopId','inner')
                   ->where('s.userId',$userId)
                   ->find();
        if(!empty($rs)){
            $rs = $rs->toArray();
            $goodscats = Db::name('cat_shops')->where('shopId',$rs['shopId'])->select();
            $rs['catshops'] = [];
            foreach ($goodscats as $v){
                $rs['catshops'][$v['catId']] = true;
            }
            $rs['taxRegistrationCertificateImgVO'] = ($rs['taxRegistrationCertificateImg']!='')?explode(',',$rs['taxRegistrationCertificateImg']):[];
        }else{
            $rs = [];
            $data1 = $this->getEModel('shops');
            $data2 = $this->getEModel('shop_extras');
            $rs = array_merge($data1,$data2);
            $rs['taxRegistrationCertificateImgVO'] = [];
        }
        return $rs;
    }

    /**
     * 判断是否申请入驻过
     */
    public function checkApply(){
        $userId = (int)session('WST_USER.userId');
        $rs = $this->where(['userId'=>$userId])->find();
        if(!empty($rs)){
            $WST_USER = session('WST_USER');
            $WST_USER['tempShopId'] = $rs->shopId;
            session('WST_USER',$WST_USER);
            session('apply_step',$rs['applyStep']);
        }
        return $rs;
    }
    /**
    * 首页店铺街列表
    */
    public function indexShopQuery($num=4){
        $cacheData = cache('PC_SHOP_STREET');
        if(!$cacheData){
            $cacheData = $this->alias('s')
                ->join('__SHOP_CONFIGS__ sc','s.shopId=sc.shopId','inner')
                ->join('__RECOMMENDS__ r','s.shopId=r.dataId')
                ->where(['r.goodsCatId'=>0,'s.shopStatus'=>1,'s.dataFlag'=>1,'r.dataSrc'=>1,'r.dataType'=>0])
                ->field('s.shopId,s.shopName,s.shopAddress,sc.shopStreetImg')->order('r.dataSort asc')->select();
            if(count($cacheData)==0){
                $cacheData = $this->alias('s')
                    ->join('__SHOP_CONFIGS__ sc','s.shopId=sc.shopId','inner')
                    ->where([['s.shopStatus','=',1],['s.dataFlag','=',1]])
                    ->field('s.shopId,s.shopName,s.shopAddress,sc.shopStreetImg')
                    ->limit($num)
                    ->select();  
            }
            cache('PC_SHOP_STREET',$cacheData,86400);
        }
        return $cacheData;
    }

    public function getCatShopInfo(){
        $shopId = (int)session('WST_USER.shopId');
        //获取经营范围
        $trade = Db::name("trades t")->join("shops s","s.tradeId=t.tradeId","inner")
                ->field("s.tradeId,t.tradeFee")
                ->where(['s.shopId'=>$shopId])
                ->find();
        $rs['needPay'] = $trade['tradeFee'];
        $rs['tradeId'] = $trade['tradeId'];
        return $rs;
    }

    public function renew(){
        $userId = (int)session('WST_USER.userId');
        $shopId = (int)session('WST_USER.shopId');
        $shops = $this->getCatShopInfo();
        if((int)$shops['needPay']>0)return WSTReturn('请缴纳年费');
        Db::startTrans();
        try{
            $shopExpireDate = $this->where(['userId'=>$userId])->value('expireDate');
            $shopData = [];
            $newExpireDate = date('Y-m-d',strtotime("$shopExpireDate +1 year"));
            $shopData['expireDate'] = $newExpireDate;
            $shopRes = $this->where(['userId'=>$userId])->update($shopData);
            $fee = [];
            $fee['userId'] = $userId;
            $fee['shopId'] = $shopId;
            $fee['money'] = 0;
            $fee['remark'] = "店铺缴纳年费";
            $fee['startDate'] = date('Y-m-d');
            $fee['endDate'] = date('Y-m-d',strtotime("$shopExpireDate +1 year"));;
            $fee['createTime'] = date('Y-m-d H:i:s');
            $result = Db::name('shop_fees')->insert($fee);
            if(false !== $shopRes && false !== $result){
                session('WST_USER.expireDate',$newExpireDate);
                Db::commit();
                return WSTReturn('续费成功',1);
            }
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('续费失败');
        }
    }
}
