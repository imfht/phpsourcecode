<?php
namespace app\ebcms\controller;
use think\Controller;
class Auth extends Controller
{
    
    public function _initialize()
    {
        \think\Session::boot();
        \think\Session::prefix(\think\Config::get('session.prefix').'admin');
    }

    // 登录
    public function login()
    {

        if (!isset($_SESSION['login_auth'])) {
            $this->redirect('index/index/index');
        }

        // 登陆页面
        if (request()->isGet()) {
            $this->assign('seo', [
                'title' => '欢迎登陆易贝内容管理系统',
                'keywords' => '欢迎登陆易贝内容管理系统',
                'description' => '欢迎登陆易贝内容管理系统',
            ]);
            return $this->fetch();
        } elseif (request()->isPost()) {

            // 验证验证码
            if (false !== \ebcms\Config::get('user.login_captcha')) {
                $captcha = new \think\captcha\Captcha();
                if (!$captcha->check(input('captcha'), 'adminauth')) {
                    $this->error('验证码错误！');
                }
            }
            
            // 读取该账户
            $where = array(
                'email' => input('email'),
            );
            if (!$manager = \think\Db::name('manager')->where($where)->find()) {
                $this->error('信息错误，请重新输入！');
            }
            
            // 判断账户状态
            if ($manager['status'] != 1) {
                $this->error('账户未通过审核！');
            }

            // 判断密码是否正确
            if ($manager['password'] !== \ebcms\Func::crypt_pwd(input('password'), $manager['email'])) {
                $this->error('密码错误！');
            }

            \think\Db::transaction(function() use($manager){
                // 更新数据库
                $data = array(
                    'login_ip'      =>  request()->ip(),
                    'login_time'    =>  time(),
                    'login_times'   =>  $manager['login_times'] + 1
                );
                \think\Db::name('manager') -> where('id',$manager['id']) -> update($data);

                unset($_SESSION['login_auth']);
                // 超级管理员识别
                if ($manager['email'] == \think\Config::get('super_admin')) {
                    \think\Session::set('super_admin', true);
                }
                // 写入新session
                \think\Session::set('manager_id', $manager['id']);
                \think\Session::set('manager_email', $manager['email']);
                \think\Session::set('manager_nickname', $manager['nickname']);
                \think\Session::set('manager_avatar', $manager['avatar']);
                unset($_SESSION['login_auth']);
            });
            $this->success('登陆成功!', 'ebcms/index/index');

        }
    }

    // 退出
    public function logout()
    {
        if (request()->isGet()) {
            \think\Session::clear();
            $this->success('退出成功');
        }
    }

    public function captcha(){
        $captcha = new \think\captcha\Captcha();
        return $captcha -> entry('adminauth');
    }

}