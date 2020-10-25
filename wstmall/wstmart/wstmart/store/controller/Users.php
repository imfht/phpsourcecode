<?php
namespace wstmart\store\controller;
use wstmart\common\model\Users as MUsers;
use wstmart\common\model\LogSms;
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
 * 用户控制器
 */
class Users extends Base{
    protected $beforeActionList = ['checkAuth'];
    /**
     * 跳去修改个人资料
     */
    public function edit(){
        $m = new MUsers();
        //获取用户信息
        $userId = (int)session('WST_STORE.userId');
        $data = $m->getById($userId);
        $this->assign('data',$data);
        return $this->fetch('users/user_edit');
    }
    /**
     * 修改
     */
    public function toEdit(){
        $m = new MUsers();
        $rs = $m->edit();
        return $rs;
    }
    /**
     * 判断手机或邮箱是否存在
     */
    public function checkLoginKey(){
        $m = new MUsers();
        if(input("post.loginName"))$val=input("post.loginName");
        if(input("post.userPhone"))$val=input("post.userPhone");
        if(input("post.userEmail"))$val=input("post.userEmail");
        $userId = (int)session('WST_STORE.userId');
        $rs = WSTCheckLoginKey($val,$userId);
        if($rs["status"]==1){
            return array("ok"=>"");
        }else{
            return array("error"=>$rs["msg"]);
        }
    }
    
    /**
     * 修改密码
     */
    public function passedit(){
        $userId = (int)session('WST_STORE.userId');
        $m = new MUsers();
        $rs = $m->editPass($userId);
        return $rs;
    }
}

