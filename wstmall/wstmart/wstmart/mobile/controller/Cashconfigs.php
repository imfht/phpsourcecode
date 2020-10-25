<?php
namespace wstmart\mobile\controller;
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
 * 提现账号控制器
 */
class Cashconfigs extends Base{
    // 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth',
    ];
	/**
     * 查看提现账号
     */
	public function index(){
        $this->assign('area',model('areas')->listQuery(0));
        $this->assign('banks',model('banks')->listQuery(0));
		return $this->fetch('users/cashconfigs/list');
    }

    /**
     * 获取用户数据
     */
    public function pageQuery(){
        $userId = (int)session('WST_USER.userId');
        $data = model('CashConfigs')->pageQuery(0,$userId);
        return WSTReturn("", 1,$data);
    }
    /**
    * 获取记录
    */
    public function getById(){
       $id = (int)input('id');
       return model('CashConfigs')->getById($id);
    }
    /**
     * 新增
     */
    public function add(){
        return model('CashConfigs')->add();
    }
    /**
     * 编辑
     */
    public function edit(){
        return model('CashConfigs')->edit();
    }
    /**
     * 删除
     */
    public function del(){
        return model('CashConfigs')->del();
    }
}
