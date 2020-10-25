<?php
namespace wstmart\home\controller;
use wstmart\common\model\GoodsConsult as M;
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
 * 商品咨询控制器
 */
class GoodsConsult extends Base{
    protected $beforeActionList = ['checkAuth'=>['only'=>'myConsult,myConsultByPage']];
    /**
     * 根据商品id获取商品咨询
     */
    public function listQuery(){
        $m = new M();
        $rs = $m->listQuery();
        return $rs;
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        return $m->add();
    }
    /**
     * 用户-商品咨询
     */
    public function myConsult(){
        $this->assign('p',(int)input('p'));
        return $this->fetch('users/my_consult');
    }
    /**
     * 用户-商品咨询列表查询
     */
    public function myConsultByPage(){
        $m = new M();
        return $m->myConsultByPage();
    }
}
