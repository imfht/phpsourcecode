<?php
namespace App\Controller\Admin;

use App\BaseController\AdminBaseController as Base;
use Swoole;
/**
 * 后台登录控制器
 * @package App\Controller\Admin
 */
class Login extends Base
{
    public function __construct(\Swoole $swoole)
    {
        parent::__construct($swoole);
    }

    /**
     * 登录界面
     */
    public function index()
    {
        $error = '';

        $this->session->start();
        //已经登录了，跳转到
        if ($this->user->isLogin())
        {
            $this->http->redirect('/Admin/Index/index');
            return;
        }
        $this->display();
    }

    /**
     * 登录提交
     */
    public function loginpost()
    {
        if (!$_POST)
        {
            $this->showMsg('error', '请勿非法操作');
        }
        $this->session->start();
        try{
            if (!$_POST['username']){
                throw new \Exception('请输入管理账号');
            }
            if (!$_POST['password']){
                throw new \Exception('请输入管理密码');
            }
            if (!$_POST['captcha']){
                throw new \Exception('请输入验证码');
            }
            if (strtolower($_POST['captcha']) != strtolower($_SESSION['vcode'])){
                throw new \Exception('验证码错误');
            }
            \Swoole\Auth::$username = 'account';
            \Swoole\Auth::$lastlogin = 'loginTime';
            \Swoole\Auth::$lastip = 'loginIp';
            //使用crypt密码
            \Swoole\Auth::$password_hash = \Swoole\Auth::HASH_SHA1;
            //设置查询数据库字段
            $this->user->select = 'id,groupId,userName,account,password,email,isDel';
            $r = $this->user->login(trim($_POST['username']), trim($_POST['password']));
            if (!$r)
            {
                throw new \Exception('登录失败,账号或密码错误');
            }
            $userinfo = $this->user->getUserInfo();
            if ($userinfo['isDel']){
                $this->user->logout();
                throw new \Exception('您的账号已被禁用，请联系管理员');
            }
            $this->user->updateStatus();

            $redirectUrl = isset($_POST['refer']) && $_POST['refer'] ? $_POST['refer'] : '/Admin/Index/index/';
            return $this->showMsg('success', '登录成功', $redirectUrl);
        }catch (\Exception $e){
            return $this->showMsg('error', $e->getMessage());
        }
    }
    /**
     * 退出登录
     */
    public function logout()
    {
        if($this->user->logout()){
            $this->http->redirect('/Admin/Login/index');
        }else{
            $this->http->redirect('/Admin/Index/index');
        }
    }

    /**
     * 验证码
     */
    public function captcha()
    {
        //输出格式为图片
        $this->http->header('Content-Type', 'image/png');
        //生成验证码
        $verifyCode = Swoole\Image::verifycode_gd();
        //将验证码数字写入session
        $_SESSION['vcode'] = $verifyCode['code'];
        return $verifyCode['image'];
    }
}