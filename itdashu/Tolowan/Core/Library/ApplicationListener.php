<?php
namespace Core\Library;

use Core\Models\Route;
use Core\Plugin;

class ApplicationListener extends Plugin
{
    public function beforeRouter($event, $application)
    {
        //判断是否有自定义路径
        if ($application->uri == null) {
            $uri = $this->router->getRewriteUri();
        } else {
            $uri = $application->uri;
        }
        $route = Route::findFirstByRewrite_uri($uri);
        if ($route) {
            $application->uri = $route->uri;
        }
        //$application->uri = '/2kdjj/index';
    }
}
