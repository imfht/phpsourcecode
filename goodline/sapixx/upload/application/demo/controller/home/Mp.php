<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 公众号页面访问
 */
namespace app\demo\controller\home;
use app\common\controller\Official;

class Mp extends Official{

    /**
     * 访问公众号登录
     * @return void
     */
    public function Index(){
        return 'Hello!SAPI++ Wechat';
    }
}