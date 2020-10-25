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

namespace app\common\Auth;
use think\Cache;
use think\Config;
use think\Loader;
use think\Request;
use think\Session;
use think\Cookie;
use think\Db;
class Rbac
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
        // $this->field_user   =Config::get('session.prefix').Config::get('auth_user');
        // $this->field_uid    =$this->field_user.".".Config::get('auth_uid');

    }

    /**
     * 权限认证
     * @access public
     * @return mixed
     */
    public  function auth(){
        $controller         = Loader::parseName($this->controller,1); //字符串命名风格转换
        $rule               = strtolower("{$this->module}/{$controller}/{$this->action}");
        //是否需要验证 (组,控制器,方法.)
        if(!in_array(strtolower($this->module),Config::get('auth_modules'))&&!in_array(strtolower($this->controller), Config::get('auth_controller'))&&!in_array(strtolower($this->action), Config::get('auth_actiion'))){
            return true;
        }
     
         //白名单验证地址
        if(in_array($rule, strtolower(trim(Config::get('white_role'),'/')))){
            return true;
        }

        //是否登陆
        if(!$uid = self::cookieGet()){
            return $this->Auth_error(0,'请先登陆',url('ucenter/admin/login'));
        }
        //是否为超级管理员
        if(in_array($uid, Config::get('super_manager'))){
            return true;
        }
        //黑名单地址
        if(!in_array($rule, strtolower(trim(Config::get('black_role'),'/')))&&!in_array($uid, Config::get('super_manager'))){
            return $this->Auth_error(0,'权限不足');
        }

        //是否为白名单用户
        if(in_array($uid, Config::get('white_uid'))){
            $this->createLog($uid,'','白名单认证');
            return true;
        }
        //验证黑名单用户且非超管
        if(in_array($uid, Config::get('black_uid'))&&!in_array($uid, Config::get('super_manager'))){
           return  $this->Auth_error(0,'权限不足');
        }

        // 验证用户权限
        $authMenu   = Cache::get('authMenu_'.$uid);
        if(!$authMenu){ //存入缓存 授权菜单
            $authMenu   = self::authMenu();
            Cache::set('authMenu_'.$uid,$authMenu,600);
        }

        

    }
    private function Auth_error($status,$msg='',$url='',$remark=[]){
        $data['status'] = $status;
        $data['msg']    = $msg;
        $data['url']    = $url;
        $data['remark'] = $remark;
        return $data;
    }
    /**
     * 创建行为日志
     * @param  string       $logrule    行为日志规则
     * @param  string       $title      标题
     * @param  int          $uid        执行者ID
     * @return array
     */
    public function  createLog($uid,$title,$remark=''){
        if($this->log===false){
            return true;
        }
        $param  = $this->param;
        $condition = '';
        $command   = preg_replace('/\{(\w*?)\}/', '{$param[\'\\1\']}', $logrule);
        @(eval('$condition=("' . $command . '");'));

        $data   = [
            'action_ip'     => ip2long($this->request->ip()),
            'username'      => self::cookieGet('user.nickname'),
            'create_time'   => time(),
            'log_url'       => '/'.$this->request->pathinfo(),
            'log'           => $condition,
            'user_id'       => $uid,
            'title'         => $title,
            'remark'        =>$remark
        ];
        Db::table('auth_log')->add($data);
    }

 

    /**
     * 权限访问清单
     * @access private
     * @param array     $where 查询附加条件
     * @param bool      $default  隐藏的菜单
     * @return array
     */
    private static function authMenu($where=[],$default = true){
        $uid        = self::cookieGet($this->field_uid);
        Db::table('auth_access')->where("uid = $uid")->select();
   
    }

    /**
     * 读取session
     * @access private static
     * @param  string  $path 被认证的数据
     * @return mixed
     */
    private static function cookieGet(){
         return Cookie::get('admin_uid');
    }


}