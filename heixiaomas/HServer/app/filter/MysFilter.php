<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 12:44
 */

use HServer\core\http\HServerFilter;

class MysFilter extends HServerFilter
{
    protected $level = 2;

    public function auth()
    {
        echo "我是拦截器MysFilter，优先级为2,你请求的URI是：" . $this->Request->getFullUri() . "\n";
    }

}