<?php
namespace app\index\controller;
use think\Loader;
use think\Controller;
use Exception;
class Login extends Controller
{
    public function index()
    {
        $this->loadScript([
            'title'=>'用户登录-Conero','js'=>['login/index'],'css'=>['login/index']
        ]);
        $right = request()->param('right');
        if($right || $this->uLoginCkeck()){
            if(isset($_GET['url'])) $this->redirect($url);        
            $this->alertLeft("您已经登录系统了，页面将回转首页！");
            //go('/conero/');
            return '您已经成功登录';
        }
        $this->useFrontFk('bootstrap');
        $imgId = 'login_img';
        $this->_captcha($imgId);
        $this->_JsVar('imgId',$imgId);
        return $this->fetch('index');
    }
    public function doIndex()
    {
        $data = $_POST;
        if(isset($data['username']) && isset($data['pswd'])){
            $ret = $this->gLoginAuth($data['username'],$data['pswd']);
            if($ret == false) $this->error('密码或用户名错误');
            else{
                //$this->success('您已经成功登录！', 'login/index?right='.rand(100000,999999));
                go('/conero/');die;
            }
        }else $this->success('非法请求', 'login/index');
        //debugOut($data,true);
    }
    public function register()
    {
        $this->loadScript([
            'title'=>'用户注册-Conero','js'=>['login/register'],'css'=>['login/register']
        ]);
        $this->useFrontFk('bootstrap');
        return $this->fetch('register');
    }
    public function doRegister()
    {
        $data = $_POST;
        isset($data['aftersuccess']) or die('非法访问！');
        $afterS = $data['aftersuccess'];unset($data['aftersuccess']);
        if($data['command1'] == $data['command']){
            unset($data['command1']);
            $data['last_ip'] = request()->ip();
            $data['command'] = $this->_password($data['command'],$data['user_nick']);
            $ret = $this->_save('net_user',$data);
            if($ret){
                $text = '恭喜您，您已经成功注册！';
                switch ($afterS){
                    case 'moveon':{
                        $this->fastLoginAuth($data['user_nick']);
                        $this->success($text,'/conero/center/uinfo.html');break;// 跳转至用户编辑页面
                    }
                    case 'autoLogin':{
                        $this->fastLoginAuth($data['user_nick']);
                        $this->success($text,'index/index');break;// 自动登录后跳入首页
                    }
                    default:$this->success($text,'index/index');
                }
            }
            else $this->success('注册失败，数据存储过程中出错...');   
        }
        else $this->success('注册失败，原因是两次设置的密码不一致');
    }
    // 注销
    public function quit()
    {
        /*
        if($this->lockUser('is_locked')){
            echo '你的用户当前处于锁定状态，请<a href="">解锁</a>';
            return;
        }
        */
        $this->uLoginCkeck('quit');
        go('/conero/');
    }
    // 锁定
    public function lock(){
        $this->lockUser();
        go('/conero/index/login/unlock');
    }
    public function unlock()
    {        
        $this->loadScript([
            'title'=>'用户解锁-Conero','css'=>['login/unlock'],'js'=>['login/unlock']
        ]);
        $this->lockUser('try_to_unlock');
        return $this->fetch('unlock');
    }
    public function ajax()
    {
        isset($_POST['item']) or die('非法访问');
        $item = $_POST['item'];
        $ret = '';
        switch($item){
            case 'index/testimg':
                try{
                    if(captcha_check($_POST['code'])) $ret = 'Y';// session_start() 重复开启报错
                    else $ret = 'N';
                }catch(Exception $e){
                    $ret = 'Y';
                }
                break;
        }
        echo $ret;
    }
    // 登录 API - 2017年1月4日 星期三 - 用于外部登录接口
    public function authapi(){
        $data = $_POST;
        if(!empty($data)){
            list($key) = array_keys($data);
            $host = base64_decode($key);
            $sHost = $_SERVER["SERVER_NAME"];
            if($host == $sHost && !$this->uLoginCkeck()){
                // 执行登入操作
                $nick = $data[$key];
                $this->fastLoginAuth($nick);                
            }
        }
        urlBuild();
        /*
        println($data,$key,$host);
        phpinfo();
        */
    }
}