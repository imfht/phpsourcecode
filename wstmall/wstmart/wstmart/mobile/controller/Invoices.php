<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\Invoices as M;
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
 * 发票信息控制器
 */
class Invoices extends Base{
    // 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth',
    ];
    
    /**
     * 发票管理列表页
     */
    public function listQuery(){
        $m = new M();
        $data = $m->pageQuery();
        $this->assign('list', $data);
        return $this->fetch('users/invoices/list');
    }

    /**
     * 单条数据
     */
    public function get()
    {
        $m = new M();
        return WSTReturn("", 1,$m->getById(input('post.id')));
    }

    /**
     * 查询
     */
    public function pageQuery(){
        $m = new M();
        return $m->pageQuery();
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
     * 修改
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
