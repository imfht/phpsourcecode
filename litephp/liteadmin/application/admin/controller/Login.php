<?php
/**
 * https://gitee.com/litephp
 * http://www.dazhetu.cn/
 * jay_fun 410136330@qq.com
 * Date: 2019/1/7
 * Time: 9:24
 */

namespace app\admin\controller;

use app\common\model\SystemAdmin;
use think\Controller;
use think\Db;
use think\facade\Session;

/**
 * @title 登陆
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{
    /**
     * @title 登陆操作
     *
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function login()
    {
        if (session('admin.id')){
            $this->redirect('admin/index/index');
        }
        if ($this->request->isGet()){
            return $this->fetch();
        }else{
            $username = $this->request->post(SystemAdmin::username(),false);
            $password = $this->request->post('password',false);
            $verify = $this->request->post('verify',false);

            (!$username || !$password || !$verify) && $this->error("缺少参数！");

            if (strlen($username) < 5 || strlen($username) > 20)
                $this->error("用户名在5-20字符之间！");
            if (strlen($password) < 5 || strlen($password) > 20)
                $this->error("用户名在5-20字符之间！");

            if (!captcha_check($verify)){
                $this->error("验证码错误！");
            }

            $user = SystemAdmin::where(SystemAdmin::username(),'=',$username)->find();
            if (empty($user))
                $this->error("用户名输入不正确！");
            if (!password_verify($password, $user['password']))
                $this->error("密码输入不正确！");
            if ($user['state'] !==1)
                $this->error("该用户已被禁用！");

            $options = [
                'cost'=>config('password.cost')
            ];
            if (password_needs_rehash($user['password'], PASSWORD_DEFAULT, $options)) {
                // 如果是这样，则创建新散列，替换旧散列
                $newpassword = password_hash($password, PASSWORD_DEFAULT, $options);
                $update_data['password']=$newpassword;
            }
            Session::set('admin',$user);
            // 最后登录时间
            $update_data['last_login'] = $this->request->time();
            SystemAdmin::where(SystemAdmin::username(),'=',$username)->update($update_data);

            $this->success("登录成功！",'admin/index/index');
        }
    }

    /**
     * @title 退出操作
     * @auth 1
     */
    public function logout()
    {
        Session::set('admin',null);
        return $this->redirect('admin/login/login');
    }
}