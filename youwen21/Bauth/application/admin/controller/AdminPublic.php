<?php
namespace app\admin\controller;

use think\Controller;
use think\captcha;
use think\Session;
use app\admin\model\Administrator as adminModel;

/**
 * 管理后台 登录
 */
class AdminPublic extends Controller {

    public function index() {
        return 'hahaadfsfasdfsa';
    }
    public function _initialize() {

    }
    /**
     * 用户登录
     * @return  [type]  [description]
     * @author baiyouwen
     */
    public function login() {
        if ($this->request->isPost()) {

            // if (!captcha_check($this->request->param('verify'), 'admin_login')) {
            //     $this->error('验证码不正确');
            // }

            $username = $this->request->param('username');
            $password = $this->request->param('password');
            // $model = new adminModel();
            $info = (new adminModel())->login($username, $password);
            if ($info['code']) {
                $uid =  Session::get('userInfo.id');
                if(!$uid){
                    $this->error('登录错误');
                }
                //后台系统用户行为记录
                \app\lib\BehaviorRecording::writeLog($uid, 'AdminPublic', 'login', '登录');

                return $this->success('登录成功', url('Index/index'));
            } else {
                $this->error($info['msg']);
            }

        }
        return $this->fetch();
    }

    /* 退出登录 */
    public function logout() {
        if ($user_id = is_login()) {
            //后台系统用户行为记录
            \app\lib\BehaviorRecording::writeLog($user_id, 'AdminPublic', 'logout', '退出登录');
            (new adminModel())->logout();
            // $_SESSION = null;
            return $this->success('退出成功！', url('login'));
        } else {
            $this->redirect('login');
        }
    }

    /**
     * 生成后台登录验证码
     * @return  [type]  [description]
     * @author baiyouwen
     */
    public function verify() {
        return captcha('admin_login');
        // $captcha = new Captcha();
        // return $captcha->entry();
    }
}
