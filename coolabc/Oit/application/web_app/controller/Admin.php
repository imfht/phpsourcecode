<?php
namespace app\web_app\controller;

use app\common\api\Menu;
use app\common\controller\OitBase;
use app\common\api\Dict;
use app\common\api\RBAC;
use app\common\api\Para;
use app\common\logic\MupLogic;
use think\Config;
use think\Db;
use think\Log;

/**
 * 需要继承OitBase的fetch方法
 * Class Manager
 * @package app\entrance\controller
 */
class Admin extends OitBase {
    // 登录界面
    /**
     * @return string
     */
    public function index() {
        Para::system_lang();            // 记忆语言
        //$frame_nag = Config::parse(APP_PATH . 'extra/oit_frame_nag_group_func.json');
        //dump($frame_nag);
        return $this->fetch();
    }

    /**
     * 主操作界面
     */
    public function main() {
        if (RBAC::not_login()) {
            $this->error(lang('没有登陆'));
        }

        $menu_data = Menu::get_menu_data();
        $this->assign('menu_data', json_encode($menu_data));

        return $this->fetch();
    }

    /**
     * 检测登录
     * 1、用户是否合法
     * 2、系统参数缓存（所有用户共用）
     * 3、用户参数缓存（session）
     */
    public function login() {
        $login_info = MupLogic::check_login();
        if (!is_array($login_info)) {
            $this->error($login_info);
        }

        Dict::init_static_list();              // 初始化静态字典列表
        Para::system_para();                        // 初始化系统参数
        Para::user_login($login_info);              // 缓存用户信息
        Para::user_bo();                            // 缓存限定
        RBAC::access_init();                        // 用户权限初始化

        $this->success(lang('登陆成功'), url("main"));
    }

    // 用户登出
    public function logout() {
        session(null); // 清空session
        $this->success(lang('退出成功!'), url('index'));
    }
}
