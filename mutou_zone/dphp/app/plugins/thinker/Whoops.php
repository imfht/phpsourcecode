<?php

namespace plugins;

class Whoops
{
    public function beforeDispatch()
    {
        $whoops = new \Whoops\Run();
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());
        $whoops->register();
    }
}