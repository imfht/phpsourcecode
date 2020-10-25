<?php
namespace wstmart\store\controller;
use wstmart\common\model\Users as MUsers;
use wstmart\common\model\LogSms;
use wstmart\store\model\HomeMenus as HM;
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
 * 默认控制器
 */
class Index extends Base{
    protected $beforeActionList = ['checkAuth'=>['only'=>'index,main']];
    /**
     * 店铺主页
     */
    public function index(){
       $m = new HM();
       $ms = $m->getStoreMenus();
       $this->assign("sysMenus",$ms[2]);
       return $this->fetch('/index');
    }

  /**
   * 去登录
   */
  public function login(){
    $USER = session('WST_STORE');
    //如果已经登录了则直接跳去用户中心
    if(!empty($USER) && $USER['userId']!='' && $USER['userType']==1)$this->index();
    $loginName = cookie("loginName");
    if(!empty($loginName)){
        $this->assign('loginName',cookie("loginName"));
    }else{
        $this->assign('loginName','');
    }
    return $this->fetch('/login');
  }

  /**
     * 获取用户信息
     */
  public function getSysMessages(){
      $rs = model('Systems')->getSysMessages();
      return $rs;
  }

  /**
   * 验证登录
   *
   */
  public function checkLogin(){
    $rs = model('Users')->checkStoreLogin();
    return $rs;
  }

  /**
   * 用户退出
   */
  public function logout(){
    session('WST_STORE',null);
    return WSTReturn("退出成功，正在跳转页面", 1);
  }

  /**
   * 系统预览
   */
  public function main(){
    $s = model('store/shops');
    $data = $s->getShopSummary();
    $this->assign('data',$data);
    return $this->fetch("/main");
  }
  
}
