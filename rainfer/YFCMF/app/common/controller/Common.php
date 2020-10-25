<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------
namespace app\common\controller;

use app\admin\model\Admin as AdminModel;
use think\captcha\Captcha;
use think\Controller;
use think\facade\Env;
use think\facade\Lang;

/**
 * 公共控制器
 * @Author: rainfer <rainfer520@qq.com>
 */
class Common extends Controller
{
    protected $adminpath;
    protected $lang;
    protected $admin_base_layout_dir;

    protected function initialize()
    {
        parent::initialize();
        if (!defined('__ROOT__')) {
            define('__ROOT__', $this->request->rootUrl());
        }
        if (!file_exists(Env::get('root_path') . 'data/install.lock')) {
            //不存在，则进入安装
            header('Location: ' . url('install/Index/index'));
            exit();
        }
        $staticPath = __ROOT__ . '/public';
        $this->assign('static_path', $staticPath);
        $this->adminpath = config('yfcmf.adminpath');
        $this->assign('admin_path', $this->adminpath);
        $this->assign('admin_layout_base', './app/admin/view/public/base.html');
        $this->assign('admin_layout_base_content', './app/admin/view/public/base_content.html');
        // 多语言
        if (config('yfcmf.lang_switch_on')) {
            $this->lang = Lang::detect();
        } else {
            $this->lang = config('yfcmf.default_lang');
        }
        $this->lang = $this->lang ? : 'zh-cn';
        $this->assign('lang', $this->lang);
    }

    /**
     * 生成验证码
     *
     * @param string $id
     *
     * @return \think\Response
     */
    public function verify($id = '')
    {
        ob_end_clean();
        $captcha = new Captcha(config('yfcmf.verify'));
        return $captcha->entry($id);
    }

    /**
     * 检测管理员是否登录
     * @return bool
     */
    protected function checkAdminLogin()
    {
        $admin = new AdminModel();
        return $admin->isLogin();
    }

    /**
     * 验证码验证
     *
     * @param string $id
     */
    protected function verifyCheck($id = '')
    {
        $verify = new Captcha();
        if (!$verify->check(input('verify'), $id)) {
            $this->error('验证码错误', url($this->request->module() . '/Login/login'));
        }
    }
}
