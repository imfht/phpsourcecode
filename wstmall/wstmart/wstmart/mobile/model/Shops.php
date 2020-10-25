<?php
namespace wstmart\mobile\model;
use wstmart\common\model\Shops as CShops;
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
 * 门店类
 */
class Shops extends CShops{
    /**
     * 店铺街列表
     */
    public function pageQuery($pagesize){
        $lng = (float)input("longitude");
        $lat = (float)input("latitude");
        if($lng=='' && $lat==''){
            $lng = 0;
            $lat = 0;
        }
        $catId = (int)input("catId");
        $keyword = input("keyword");
        $condition = input("condition");
        $distance = input("distance");
        $desc = input("desc");
        $totalScore = input('totalScore');
        $datas = array('sort'=>array('1'=>'ss.totalScore/ss.totalUsers'),'desc'=>array('desc','asc'));
        $datas1 = array('sort'=>array('1'=>'distince'),'desc'=>array('desc','asc'));
        $rs = $this->alias('s');
        $tol = $this->alias('s');
        $where = [];
        $where[] = ['s.dataFlag','=',1];
        $where[] = ['s.shopStatus','=',1];
        $where[] = ['s.applyStatus','=',2];
        if($keyword!='')$where[] = ['s.shopName','like','%'.$keyword.'%'];
        if($catId>0){
            $rs->join('__CAT_SHOPS__ cs','cs.shopId = s.shopId','left');
            $tol->join('__CAT_SHOPS__ cs','cs.shopId = s.shopId','left');
            $where[] = ['cs.catId','=',$catId];
        }
        $order = "distince asc";
        if($condition == 1){
            $order = "{$datas['sort'][$condition]} {$datas['desc'][$desc]}";
        }
        if($condition == 2){
            $order = "{$datas1['sort'][1]} {$datas['desc'][$desc]}";
        }
        $shopIds = $this->filterByCondition();
        if(!empty($shopIds)){
            $where[] = ['s.shopId','in',$shopIds];
        }
        $where1 = [];
        if($totalScore != ''){
            $totalScore = explode('_',$totalScore);
            $maxScore = (float)(($totalScore[1])*5/100);
            $minScore = (float)(($totalScore[0])*5/100);
            $where1[] = '(ss.totalScore/3)/ss.totalUsers >='.$minScore;
            $where1[] = '(ss.totalScore/3)/(case when ss.totalUsers = 0 then 1 else ss.totalUsers end) <= '.$maxScore;
        }
        $recommendShopsId = model('common/shops')->getRecommendShopsId();
        $recommendWhere = '';
        $recommendOrder = '';
        if($recommendShopsId!=''){
            $recommendWhere = "find_in_set(s.shopId,'".$recommendShopsId."')";
            $recommendOrder = "find_in_set(s.shopId,'".$recommendShopsId."') desc";
        }
        $rs->fieldRaw('(ss.totalScore/3)/(case when ss.totalUsers = 0 then 1 else ss.totalUsers end) as aa');
        $rs = $rs->join('__SHOP_SCORES__ ss','ss.shopId = s.shopId','left')
            ->join('__RECOMMENDS__ r','s.shopId = r.dataId and r.dataType=0','left')
        ->where($where)->where(implode(' AND ',$where1))->orderRaw('isnull(r.dataSort),r.dataSort asc')->orderRaw($order);
        if($recommendWhere!='')$rs->whereOrRaw($recommendWhere)->orderRaw($recommendOrder);
        $page = $rs->fieldRaw('s.shopId,s.shopImg,s.shopName,s.shopCompany,ss.totalScore,ss.totalUsers,ss.goodsScore,ss.goodsUsers,ss.serviceScore,ss.serviceUsers,ss.timeScore,ss.timeUsers,s.areaIdPath')
        ->fieldRaw("round(6378.138*2*asin(sqrt(pow(sin( (".$lat."*pi()/180-s.latitude*pi()/180)/2),2)+cos(".$lat."*pi()/180)*cos(s.latitude*pi()/180)* pow(sin( (".$lng."*pi()/180-s.longitude*pi()/180)/2),2)))*1000)/1000 as distince")->distinct(true);
        $count = $page->select();
    	$page = $page
            ->paginate($pagesize,count($count))->toArray();
        $totalShop = $tol->join('__SHOP_SCORES__ ss','ss.shopId = s.shopId','left')
                        ->where($where)->where(implode(' AND ',$where1))
                        ->field('ss.totalScore,ss.totalUsers')
                        ->select();
        foreach ($page['data'] as $key =>$v){
            //商品列表
            $goods = model('Tags')->listShopGoods('recom',$v['shopId'],0,4);
            $page['data'][$key]['goods'] = $goods;
        }
       // $page['a'] = $a;
        if(empty($page['data']))return $page;
        $shopIds = [];
        $areaIds = [];
        $totalScores = [];
        foreach ($totalShop as $key => $v) {
            if($v["totalUsers"] != 0){
               $totalScores[] = round(($v["totalScore"]/3)/$v["totalUsers"],8);
            }else{
               $totalScores[] = 5;
            }
        }
        foreach ($page['data'] as $key =>$v){
            $shopIds[] = $v['shopId'];
            $tmp = explode('_',$v['areaIdPath']);
            $areaIds[] = $tmp[1];
            $page['data'][$key]['areaId'] = $tmp[1];
            $page['data'][$key]['totalScore'] = WSTScore($v["totalScore"]/3, $v["totalUsers"]==0?1: $v["totalUsers"]);
            $page['data'][$key]['goodsScore'] = WSTScore($v['goodsScore'],$v['goodsUsers']);
            $page['data'][$key]['serviceScore'] = WSTScore($v['serviceScore'],$v['serviceUsers']);
            $page['data'][$key]['timeScore'] = WSTScore($v['timeScore'],$v['timeUsers']);
        }
        $maxScore = max($totalScores)<5?((max($totalScores)/5)*100):100;
        $minScore = min($totalScores)>0?(((min($totalScores)-0.01)/5)*100):0;
        $scores = [];
        //区间跨度
        $span = ($maxScore - $minScore) / 3;
        for($i=1;$i<=3;$i++){
            if(($minScore+$i*$span)>100) {
                $tmpMinVal = round(($minScore+($i-1)*$span),0);
                $scores[$tmpMinVal."_100"] = $tmpMinVal."%-100%";
                break;
            }
            $tmpMinVal = round(($minScore+($i-1)*$span),0);
            $tmpMaxVal = round(($minScore+$i*$span),0);
            $scores[$tmpMinVal."_".$tmpMaxVal] = $tmpMinVal."%-".$tmpMaxVal."%";
        }
        if($totalScore == '' && count($scores)>1){
            $page['screen']['scores'] = $scores;
        }
        $page['minScore'] = $minScore.'-'.$maxScore;
        $rccredMap = [];
        $goodsCatMap = [];
        $areaMap = [];
        //认证、地址、分类
        if(!empty($shopIds)){
            $rccreds = Db::name('shop_accreds')->alias('sac')->join('__ACCREDS__ a','a.accredId=sac.accredId and a.dataFlag=1','left')
                         ->where([['shopId','in',$shopIds]])->field('sac.shopId,accredName,accredImg,a.accredId')->select();
            $accredIds = [];
            $accreds = [];
            foreach ($rccreds as $v){
                $rccredMap[$v['shopId']][] = $v;
                if(!in_array($v['accredId'],$accredIds)){
                    $accredIds[] = $v['accredId'];
                    $accreds[] = $v;
                }
            }
            $page['screen']['accreds'] = $accreds;
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
            $page['data'][$key]['lng'] = $lng;
            $page['data'][$key]['lat'] = $lat;
            $page['data'][$key]['accreds'] = (isset($rccredMap[$v['shopId']]))?$rccredMap[$v['shopId']]:[];
            $page['data'][$key]['catshops'] = (isset($goodsCatMap[$v['shopId']]))?implode(',',$goodsCatMap[$v['shopId']]):'';
            $page['data'][$key]['areas']['areaName1'] = (isset($areaMap[$v['areaId']]['areaName1']))?$areaMap[$v['areaId']]['areaName1']:'';
            $page['data'][$key]['areas']['areaName2'] = (isset($areaMap[$v['areaId']]['areaName2']))?$areaMap[$v['areaId']]['areaName2']:'';
        }
        return $page;
    }
    /**
    * 自营店铺楼层
    */
    public function getFloorData(){
        $limit = (int)input('post.currPage');
        $cacheData = cache('WX_SHOP_FLOOR'.$limit);
        if($cacheData)return $cacheData;
        $rs = Db::name('shop_cats')->where(['dataFlag'=>1,'isShow'=>1,'parentId'=>0,'shopId'=>1])->field('catId,catName')->order('catSort asc,catId asc')->limit($limit,1)->select();
        if($rs){
            $rs= $rs[0];
            $goods = Db::name('goods')->where(['shopCatId1'=>$rs['catId'],'dataFlag'=>1,'isSale'=>1,'goodsStatus'=>1])->field('goodsId,goodsName,goodsImg,shopPrice,marketPrice,saleNum')->order('isHot desc')->limit(4)->select();
            $rs['goods'] = $goods;
            $rs['currPage'] = $limit;
        }
        cache('WX_SHOP_FLOOR'.$limit,$rs,86400);
        return $rs;
    }
    /**
    * 根据订单id 获取店铺名称
    */
    public function getShopName($oId){
        return $this->alias('s')
                    ->join('__ORDERS__ o',"s.shopId=o.shopId",'inner')
                    ->where("o.orderId=$oId")
                    ->value('s.shopName');

    }
    /*过滤条件获取符合店铺Id*/
    public function filterByCondition(){
        $accredId = input('accredId');
        $res = [];
        $shopIds = Db::name('shop_accreds')->where(['accredId'=>$accredId])->field('shopId')->select();
        foreach ($shopIds as $key => $value) {
            $res[] = $value['shopId'];
        }
        return $res;
    }
}
