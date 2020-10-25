<?php
namespace wstmart\admin\controller;
use wstmart\admin\model\LimitWords as M;
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
 * 系统禁用关键字控制器
 */
class Limitwords extends Base{
	
    public function index(){
    	return $this->fetch("list");
    }
    
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	return WSTGrid($m->pageQuery());
    }

    /**
     * 获取禁用关键字内容
     */
    public function get(){
        $m = new M();
        return $m->get((int)Input("post.id"));
    }

    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }

    /**
     * 编辑
     */
    public function edit(){
        $m = new M();
        return $m->edit();
    }

    /**
     * 删除
     */
    public function del(){
        $m = new M();
        return $m->del();
    }
}
