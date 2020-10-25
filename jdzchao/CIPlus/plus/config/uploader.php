<?php defined('BASEPATH') OR exit('No direct script access allowed');
// 上传文件路径
$config['file_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR;
// 临时文件路径
$config['temp_save_path'] = dirname(APPPATH) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;
// 按用途创建文件夹
$config['create_usage_folder'] = TRUE;

// 上传类型：头像
$config['usages']['avatar'] = [
    'approved_ext' => ['jpg', 'png', 'gif', 'jpeg'],
    'max_file_size' => 1 * 1024 * 1024,
    'max_file_mun' => 1,
    'fetch_hash' => TRUE,
    'rename' => 'hash',
];
// 上传类型：图像
$config['usages']['image'] = [
    'approved_ext' => ['jpg', 'png', 'gif', 'bmp', 'jpeg'],
    'max_file_size' => 10 * 1024 * 1024,
    'max_file_mun' => 1,
    'fetch_hash' => TRUE,
    'rename' => 'hash',
];
// 上传类型：附件
$config['usages']['attach'] = [
    'approved_ext' => ['doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx'],
    'max_file_size' => 10 * 1024 * 1024,
    'max_file_mun' => 1,
    'fetch_hash' => TRUE,
    'rename' => 'hash',
];
// 上传类型：三维模型
$config['usages']['model'] = [
    'approved_ext' => ['obj'],
    'max_file_size' => 10 * 1024 * 1024,
    'max_file_mun' => 1,
    'fetch_hash' => TRUE,
    'rename' => 'hash',
];
