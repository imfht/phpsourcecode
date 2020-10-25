<?php

namespace app\home\controller;

use think\Loader;
use think\Request;
use think\Url;
use think\Db;
use think\Config;
use think\View;

/**
 * Description of Static7
 * 前台公共控制
 * @author static7
 */
class Static7 {

    //引入jump类
    use \traits\controller\Jump;

    //当前用户
    protected $uid;
    //视图
    protected $view;

    public function __construct() {
//        if (!is_file(APP_PATH . 'database.php')) {
//            return $this->redirect('install/index/index');
//        }
        $this->view = View::instance([], Config::get('replace_str'));
    }

}
