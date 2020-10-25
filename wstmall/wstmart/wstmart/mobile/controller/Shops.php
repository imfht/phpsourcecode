<?php
namespace wstmart\mobile\controller;
use think\Db;
use wstmart\common\model\GoodsCats;
use wstmart\mobile\model\Goods;
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
 * 门店控制器
 */
class Shops extends Base{
    /**
     * 店铺街
     */
    public function shopStreet(){
    	$gc = new GoodsCats();
    	$goodsCats = $gc->listQuery(0);
    	$this->assign('goodscats',$goodsCats);
        $keyword = WSTReplaceFilterWords(input('keyword'),WSTConf("CONF.limitWords"));
    	$this->assign("keyword", $keyword);
    	return $this->fetch('shop_street');
    }
    /**
     * 店铺首页
     */
    public function index(){
        $s = model('shops');
        $shopId = (int)input('shopId',1);
        $data = [];
        $data['shop'] = $s->getShopInfo($shopId);
        $this->assign('data',$data);
        $this->assign("goodsName", input('goodsName'));
        $this->assign('ct1',(int)input("param.ct1/d",0));//一级分类
        $this->assign('ct2',(int)input("param.ct2/d",0));//二级分类
        $this->assign('shopId',$shopId);//店铺id
        
        $sm = model("common/ShopCats");
        $goodsCat = $sm->listQuery($shopId,0);
        $this->assign('goodsCat',$goodsCat);
        
        return $this->fetch($data['shop']["mobileShopHomeTheme"]);
    }
    /**
    * 店铺详情
    */
    public function view(){
        $s = model('shops');
        $shopId = (int)input("param.shopId/d",1);
        $data = [];
        $data['shop'] = $s->getShopInfo($shopId);
        $this->assign('data',$data);
        $cart = model('carts')->getCartInfo();
        $this->assign('cart',$cart);
        return $this->fetch('shop_view');
    }
    /**
    * 店铺商品列表
    */
    public function goods(){
        $s = model('shops');
        $shopId = (int)input("param.shopId/d",1);
        $ct1 = input("param.ct1/d",0);
        $ct2 = input("param.ct2/d",0);
        $goodsName = input("param.goodsName");
        $gcModel = model('ShopCats');
        $data = [];
        $data['shop'] = $s->getShopInfo($shopId);
        $data['shopcats'] = $gcModel->getShopCats($shopId);
        $this->assign('shopId',$shopId);//店铺id
        $this->assign('ct1',$ct1);//一级分类
        $this->assign('ct2',$ct2);//二级分类
        $goodsName = WSTReplaceFilterWords($goodsName,WSTConf("CONF.limitWords"));
        hook('afterUserSearchWords',['keyword'=>$goodsName]);
        $this->assign('goodsName',urldecode($goodsName));//搜索
        $this->assign('data',$data);

        return $this->fetch('shop_goods_list');
    }
    /**
    * 获取店铺商品
    */
    public function getShopGoods(){
        $shopId = (int)input('shopId',1);
        $g = model('goods');
        $rs = $g->shopGoods($shopId);
        foreach($rs['data'] as $k=>$v){
            $rs['data'][$k]['goodsImg'] = WSTImg($v['goodsImg'],3,'goodsLogo');
        }
        return $rs;
    }


    public function getFloorData(){
        $m = model("common/Goods");
        $rs = $m->shopCatPageQuery(3);
        return $rs;
    }

    /**
     * 店铺街列表
     */
    public function pageQuery(){
    	$m = model('shops');
    	$rs = $m->pageQuery(input('pagesize/d'));
    	foreach ($rs['data'] as $key =>$v){
    		$rs['data'][$key]['shopImg'] = WSTImg($v['shopImg'],3,'shopLogo');
    	}
    	return $rs;
    }

}
