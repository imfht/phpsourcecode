<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 用户公共调用模块
 */
namespace app\system\controller\cms;
use app\common\controller\Admin;

class Common extends Admin{


    public function initialize() {
        parent::initialize();
        $this->view->engine->layout('admin/layout');
    }
}