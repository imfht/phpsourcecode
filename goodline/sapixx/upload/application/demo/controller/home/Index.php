<?php
/**
 * @copyright   Copyright (c) 2017 https://www.sapixx.com All rights reserved.
 * @license     Licensed (http://www.apache.org/licenses/LICENSE-2.0).
 * @author      pillar<ltmn@qq.com>
 * 应用公共访问页面,如PC/H5站点
 */
namespace app\demo\controller\home;
use app\common\controller\Home;

class Index extends Home{

    /**
     * 访问公众号登录
     * @return void
     */
    public function Index(){
        return 'Hello!SAPI++ WebPage';
    }
}