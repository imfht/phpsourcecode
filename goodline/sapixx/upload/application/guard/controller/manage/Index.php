<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 前台管理演示
 */
namespace app\guard\controller\manage;
use app\common\controller\Manage;
use app\guard\model\Guard;

class Index extends Manage{

    /**
     * 默认方法方式
     * @return void
     */
    public function Index(){
        $view['guard'] = Guard::where(['member_miniapp_id' => $this->member_miniapp_id])->select();
        return view()->assign($view);
    }

}