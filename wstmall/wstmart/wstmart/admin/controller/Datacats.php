<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\DataCats as M;
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
 * 系统数据分类控制器
 */
class Datacats extends Base{
    /**
    * 根据
    */
    public function listQuery(){
        $m = new M();
        return $m->listQuery((int)Input("post.id",-1));
    }
    /**
     * 根据catId获取数据分类
     */
    public function get(){
    	$m = new M();
    	return $m->getById((int)Input("post.id"));
    }
    /**
     * 新增数据分类
     */
    public function add(){
    	$m = new M();
    	return $m->add();
    }
    /**
     * 编辑数据分类
     */
    public function edit(){
    	$m = new M();
    	return $m->edit();
    }
    /**
     * 删除数据分类
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
}
