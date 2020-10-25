<?php
namespace wstmart\shop\controller;
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
 * 用户地址控制器
 */

class Supplieruseraddress extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
    * 设置为默认地址
    */
    public function setDefault(){
        return model('userAddress')->setDefault();
    }

    /**
    * 获取地址信息
    * 1.购物车结算有引用
    */
    public function listQuery(){
        //获取用户信息
        $userId = (int)session('WST_USER.userId');
        if(!$userId){
            return WSTReturn('未登录', -1);
        }
        $list = model('common/userAddress')->listQuery($userId);
        return WSTReturn('', 1,$list);
    }
	

	/**
     * 新增
     */
    public function add(){
        $m = model('userAddress');
        $rs = $m->add();
        return $rs;
    }
	/**
    * 修改
    */
    public function toEdit(){
        $m = model('userAddress');
        $rs = $m->edit();
        return $rs;
    }
    /**
    * 删除
    */
    public function del(){
    	$m = model('userAddress');
        $rs = $m->del();
        return $rs;
    }
    
    /**
     * 获取用户地址
     */
    public function getById(){
    	$m = model('userAddress');
        $id=(int)input('id');
        $data = $m->getById($id);
        return WSTReturn('', 1,$data);
    }
}
