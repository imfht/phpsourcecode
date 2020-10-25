<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用管理首页
 */
namespace app\popupshop\controller\Manage;
use app\common\controller\Manage;
use think\facade\Request;
class Index extends Manage{

    public function initialize() {
        parent::initialize();
        $this->assign('pathMaps', [['name'=>'管理首页','url'=>'javascript:;']]);
    }

    /**
     * 应用管理首页
     * @access public
     */
    public function Index(){
        return redirect('bank/statistics');
    }    
}