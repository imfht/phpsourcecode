<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 图片文件路径
$config['image_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'image' . DIRECTORY_SEPARATOR;
// 临时文件路径
$config['temp_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
// 缩略图配置
$config['thumb_save'] = true; // 是否保存缩略图
$config['thumb_folder'] = 'thumb'; // 缩略图保存文件夹
$config['thumb_factor'] = array(360, 720, 1080); // 保存的标准尺寸
$config['thumb_default'] = 720; // 默认保存的尺寸

$config['cache_time'] = 24 * 60; // 缓存时间 单位：分钟

