<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 前台管理演示
 */
namespace app\demo\controller\manage;
use app\common\controller\Manage;

class Index extends Manage{

    /**
     * 默认方法方式
     * @return void
     */
    public function Index(){
        $view['hello'] = 'Hello!SAPI++ Passport Page';
        return view()->assign($view);
    }

}