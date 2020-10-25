<?php
/**
 * Project      CuteLib
 * Author       Ryan Liu <azhai@126.com>
 * Copyright (c) 2013 MIT License
 */

namespace Cute\Web;

use \Cute\Web\Site;
use \Cute\ORM\Mapper;


/**
 * WEB控制器
 */
class Handler
{
    protected $app = null;
    protected $method = null;

    public function __construct(Site& $app, $method = 'get')
    {
        $this->app = $app;
        $this->method = $method;
        $this->setup();
    }

    public function setup()
    {
    }

    public function __invoke()
    {
        if (!method_exists($this, $this->method)) {
            return $this->app->abort(403);
        }
        $args = func_get_args();
        return exec_method_array($this, $this->method, $args);
    }

    public function except()
    {
        echo 'ERROR';
    }
}
