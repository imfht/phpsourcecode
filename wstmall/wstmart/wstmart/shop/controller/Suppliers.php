<?php
namespace wstmart\shop\controller;
use think\Db;
use wstmart\shop\model\SupplierGoods;
use wstmart\common\model\GoodsCats;
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
 * 门店控制器
 */

class Suppliers extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 店铺详情
     */
    public function index(){
    	$supplierId = (int)input("supplierId");
    	$s = model('suppliers');
    	$data['supplier'] = $s->getSupplierInfo($supplierId);
    	if(empty($data['supplier']))return $this->fetch('supplier/error_lost');
    	$g = model('SupplierGoods');
    	$data['list'] = $g->supplierGoods($supplierId);
        $supplierCats= $g->listSupplierCats(0,8,$supplierId);
        $this->assign('supplierCats',$supplierCats);
    	$this->assign('data',$data);
        $this->assign('goodsName',input("goodsName"));//筛选条件
        $this->assign('msort',(int)input("param.msort",0));//筛选条件
        $this->assign('mdesc',(int)input("param.mdesc",1));//升降序
        $this->assign('sprice',input("param.sprice"));//价格范围
        $this->assign('eprice',input("param.eprice"));
        $this->assign('ct1',(int)input("param.ct1",0));//一级分类
        $this->assign('ct2',(int)input("param.ct2",0));//二级分类
        $this->assign('supplierId',$supplierId);//店铺id
    	return $this->fetch('supplier/supplier_home');
    }
    
    /**
     * 店铺分类
     */
    public function goods(){
    	$s = model('suppliers');
    	$supplierId = (int)input("param.supplierId/d");
    	$data['supplier'] = $s->getShopInfo($supplierId);
    	$ct1 = input("param.ct1/d",0);
    	$ct2 = input("param.ct2/d",0);
    	$goodsName = input("param.goodsName");
    	if(empty($data['supplier']))return $this->fetch('error_lost');
    	$g = model('goods');
    	$data['list'] = $g->supplierGoods($supplierId);
    	$this->assign('msort',input("param.msort/d",0));//筛选条件
    	$this->assign('mdesc',input("param.mdesc/d",1));//升降序
    	$this->assign('sprice',input("param.sprice"));//价格范围
    	$this->assign('eprice',input("param.eprice"));
    	$this->assign('ct1',$ct1);//一级分类
    	$this->assign('ct2',$ct2);//二级分类
    	$this->assign('goodsName',urldecode($goodsName));//搜索
    	$this->assign('data',$data);
    	return $this->fetch('supplier/supplier_goods_list');
    }

}
