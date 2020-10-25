<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 11:38
 */

namespace naples\lib;


use naples\lib\base\Service;

/** 加载助手函数 */
class Help extends Service
{
    public function init()
    {
        require $this->config('pathHelp');
        require $this->config('pathCustom');
    }
}