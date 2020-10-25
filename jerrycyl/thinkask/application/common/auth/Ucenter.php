<?php
/*
+--------------------------------------------------------------------------
|   thinkask [#开源系统#]
|   ========================================
|   http://www.thinkask.cn
|   ========================================
|   如果有兴趣可以加群{开发交流群} 485114585
|   ========================================
|   更改插件记得先备份，先备份，先备份，先备份
|   ========================================
+---------------------------------------------------------------------------
 */

namespace app\common\auth;
use think\Cache;
use think\Config;
use think\Loader;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
use app\ucenter\model\Admin as AdminModel;
class Ucenter extends Auth
{
    public $log         = true;
    private $request;
    private $param;
    private $module;
    private $controller;
    private $action;
    private $field_user;
    private $field_uid;
    public function __construct()
    {
        $this->request      = Request::instance();
        $this->param        = $this->request->param();
        $this->module       = $this->request->module();
        $this->controller   = $this->request->controller();
        $this->action       = $this->request->action();
        $this->field_user   =Config::get('session.prefix').Config::get('auth_user');
        $this->field_uid    =$this->field_user.".".Config::get('auth_uid');

    }


    private function Auth_error($status,$msg='',$url='',$remark=[]){
        $data['status'] = $status;
        $data['msg']    = $msg;
        $data['url']    = $url;
        $data['remark'] = $remark;
        return $data;
    }

    /**
     * 权限访问清单
     * @access private
     * @param array     $where 查询附加条件
     * @param bool      $default  隐藏的菜单
     * @return array
     */
    private static function authMenu($where=[],$default = true){
        $uid        = self::sessionGet($this->field_uid);
        Db::table('auth_access')->where("uid = $uid")->select();
   
    }

    /**
     * 检测用户是否登录
     * @return mixed
     */
    public  function is_login(){
       if($this->getUid()<1){
            gourl(url('ucenter/user/login'));
       }
    }
    
    

    /**
     * 注销
     * @access private static
     * @return bool
     */
    public static function logout(){
        Cookie::delete(Config::get('cookie.prefix').Config::get('auth_uid'),$userinfo[Config::get('auth_uid')]);
        return true;
    }
    /**
     * [getUser 前台用户数据]
     * @Author   Jerry
     * @DateTime 2017-06-13T16:01:37+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function getUser(){
        if($this->getUid()){
            $userinfo = model('base')->getone('users',['where'=>['uid'=>$this->getUid()]]);
            unset($userinfo['password']);
        }else{
            $userinfo = "";
        }
        return $userinfo;
    }
    /**
     * [getUid 前台用户的UID]
     * @Author   Jerry
     * @DateTime 2017-06-13T16:01:45+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function getUid(){
        return Cookie::get(Config::get('cookie.prefix').Config::get('auth_uid'));
    }
    
    /**
     * [creatUser 前台用户]
     * @Author   Jerry
     * @DateTime 2017-06-12T16:17:47+0800
     * @Example  eg:
     * @param    [type]                   $userinfo [description]
     * @return   [type]                             [description]
     */
    public function creatUser($userinfo){
        // Session::set($this->field_user,$userinfo);
        Cookie::set(Config::get('cookie.prefix').Config::get('auth_uid'),$userinfo[Config::get('auth_uid')]);
        return Session::get($this->field_user);
    }

    /**
     * 数据签名认证
     * @access private static
     * @param  array  $data 被认证的数据
     * @return string       签名
     */
    private static  function data_auth_sign($data) {
        $code = http_build_query($data); //url编码并生成query字符串
        $sign = sha1($code); //生成签名
        return $sign;
    }
##==============================================================================================后台用户=========================================##

    /**
     * [creatAdmin 后台用户]
     * @Author   Jerry
     * @DateTime 2017-06-12T16:17:57+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function creatAdmin($userinfo){
        Cookie::set('admin_uid',$userinfo[Config::get('auth_uid')]);
        return $userinfo[Config::get('auth_uid')];
    }
    /**
     * [delAdmin 删除当前的后台用户（退出登陆）]
     * @Author   Jerry
     * @DateTime 2017-06-12T17:01:04+0800
     * @Example  eg:
     * @param    [type]                   $uid [description]
     * @return   [type]                        [description]
     */
    public function delAdmin(){
         Cookie::set('admin_uid',null);
         return true;
    }
    /**
     * [getUser 获得后台的用户数据信息]
     * @Author   Jerry
     * @DateTime 2017-06-12T16:33:17+0800
     * @Example  eg:
     * @return   [type]                   [description]
     */
    public function getAdminInfo(){
        if(Cookie::get('admin_uid')){
            $userinfo = AdminModel::f_uid_to_info(Cookie::get('admin_uid'));
            unset($userinfo['password']);
        }else{
            $userinfo = "";
        }
        return $userinfo;
    }





}