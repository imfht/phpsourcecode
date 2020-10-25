<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 12:44
 */

use HServer\core\http\HServerFilter;

class MyFilter extends HServerFilter
{

    protected $level = 1;

    public function auth()
    {
        echo "我是拦截器MyFilter，优先级为1,你的IP是：".$this->Request->getIp()."\n";
    }

}