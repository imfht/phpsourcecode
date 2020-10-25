<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 官网首页
 */
namespace app\system\controller\home;
use app\common\model\SystemMiniapp;

class Index extends Common{

    /**
     * 首页
     * @return void
     */
    public function index(){
        $view['list']  = SystemMiniapp::where(['is_lock' => 0,'is_diyapp' => 0])->order('id desc')->paginate(9);
        return view('/index')->assign($view);
    }

    /**
     * 应用详情
     * @return void
     */
    public function review(int $id){
        $view['info']  = SystemMiniapp::where(['id' => $id,'is_lock' => 0,'is_diyapp' => 0])->find();
        if(!$view['info']){
            return $this->error("404 NOT FOUND");
        }
        $view['style_pic'] =  empty($view['info']['style_pic']) ? [] :json_decode($view['info']['style_pic'],true);
        return view('/review')->assign($view);
    }
}