<?php

namespace app\admin\controller;

use think\Config;

class Index extends Admin {

    /**
     * 后台首页
     * @param 类型 参数 参数说明
     * @author staitc7 <static7@qq.com>
     */
    public function index() {
        $this->view->metaTitle = '首页';
        return $this->view->fetch();
    }

}
