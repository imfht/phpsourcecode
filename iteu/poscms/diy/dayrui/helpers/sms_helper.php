<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 请把此文件复制到 dayrui/dayrui/helpers/ 下面
 */

/**
 * 第三方短信发送接口
 *
 * @param	string	$phone		发送对象，多个手机号码以,分开
 * @param	string	$content	发送内容，限制在40个字以内
 * @param	string	$user   	用户信息（开发者自行规定）
 * @return	array	返回格式为：array('status' => 1/0, 'msg' => '成功/失败')
 */

function my_sms_send($phone, $content, $config = array()) {




}

