<?php

namespace app\admin\controller;

use think\Request;
use think\Loader;
use think\Url;
use think\Config;
use think\View;
use Verify\verify;

/**
 * Description of Login
 * 用户登录
 * @author static7
 */
class Login {

    //引入jump类
    use \traits\controller\Jump;

    /**
     * 空操作
     * @author staitc7 <static7@qq.com>
     * @return mixed
     */
    public function _empty() {
        return View::instance([], Config::get('replace_str'))->fetch('common/error');
    }

    /**
     * 用户登录
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        is_login() && $this->redirect('Index/index');
        return View::instance([], Config::get('replace_str'))->fetch();
    }

    /**
     * 用户登录
     * @param string $username 用户名
     * @param string $password 密码
     * @param string $verify 验证码
     * @author staitc7 <static7@qq.com>
     */
    public function login($username = '', $password = '', $verify = '') {
        Request::instance()->isPost() || $this->error('非法请求'); //判断是否ajax登录
        check_verify($verify) || $this->error('验证码输入错误！');
        $userApi = Loader::model('UcenterMember', 'api');
        $user_id = $userApi->login($username, $password);
        $user_id < 0 && $this->error($this->loginError($user_id)); //登录失败
        $Member = Loader::model('Member');
        return $Member->login($user_id) ?
            $this->success('登录成功', Url::build('admin/Index/index')) :
            $this->error($Member->getError());
    }

    /**
     * 登录错误信息
     * @param int $code 错误信息
     * @return string
     */
    private function loginError($code) {
        switch ($code) {
            case -1:
                $error = '用户不存在或被禁用！';
                break; //系统级别禁用
            case -2:
                $error = '用户名或者密码错误！';
                break;
            default:
                $error = '未知错误！';
                break; // 0-接口参数错误（调试阶段使用）
        }
        return $error;
    }

    /**
     * 退出登录
     * @author staitc7 <static7@qq.com>
     */

    public function logout() {
        if (!is_login()) {
            return $this->redirect('Login/index');
        }
        Loader::model('Member')->logout();
        return $this->success('退出成功！', Url::build('Login/index'));
    }

    /**
     * 验证码
     * @author staitc7 <static7@qq.com>
     */
    public function verify() {
        $config = ['expire' => 30, // 验证码过期时间（s）
            'fontSize' => 18, // 验证码字体大小(px)
            'useCurve' => true, // 是否画混淆曲线
            'useNoise' => true, // 是否添加杂点
            'imageH' => 34, // 验证码图片高度
            'imageW' => 160, // 验证码图片宽度
        ];
        $verify = new verify($config);
        return $verify->entry(1);
    }

}
