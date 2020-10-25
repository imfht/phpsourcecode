<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 管理首页
 */
namespace app\green\controller\Manage;
use app\common\controller\Manage;

class Index extends Manage{


    public $mini_program   = []; 

    public function initialize(){
        parent::initialize();
        $this->mini_program = ['member_miniapp_id' => $this->member_miniapp_id];
        $this->assign('pathMaps',[['name'=>'商户管理','url'=>url("device/index")]]);
    }

    /**
     * 商家关联
     * @return void
     */
    public function index(){
        $this->redirect(url('user/index'));
    }
}