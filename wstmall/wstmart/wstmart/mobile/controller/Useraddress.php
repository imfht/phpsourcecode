<?php
namespace wstmart\mobile\controller;
use wstmart\common\model\UserAddress as M;
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
class UserAddress extends Base{
	// 前置方法执行列表
    protected $beforeActionList = [
        'checkAuth'
    ];
	/**
	 * 地址管理
	 */
	public function index(){
		$m = new M();
		$userId = session('WST_USER.userId');
		$addressList = $m->listQuery($userId);
		//获取省级地区信息
		$area = model('areas')->listQuery(0);
		$this->assign('area',$area);
		$this->assign('list', $addressList);
		$this->assign('type', (int)input('type'));
		$this->assign('addressId', (int)input('addressId'));//结算选中的地址
		return $this->fetch('users/useraddress/list');
	}
	/**
	 * 获取地址信息
	 */
	public function getById(){
		$m = new M();
		return $m->getById(input('post.addressId/d'));
	}
	/**
	 * 设置为默认地址
	 */
	public function setDefault(){
		$m = new M();
		return $m->setDefault();
	}
	/**
     * 新增/编辑地址
     */
    public function edits(){
        $m = new M();
        if((int)input('addressId')>0){
        	$rs = $m->edit();
        }else{
        	$rs = $m->add();
        } 
        return $rs;
    }
    /**
     * 删除地址
     */
    public function del(){
    	$m = new M();
    	return $m->del();
    }
}
