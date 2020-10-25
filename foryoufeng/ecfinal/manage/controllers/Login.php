<?php
/**
 * 管理员登录
 * Created by PhpStorm.
 * User: root
 * Date: 7/15/16
 * Time: 11:12 AM
 */
include_once(dirname(UTILS) . '/Imagecode/GeetestLib.class.php');//引用图片验证码
class Login extends Base{

    protected $nocheck=array('code'=>3,'msg'=>'请重新刷新验证码');
    protected $nouser=array('code'=>4,'msg'=>'没有这个用户');
    protected $nologin=array('code'=>6,'msg'=>'用户名或者密码错误');
    protected $wrong=array('code'=>2,'msg'=>'所填信息不完整');
    public function init(){
        $model=M('adminuser');
        $this->model=$model;
    }
    /**
     * 用户登录
     */
    public function index(){
        if($_POST){
            $name=trim(I('name'));
            $password=trim(I('password'));
            $change=trim(I('change'));
            $validate=trim(I('validate'));
            $seccode=trim(I('seccode'));

            if($_SESSION['login_times']>2){
                $_SESSION['login_times']=1;
                $_SESSION['check_code']=1;
                $this->ajax($this->wrong);
            }
            if(isset($_SESSION['login_times'])){
                $_SESSION['login_times']++;
            }else{
                $_SESSION['login_times']=1;
            }
            if(isset($_SESSION['check_code'])){
                $sdk=new GeetestLib('8173bd226277b8280aa86afeb41ea940','0cae794b55ab463db9197ac5c1f4dc33');
                if($_SESSION['gtserver']==1){
                    $flag=$sdk->success_validate($change,$validate,$seccode,$_SESSION['gtid']);
                }else{
                    $flag=$sdk->fail_validate($change,$validate,$seccode);
                }
                if(!$flag){
                    $this->error('请重新刷新验证码');
                }
            }
            $where['user_name|email']=$name;
            $where['seller_id']=0;
            $data=$this->model->where($where)->find();
            if($data){
                if($data['ec_salt']){
                    $sendpwd=md5(md5($password).$data['ec_salt']);
                }else{
                    $sendpwd=md5($password);
                }
                if($sendpwd==$data['password']){

                    $this->user_update($data);//更新用户登陆信息

                    $this->success('登录成功');
                }else{
                    $this->error('用户名或者密码错误');
                }
            }else{
                $this->error('没有这个用户');//用户名不存在
            }
        }else{

            $host=trim(I('redirect'))?:'?c=home&a=index';

            if(session('user_id')){
                header("?c=home&a=index");
            }

            if($_COOKIE['nickname']){
                $this->assign('nickname', $_COOKIE['nickname']);//用户名
            }
            $this->assign('config',$this->config);
            $this->assign('page_title', $this->config['ww'].'--管理中心');//标题
            $this->assign('keywords', $this->config['ww']);//关键词
            $this->assign('description', $this->config['ww']);//描述
            $this->assign('redirect',$host);//跳转域名
            $this->assign('check_code',$_SESSION['check_code']?$_SESSION['check_code']:0);
            $this->display('login/admin_login.html');
        }

    }
    /**
     * 登出
     */
    public function logout(){
        $_SESSION=array();
        session_destroy();
        header("Location:?c=login");
    }

    /**
     * 更新用户信息
     * @param $user_id
     */
    private function user_update($data){

        $_SESSION['login_times']=1;
        $_SESSION['check_code']=null;
        $_SESSION['admin_id']=$data['user_id'];
        $_SESSION['admin_name']=$data['user_name'];

        setCookie("nickname", $data['user_name'],time()+3600*30*100);

        if('admin'==$data['user_name']){
            //超级管理员
            session('action_code','all');
            session('role_ids',-1);
        }else{
            //用户权限
            $role_ids=M('adminrole')->role_id($data['user_id']);
            session('role_ids',implode(',',$role_ids));
            $action_code=[];
            foreach($role_ids as $role_id){
                $action_code=array_merge($action_code,M('roleaction')->show_code($role_id));
            }
            //保存用户权限
            session('action_code',implode(',',$action_code).',home_index');
        }


        $info=[
            'last_login'=>time(),
            'last_ip'=>real_ip(),
        ];
        $this->model->where(['user_id'=>$data['user_id']])->save($info);
    }

    /**
     * 验证码
     */
    public function code(){
        $sdk=new GeetestLib('8173bd226277b8280aa86afeb41ea940','0cae794b55ab463db9197ac5c1f4dc33');
        $user_id='1906592238@qq.com';
        $status=$sdk->pre_process($user_id);
        $_SESSION['gtserver']=$status;
        $_SESSION['gtid']=$user_id;
        $sdk->pre_process();
        echo $sdk->get_response_str();
        return;
    }
}
