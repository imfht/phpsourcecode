<?php
namespace wstmart\home\controller;
use wstmart\home\model\ShopUsers as M;
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
 * 门店角色控制器
 */
class Shopusers extends Base{
    protected $beforeActionList = ['checkShopAuth'];

	/**
	 * 列表
	 */
	public function index(){
		$m = new M();
		$list = $m->pageQuery();
		$this->assign('list',$list);
		return $this->fetch("shops/shopusers/list");
	}
	
    /**
    * 查询
    */
    public function pageQuery(){
        $m = new M();
        return $m->pageQuery();
    }
    
    /**
     * 新增店铺管理员
     */
    public function add(){
    	$m = new M();
    	$object = $m->getEModel('shop_roles');
        $roles = model("ShopRoles")->listQuery();
		$data = ['object'=>$object,"roles"=>$roles];
    	return $this->fetch('shops/shopusers/add',$data);
    }
	
	/**
     * 新增店铺管理员
     */
    public function toAdd(){
    	$m = new M();
    	return $m->add();
    }
	
    /**
     * 修改店铺管理员
     */
    public function edit(){
    	$m = new M();
    	$object = $m->getById(input('get.id'));
		$roles = model("ShopRoles")->listQuery();
        $data = ['object'=>$object,"roles"=>$roles];
    	return $this->fetch('shops/shopusers/edit',$data);
    }

	/**
     * 编辑店铺管理员
     */
    public function toEdit(){
    	$m = new M();
    	return $m->edit();
    }
	
    /**
     * 删除操作
     */
    public function del(){
    	$m = new M();
    	$rs = $m->del();
    	return $rs;
    }
    
}
