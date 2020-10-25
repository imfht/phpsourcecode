<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 保存json文件的物理路径
$config['save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR;
// 保存json文件的扩展名
$config['save_extension'] = 'json';
$config['security_extension'] = 'php';
