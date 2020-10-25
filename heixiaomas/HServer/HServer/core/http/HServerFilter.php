<?php
/**
 * Created by PhpStorm.
 * User: hxm
 * Date: 2019/4/10
 * Time: 12:01
 */

namespace HServer\core\http;

use HServer\core\view\HActionView;

abstract class HServerFilter extends HActionView
{
    protected $level = 1;

    public abstract function auth();
}