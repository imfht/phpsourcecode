<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 后台页面
 */
namespace app\demo\controller\admin;
use app\common\controller\Admin;

class Index extends Admin{


    /**
     * 默认方法方式
     * @return void
     */
    public function Index(){
        $view['hello'] = 'Hello!SAPI++ Manage Page';
        return view()->assign($view);
    }
}