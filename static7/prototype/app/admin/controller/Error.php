<?php

namespace app\admin\controller;

use think\View;
use think\Config;

/**
 * Description of Error
 * 空控制器
 * @author static7
 */
class Error { 

    /**
     * 空操作
     * @author staitc7 <static7@qq.com>
     * @return mixed   
     */
    public function index() {
        return View::instance([], Config::get('replace_str'))->fetch('common/error');
    }

}
