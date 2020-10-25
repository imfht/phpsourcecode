<?php
/**
 * 文件：main.php
 * 作者：mqycn
 * 博客：http://www.miaoqiyuan.cn
 * 源码：http://gitee.com/mqycn/diy-qrcode/
 * 说明：主程序
 */

require_once 'src/diyqrcode.php';

// 选择的模板
$skin = isset($_GET['skin']) ? $_GET['skin'] : '';

// 输入的字符串
$key = isset($_GET['key']) ? $_GET['key'] : '';
$key = base64_decode($key);

$config_file = "./qrcode.{$skin}/config.php";

// 判断配置文件是存在
if (!is_file($config_file)) {
	die('Skin Error!');
}

// 读取配置信息
$qrcode_config = require $config_file;

if (!is_array($qrcode_config)) {
	throw new Exception("配置文件错误({$config_file})", 1);
}

if (!isset($qrcode_config['skin'])) {
	$qrcode_config['skin'] = $skin;
}

// 生成二维码
$qrcode = new DiyQrcode($qrcode_config);

$qrcode->setKey($key); //需要显示的二维码

if (!isset($_GET['response_type']) || $_GET['response_type'] != 'json') {

	// 调用方法一：直接输出
	$qrcode->output();

} else {

	// 调用方法二：保存为文件
	$image = $qrcode->save('./qrimg/' . md5($key . $skin) . '.png');

	header('content-type: text/javascript');
	echo json_encode($image);
}

unset($qrcode);
?>