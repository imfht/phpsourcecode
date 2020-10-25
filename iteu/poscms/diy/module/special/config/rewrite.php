<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['(\w+)']                                 = 'category/index/dir/$1'; // 对应规则：{dirname}/
$route['(\w+)\/(\d+).html']                     = 'category/index/dir/$1/page/$2'; // 对应规则：{dirname}/{page}.html
$route['(\w+)\/(.+)(.+)\/(\d+).html']           = 'show/index/id/$4'; // 对应规则：{dirname}/{y}{m}/{id}.html
$route['(\w+)\/(.+)(.+)\/(\d+)-(\d+).html']     = 'show/index/id/$4/page/$5'; // 对应规则：{dirname}/{y}{m}/{id}-{page}.html


/* 以下是规则备注 */

$note['(\w+)']                                 = "{dirname}/";
$note['(\w+)\/(\d+).html']                     = "{dirname}/{page}.html";
$note['(\w+)\/(.+)(.+)\/(\d+).html']           = "{dirname}/{y}{m}/{id}.html";
$note['(\w+)\/(.+)(.+)\/(\d+)-(\d+).html']     = "{dirname}/{y}{m}/{id}-{page}.html";
