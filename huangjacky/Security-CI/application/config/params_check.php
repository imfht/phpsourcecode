<?php
/**
 * 入口统一的参数校验配置文件
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$config['params'] = array(
    'id' => array(
        'check'=>array('Int','gt:0'),
        'filter'=>'Int'
    ), 'title' => array(
        'check' => array('string_length:0,99'),
        'filter' => 'html_escape'
    )
);
/**
 * 当参数校验失败的后执行的操作
 */
$config['param_error_callback'] = function ($pname){
    echo 'param: '.$pname. ' with wrong value';
};