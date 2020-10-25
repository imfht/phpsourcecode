<?php

use Dcat\Admin\Admin;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

Admin::routes();

Route::group([
    'prefix' => config('admin.route.prefix'),
    'namespace' => config('admin.route.namespace'),
    'middleware' => config('admin.route.middleware'),
], function (Router $router) {
    $router->get('/', 'HomeController@index');
    $router->get('/dashboard', 'HomeController@dashboard');
    $router->resource('/servers', 'ServerController');
    $router->resource('/services', 'ServiceController');
    $router->resource('/service_tracks', 'ServiceTrackController');
});
