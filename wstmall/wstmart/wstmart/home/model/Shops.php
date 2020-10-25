<?php
namespace wstmart\home\model;
use wstmart\common\model\Shops as CShops;
use wstmart\home\validate\Shops as VShop;
use wstmart\home\validate\ShopBase as VShopBase;
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
        $recommendShopsId = model('common/shops')->getRecommendShopsId();
        $recommendWhere = '';
        $recommendOrder = '';
        if($recommendShopsId!=''){
            $recommendWhere = "find_in_set(s.shopId,'".$recommendShopsId."')";
            $recommendOrder = "find_in_set(s.shopId,'".$recommendShopsId."') desc";
        }
    	$rs = $rs->join('__SHOP_SCORES__ ss','ss.shopId = s.shopId','left')
    	->join('__USERS__ u','u.userId = s.userId','left')
    	->join('__FAVORITES__ f','f.userId = '.$userId.' and f.favoriteType=1 and f.targetId=s.shopId','left')
    	->where($where);
        if($recommendWhere!='')$rs->whereOrRaw($recommendWhere)->orderRaw($recommendOrder);
    	$page = $rs->order('distince asc')->distinct(true)
    	->field('s.shopId,s.shopImg,s.shopName,s.longitude,s.latitude,s.shopTel,s.shopQQ,s.shopWangWang,s.shopCompany,ss.totalScore,ss.totalUsers,ss.goodsScore,ss.goodsUsers,ss.serviceScore,ss.serviceUsers,ss.timeScore,ss.timeUsers,u.loginName,u.userName,f.favoriteId,s.areaIdPath')
        ->field("round(6378.138*2*asin(sqrt(pow(sin( (".$location['latitude']."*pi()/180-s.latitude*pi()/180)/2),2)+cos(".$location['latitude']."*pi()/180)*cos(s.latitude*pi()/180)* pow(sin( (".$location['longitude']."*pi()/180-s.longitude*pi()/180)/2),2)))*1000)/1000 as distince");
    	$count = $page->select();
    	$page = $page
        ->paginate($pagesize,count($count))->toArray();
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
     * 获取店铺信息
     */
	public function getByView($id){
		$shop = $this->alias('s')->join('__BANKS__ b','b.bankId=s.bankId','left')
		             ->where(['s.dataFlag'=>1,'shopId'=>$id])
		             ->field('s.*,b.bankName')->find();
	     $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$shop['areaIdPath']);
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::name('areas')->where([['areaId','in',$areaIds],['dataFlag','=',1]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$shop['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $shop['areaName'] = implode('',$areaNames);
	         }
         }             
		                          
		//获取经营范围
		$goodsCats = Db::name('goods_cats')->where(['parentId'=>0,'isShow'=>1,'dataFlag'=>1])->field('catId,catName')->select();
		$catshops = Db::name('cat_shops')->where('shopId',$id)->select();
		$catshopMaps = [];
		foreach ($goodsCats as $v){
			$catshopMaps[$v['catId']] = $v['catName'];
		}
		$catshopNames = [];
		foreach ($catshops as $key =>$v){
			if(isset($catshopMaps[$v['catId']]))$catshopNames[] = $catshopMaps[$v['catId']];
		}
		$shop['catshopNames'] = implode('、',$catshopNames);
		//获取认证类型
	    $shop['accreds'] =Db::name('shop_accreds')->alias('sac')->join('__ACCREDS__ a','sac.accredId=a.accredId and a.dataFlag=1','inner')
	                    ->where('sac.shopId',$id)->field('accredName,accredImg')->select();
	    //开卡地址
        $areaNames  = model('areas')->getParentNames($shop['bankAreaId']);
        $shop['bankAreaName'] = implode('',$areaNames);
		return $shop;
	}
    /**
     * 获取店铺指定字段
     */
    public function getFieldsById($shopId,$fields){
        return $this->where(['shopId'=>$shopId,'dataFlag'=>1])->field($fields)->find();
    }

    /**
     * 保存入驻资料
     */
    public function saveStep($data = []){
        $userId = (int)session('WST_USER.userId');
        $flowId = (int)input('flowId');
        //判断是否存在入驻申请
        $shops = $this->alias('s')->join('__SHOP_USERS__ sur','s.shopId=sur.shopId','left')->field('s.*')->where(['sur.userId'=>$userId])->find();
        if(!empty($shops))return WSTReturn('请勿重复申请入驻');
        $shops = $this->where('userId',$userId)->find();
        $shopId = 0;
        if(empty($shops)){
            $shop = ['userId'=>$userId,'applyStatus'=>0];
            $this->save($shop);
            $exData['shopId'] = $this->shopId;
            Db::name('shop_extras')->insert($exData);
            $shopId = $this->shopId;
        }else{
            $shopId = $shops['shopId'];
        }
        if($shops['applyStatus']==1)return WSTReturn('您的入驻申请正在审核，请勿重复提交');
        if($shops['applyStatus']==2)return WSTReturn('请勿重复申请入驻');

        // 保存流程id
        $applyStep = ['applyStep'=>$flowId];
        $this->save($applyStep,['shopId'=>$shopId]);
        //获取完整流程信息
        $shopFlows = $this->getShopFlowDatas($flowId);

        //新增入驻申请
        // 先遍历前台传来的data,根据shop_base表判断是属于shops表还是shop_extras表，分别用两个数组保存
        $shopsData = [];
        $shopExtrasData = [];
        // 保存上传图片的路径，用来启用上传图片
        $uploadShopsImgPath = [];
        $uploadShopExtrasImgPath = [];
        $unsetField = [];
        $goodsCats = [];
        foreach($data as $k => $v){
            $field = Db::name('shop_bases')->where(['fieldName'=>$k,'dataFlag'=>1])->field('fieldName,fieldType,fieldAttr,isShopsTable,dateRelevance,isShow,isRequire')->find();
            if($field['isShopsTable']==1){
                // 属于shops表
                $shopsData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaId = $shopsData[$k];
                    $areaIds = model('Areas')->getParentIs($shopsData[$k]);
                    if(!empty($areaIds))$shopsData[$k] = implode('_',$areaIds)."_";
                    if($field['fieldName'] == 'areaIdPath')$shopsData['areaId'] = $areaId;
                    if($field['fieldName'] == 'bankAreaIdPath')$shopsData['bankAreaId'] = $areaId;
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopsImgPath[] = $data[$k];
                }
            }else{
                // 属于shop_extras表
                $shopExtrasData[$k] = $v;
                //获取地区
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'area'){
                    $areaIds = model('Areas')->getParentIs($shopExtrasData[$k]);
                    if(!empty($areaIds))$shopExtrasData[$k] = implode('_',$areaIds)."_";
                }
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'file'){
                    $uploadShopExtrasImgPath[] = $data[$k];
                }
                // 日期字段入库前处理
                if($field['fieldType'] == 'other' && $field['fieldAttr'] == 'date'){
                    // 当日期字段不是必填项，需删除该字段
                    if($field['isRequire'] == 0){
                        $unsetField[] = $field['fieldName'];
                    }
                    if($field['dateRelevance']){
                        $dateRelevance = explode(',',$field['dateRelevance']);
                        // 如果选择了长期，就删除字段的结束日期
                        if($data[$dateRelevance[1]]==1){
                            $unsetField[] = $dateRelevance[0];
                        }
                    }
                }
                //经营范围
                if(!empty($data['goodsCatIds']))$goodsCats = explode(',',$data['goodsCatIds']);
            }
        }
        // 删除无需入库的字段
        foreach($shopExtrasData as $k => $v){
            if(in_array($k,$unsetField)){
                unset($shopExtrasData[$k]);
            }
        }

        $validate = new VShopBase();
        $validate->setRuleAndMessage($shopsData);
        $validate->setRuleAndMessage($shopExtrasData);

        Db::startTrans();
        try{
            $shopsData['shopId'] = $shopId;
            //$shopsData['applyStatus'] = 1;
            $shopExtrasData['shopId'] = $shopId;
            if(!$validate->scene('add')->check($data))return WSTReturn($validate->getError());
            //判断是不是最后一个表单环节了
            $flows = $shopFlows['flows'];
            if($flows[count($flows)-1]['flowId']==$shopFlows['nextStep']['flowId']){
                $shopsData['createTime'] = date('Y-m-d');
                $shopsData['expireDate'] = date('Y-m-d');
                $shopsData['applyTime'] = date('Y-m-d H:i:s');
                $tmpPkey = session('tmpPkey');
                if($tmpPkey==''){
                    // 选择不用交钱的类目，直接更改申请状态为待审核
                    $shopsData['applyStatus'] = 1;
                }else{
                    // 选择要交钱的类目，要等支付成功后才会将申请状态改为待审核
                    $shopsData['applyStatus'] = 0;
                }
            }
            $this->allowField(true)->save($shopsData,['shopId'=>$shopId]);
            foreach($uploadShopsImgPath as $v){
                //启用上传图片
                WSTUseResource(0, $this->shopId, $v ,'shops');
            }
            $seModel = model('ShopExtras');
            $seModel->allowField(true)->save($shopExtrasData,['shopId'=>$shopId]);
            $extraId = $seModel->where(['shopId'=>$shopId])->value('id');// 获取主键
            foreach($uploadShopExtrasImgPath as $v){
                //启用上传图片
                WSTUseResource(0, $extraId, $v ,'shopextras');
            }
            if($goodsCats){
                Db::name('cat_shops')->where('shopId','=',$shopId)->delete();
                foreach ($goodsCats as $v){
                    if((int)$v>0)Db::name('cat_shops')->insert(['shopId'=>$shopId,'catId'=>$v]);
                }
            }
            Db::commit();
            session('tmpApplyStep',$shopFlows['nextStep']['flowId']);
            return WSTReturn('保存成功', 1, ['nextflowId'=>$shopFlows['nextStep']['flowId']]);
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
        }else{
            $rs = [];
            $data1 = $this->getEModel('shops');
            $data2 = $this->getEModel('shop_extras');
            $rs = array_merge($data1,$data2);
        }
        return $rs;
    }

    /**
     * 判断是否申请入驻过
     */
    public function checkApply(){
        $userId = (int)session('WST_USER.userId');
        $rs = $this->alias('s')->join('__SHOP_USERS__ sur','s.shopId=sur.shopId','left')->field('s.*')->where(['sur.userId'=>$userId])->find();
        if(empty($rs)){
            $rs = $this->where('userId',$userId)->find();
        }
        if(!empty($rs)){
            $WST_USER = session('WST_USER');
            $WST_USER['tempShopId'] = $rs->shopId;
            session('WST_USER',$WST_USER);
            session('tmpApplyStep',$rs['applyStep']);
        }else{
            session('tmpApplyStep',0);
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
            cache('PC_SHOP_STREET',$cacheData,86400);
        }
        return $cacheData;
    }

    /*
     * 获取入驻流程
     */
    public function getShopFlows(){
        return Db::name('shop_flows')->where(['isShow'=>1,'dataFlag'=>1])->order('sort asc')->select();
    }

    /*
     * 获取单个入驻流程
     */
    public function getShopFlowById($id){
        return Db::name('shop_flows')->where(['flowId'=>$id,'isShow'=>1,'dataFlag'=>1])->find();
    }

    /*
     * 获取单个入驻流程里的字段信息
     */
    public function getFlowFieldsById($id){
        return Db::name('shop_bases')->where(['flowId'=>$id,'dataFlag'=>1])->order('fieldSort asc,id asc')->select();
    }
    /**
     * 获取商家入驻流程
     */
    public function getShopFlowDatas($flowId = 0){
        $data = ['flows'=>[],'prevStep'=>[],'nextStep'=>[]];
        $data['flows'] = Db::name('shop_flows')->where(['isShow'=>1,'dataFlag'=>1])->order('sort asc')->select();
        $flowNum = count($data['flows']);
        $flowId = ($flowId==0)?$data['flows'][0]['flowId']:$flowId;
        foreach ($data['flows'] as $key => $v) {
            if($key>0){
               $data['prevStep'] =  $data['flows'][$key-1];
            }
            if($v['flowId'] == $flowId){
                $data['currStep'] = $v;
                if(($flowNum-1)>$key){
                    $data['nextStep'] = $data['flows'][$key+1];
                }
                break;
            }
        }
        return $data;
    }


    /**
     * 获取店铺信息
     */
    public function getTradeFee($userId){
        $rs = Db::name("trades t")->join("shops s","s.tradeId=t.tradeId","inner")
        ->field("t.tradeFee")
        ->where(['s.userId'=>$userId])
        ->find();
        return $rs;
    }

}
