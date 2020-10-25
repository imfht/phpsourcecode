<?php
namespace wstmart\mobile\controller;
use think\Db;
use wstmart\common\model\GoodsCats;
use wstmart\common\model\Attributes as AT;
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
 * 商品控制器
 */
class Goods extends Base{
	/**
	 * 商品主页
	 */
	public function detail(){
        $root = WSTDomain();
		$m = model('goods');
        $goods = $m->getBySale(input('goodsId/d'));

        // 找不到商品记录
        if(empty($goods)){
            $this->assign('message','商品不知道去哪里了');
            return $this->fetch('error_sys');
        }
        hook('mobileControllerGoodsIndex',['getParams'=>input()]);
        // 分类信息
        $catInfo = Db::name("goods_cats")->field("mobileDetailTheme")->where(['catId'=>$goods['goodsCatId'],'dataFlag'=>1])->find();
        $rule = '/<img src="\/.*?(upload.*?)"/';
        preg_match_all($rule, $goods['goodsDesc'], $images);
        foreach($images[0] as $k=>$v){
            $goods['goodsDesc'] = str_replace(WSTConf('CONF.resourcePath').'/'.$images[1][$k],$root.'/'.WSTConf("CONF.goodsLogo") . "\"  data-echo=\"".$root."/".WSTImg($images[1][$k],2), $goods['goodsDesc']);
        }
        if(!empty($goods)){
            $history = cookie("mo_history_goods");
            $history = is_array($history)?$history:[];
            array_unshift($history, (string)$goods['goodsId']);
            $history = array_values(array_unique($history));
            if(!empty($history)){
                cookie("mo_history_goods",$history,25920000);
            }
        }
        $goods['consult'] = model('GoodsConsult')->firstQuery($goods['goodsId']);
        $goods['appraises'] = model('GoodsAppraises')->getGoodsEachApprNum($goods['goodsId']);
        $goods['appraise'] = model('GoodsAppraises')->getGoodsFirstAppraise($goods['goodsId']);
		$this->assign("info", $goods);
		return $this->fetch($catInfo['mobileDetailTheme']);
	}
    /**
     * 搜索商品列表
     */
    public function search(){
        $keyword = WSTReplaceFilterWords(input('keyword'),WSTConf("CONF.limitWords"));
        hook('afterUserSearchWords',['keyword'=>input('keyword')]);
        $this->assign("keyword", $keyword);
        $this->assign("minPrice", input('minPrice/d'));
        $this->assign("maxPrice", input('maxPrice/d'));
        $this->assign("brandId", input('brandId/d'));
        return $this->fetch('goods_search');
    }
	/**
     * 商品列表
     */
    public function lists(){
        $catId = input('cat/d');
        $keyword = WSTReplaceFilterWords(input('keyword'),WSTConf("CONF.limitWords"));
        hook('afterUserSearchWords',['keyword'=>input('keyword')]);
    	$this->assign("keyword", $keyword);
    	$this->assign("catId", $catId);
        $this->assign("minPrice", input('minPrice/d'));
        $this->assign("maxPrice", input('maxPrice/d'));
    	$this->assign("brandId", input('brandId/d'));
        // 分类信息
        $catInfo = Db::name("goods_cats")->field("catName,seoTitle,seoKeywords,seoDes,mobileCatListTheme,showWay")->where(['catId'=>$catId,'dataFlag'=>1])->find();
        $this->assign("catInfo",$catInfo);
        return $this->fetch($catInfo['mobileCatListTheme']?$catInfo['mobileCatListTheme']:'goods_list');
    }
    /**
     * 获取列表
     */
    public function pageQuery(){
    	$m = model('goods');
    	$gc = new GoodsCats();
    	$catId = (int)input('catId');
        $data = []; 
    	if($catId>0){
    		$goodsCatIds = $gc->getParentIs($catId);
    	}else{
    		$goodsCatIds = [];
    	}

        //处理已选属性
        $vs = input('vs');
        $vs = ($vs!='')?explode(',',$vs):[];
        $data['arvs'] = $vs;
        $data['vs'][] = implode(',',$vs);

        $at = new AT();
        $goodsFilter = $at->listQueryByFilter((int)input('catId/d'));
        $ngoodsFilter = [];
        if(!empty($vs)){
            // 存在筛选条件,取出符合该条件的商品id,根据商品id获取可选属性进行拼凑
            $goodsId = model('goods')->filterByAttributes();

            $attrs = model('Attributes')->getAttribute($goodsId);
            // 去除已选择属性
            foreach ($attrs as $key =>$v){
                if(!in_array($v['attrId'],$vs)){$ngoodsFilter[] = $v;}
            }
        }else{
            // 当前无筛选条件,取出分类下所有属性
            foreach ($goodsFilter as $key =>$v){
                if(!in_array($v['attrId'],$vs))$ngoodsFilter[] = $v;
            }
        }
        $data['goodsPage'] = $m->pageQuery($goodsCatIds);
        foreach ($ngoodsFilter as $k => $val) {
           $result = array_values(array_unique($ngoodsFilter[$k]['attrVal']));

           $ngoodsFilter[$k]['attrVal'] = $result;
        }
        $data['goodsFilter'] = $ngoodsFilter;

    	foreach ($data['goodsPage']['data'] as $key =>$v){
    		$data['goodsPage']['data'][$key]['goodsImg'] = WSTImg($v['goodsImg'],3,'goodsLogo');
    		$data['goodsPage']['data'][$key]['praiseRate'] = ($v['totalScore']>0)?(sprintf("%.2f",$v['totalScore']/($v['totalUsers']*15))*100).'%':'100%';
    	}
        // `券`标签
        hook('afterQueryGoods',['page'=>&$data['goodsPage']]);
    	return $data;
    }

    /**
    * 浏览历史页面
    */
    public function history(){
        return $this->fetch('users/history/list');
    }
    /**
    * 获取浏览历史
    */
    public function historyQuery(){
        $rs = model('goods')->historyQuery();
        if(!empty($rs)){
	        foreach($rs['data'] as $k=>$v){
	            $rs['data'][$k]['goodsImg'] = WSTImg($v['goodsImg'],3,'goodsLogo');
	        }
        }
        return $rs;
    }

    /**
     * 生成海报
     */
    public function moCreatePoster(){
        $m = model('goods');
        $userId = (int)session("WST_USER.userId");
        $isNew = (int)input("isNew",0);
        $goodsId = (int)input("goodsId",0);
        $subDir =  'upload/shares/goods/'.date("Y-m");
        WSTCreateDir(WSTRootPath().'/'.$subDir);
        $today = date("Ymd");
        $fname = 'goods_qr_mo_'.$today.'_'.$goodsId.'_'.$userId.'.png';
        $outImg = $subDir.'/'.$fname;
        $shareImg = WSTRootPath().'/'.$outImg;
        if($isNew==0){
            if (file_exists($shareImg)) {
                return WSTReturn("",1,["shareImg"=>$outImg]);
            }
        }
        $qr_url = url('mobile/goods/detail',array('goodsId'=>$goodsId,'shareUserId'=>base64_encode($userId)),true,true);//二维码内容   
        //生成二维码图片   
        $qr_code = WSTCreateQrcode($qr_url,'','goods',3600,2);
        $qr_code = WSTRootPath().'/'.$qr_code;
        $rs = $m->createPoster($userId,$qr_code,$outImg);
        return $rs;
    }
}
