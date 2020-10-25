<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// 当生成伪静态时此文件会被系统覆盖；如果发生页面指向错误，可以调整下面的规则顺序；越靠前的规则优先级越高。

$route['show-(\d+)-(\d+)-(\d+).html']           = 'show/index/id/$2/page/$3'; // 【内容页】 对应规则：show-{fid}-{id}-{page}.html
$route['show-(\d+)-(\d+).html']                 = 'show/index/id/$2'; // 【内容页】 对应规则：show-{fid}-{id}.html
$route['extend-(\d+)-(\d+).html']               = 'extend/index/id/$2'; // 【扩展页】 对应规则：extend-{fid}-{id}.html
$route['list-(\d+)-(\d+){-(\d+).html']          = 'category/index/id/$2'; // 【栏目页】 对应规则：list-{fid}-{id}{-{page}.html
$route['list-(\d+)-(\d+).html']                 = 'category/index/id/$2'; // 【栏目页】 对应规则：list-{fid}-{id}.html
