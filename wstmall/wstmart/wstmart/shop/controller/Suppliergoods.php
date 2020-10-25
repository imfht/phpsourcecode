<?php
namespace wstmart\shop\controller;
use wstmart\shop\model\SupplierGoods as M;
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
class Suppliergoods extends Base{
    protected $beforeActionList = ['checkAuth'];
    
    /**
     * 检测商品是否已copy到店铺
     */
    public function checkHasCopy(){
        $goodsId = (int)input('goodsId/d');
        $shopId = (int)session("WST_USER.shopId");
        $m = new M();
        $hasCopy = $m->checkHasCopy($shopId,$goodsId);
        return WSTReturn("",1,['hasCopy'=>$hasCopy]);
    }
	/**
     * 查看商品详情
     */
    public function detail(){
    	$m = new M();
    	$goods = $m->getBySale(input('goodsId/d',0));
    	if(!empty($goods)){
            // 商品详情延迟加载
            $rule = '/<img src="\/(upload.*?)"/';
            preg_match_all($rule, $goods['goodsDesc'], $images);
            foreach($images[0] as $k=>$v){
                $goods['goodsDesc'] = str_replace($v, "<img class='goodsImg' data-original=\"".str_replace('/index.php','',request()->root())."/".WSTImg($images[1][$k],3)."\"", $goods['goodsDesc']);
            }
	    	$this->assign('goods',$goods);
            $this->assign('supplier',$goods['supplier']);
	    	return $this->fetch('supplier/goods_detail');
    	}else{
    		return $this->fetch("supplier/error_lost");
    	}
    }

    

    public function toAdd(){
        $m = new M();
        return $m->add();
    }
}
