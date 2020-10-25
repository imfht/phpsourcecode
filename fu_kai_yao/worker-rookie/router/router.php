<?php
use workerbase\classs\Router;

//只匹配一次
Router::haltOnMatch(true);
//路由前缀
Router::setPrefix('\/?');

/**********************************自定义路由start****************************************/
Router::get('test/', 'test\TestController@test');
Router::any('test2/', 'test\TestController@test2');
//any传参
Router::any('test3:any', 'test\TestController@test3');




/**********************************自定义路由end****************************************/


//pathinfo自动路由
Router::any(':all', 'pathinfo\AutoPathController@runController');
Router::error(function(){
  echo '404:: ' . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) . ' Not Found！';
});
Router::dispatch();
