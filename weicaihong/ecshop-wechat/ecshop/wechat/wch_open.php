<?php
/**
 * wch_open.php UTF8
 * 二次开发文件,或者是未知动作
 * User: weicaihong
 * Date: 15/4/16 15:45
 * Copyright: http://www.weicaihong.com
 */

// 表前缀 $prefix
$tb_users = $prefix.'users';

$data = array(
    'msg'=>$act.'没有相对应的接口',
    'post'=>json_encode($post_data)
);
// 输出json
require_once('wch_json.php');