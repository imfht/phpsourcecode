<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。




$route['(.+)\/(\d+).html']                      = 'page/index/dir/$1/page/$2'; // 【单网页】 对应规则：{pdirname}/{page}.html
$route['(.+)']                                  = 'page/index/dir/$1'; // 【单网页】 对应规则：{pdirname}/
