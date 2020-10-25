<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 服务中心
 */
namespace app\system\controller\passport;

class Service extends Common{


    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'服务中心','url'=>url("system/passport.service/index")]]);
    }

    /**
     * 帐号管理
     * @access public
     */
    public function index(){

    }
}