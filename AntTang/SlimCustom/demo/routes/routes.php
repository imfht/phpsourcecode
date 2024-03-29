<?php
/**
 * @package     routes.php
 * @author      Jing <tangjing3321@gmail.com>
 * @link        http://www.slimphp.net
 * @version     1.0
 * @copyright   Copyright (c) SlimCustom.
 * @date        2017年5月2日
 */

namespace Demo\bootstrap;

use \SlimCustom\Libs\App;

// Routes Example
App::get('/[{name}]', function ($request, $response, $args) {
    //Demo log message
    $this->logger->info("Demo-Skeleton '/' route");
    //Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

App::get('/hello/{name}', function ($request, $response, $args) {
    return 'hello world';
});

App::group('/admin', function () {
    $this->get('/index/{name}', \Demo\controllers\Admin\Index::class . ':index');
    //$this->get('/index/{name}', \Demo\controllers\Admin\Index::class . ':index')->add(\Demo\middlewares\Admin\Index::class . ':index');
});