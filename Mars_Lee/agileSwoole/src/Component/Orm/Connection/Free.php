<?php
/**
 * Created by Wenlong Li
 * User: wenlong11
 * Date: 2018/9/21
 * Time: 下午12:22
 */

namespace Component\Orm\Connection;


use Kernel\AgileCore;

trait Free
{
    protected $hash = '';
    public function free()
    {
        AgileCore::getInstance()->get('pool')->free($this);
    }
}