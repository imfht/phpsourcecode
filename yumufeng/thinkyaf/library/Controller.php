<?php

/**
 * 基于thinkphp 5.1版本的封装抽取
 * Date: 2018\2\19 0019 15:42
 *
 */
class Controller extends \Yaf_Controller_Abstract
{
    /**
     * @var Request Request 实例
     */
    protected $request;

    protected function init()
    {
        $this->request = Request::instance();
    }
}