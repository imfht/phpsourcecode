<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\Datas as M;
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
 * 系统数据控制器
 */
class Datas extends Base{
	
    public function index(){
    	return $this->fetch("list");
    }
    /**
    * 根据catId获取子数据
    */
    public function childQuery(){
        $m = new M();
        return WSTGrid($m->childQuery());
    }
    /**
     * 获取菜单列表
     */
    public function listQuery(){
    	$m = new M();
    	return $m->dataQuery((int)Input("post.id",-1));
    }
    /**
     * 获取菜单
     */
    public function get(){
    	$m = new M();
    	return $m->getById((int)Input("post.id"));
    }
    /**
     * 新增菜单
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
}
