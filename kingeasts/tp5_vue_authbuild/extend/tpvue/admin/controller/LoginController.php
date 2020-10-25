<?php
// 登录控制器       
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.bcahz.com, All rights reserved.
// +----------------------------------------------------------------------
// | Author: White to black <973873838@qq.com>
// +----------------------------------------------------------------------
namespace tpvue\admin\controller;


use think\captcha\Captcha;
use tpvue\admin\model\AdminModel;
use tpvue\admin\validate\LoginValidate;


class LoginController extends BaseController
{

    /**
     * 验证码设置
     * @return \think\Response
     */
    public function verify()
    {
        $config =    [
            // 验证码字体大小
            'fontSize'    =>    50,
            // 验证码位数
            'length'      =>    3,
            //纯数字
            'codeSet' => '0123456789',
        // 关闭验证码杂点
            'useNoise'    =>    false,
        ];
        $captcha = new Captcha($config);
        return $captcha->entry();
    }
    /**
     * [index 登录主页]
     */
    public function index($loginId = '', $password = '', $verify = '')
    {
        if ($this->request->isPost()) {
            $captcha = new Captcha();
            if (!$captcha->check($verify)) {
                $this->error('验证码错误');
            }
            // 需要校验的数据
            $data = [
                'loginId' => $loginId,
                'password' => $password,
                'verify' => $verify
            ];

            // 调用独立校验器
            $login_validate = new LoginValidate;

            // 数据校验
            if (!$login_validate->check($data)) {
                $this->error($login_validate->getError());
            }

            // 登录状态

            $uuid = AdminModel::login($loginId, $password, 6);

            if ($uuid < 0) {
                /* 登录失败根据模型数据处理任意扩展 */
                switch ($uuid) {
                    case -1:
                        $error = '用户不存在';
                        break;
                    case -2:
                        $error = '用户被禁用';
                        break;
                    case -3:
                        $error = '密码错误';
                        break;
                    default:
                        $error = '未知错误';
                        break;
                }
                $this->error($error);
            }

            $this->success('登录中...', 'admin/index/index');
        }
        return $this->fetch('login');
    }

    /**
     * 退出登录
     */
    public function loginOut() 
    {
        session(null);
        cookie(null);
        $this->redirect('admin/login');
    }
}
