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

	if (!is_array($config)) {
		$file = WEBPATH.'config/sms.php';
		if (!is_file($file)) {
			return array('status' => 0, 'msg' => 'sms.php配置文件不存在');
		}
		$config = require_once $file;
	}

	$app = dr_string2array($config['third']);
	if (!$app['value']) {
		return array('status' => 0, 'msg' => '此应用没有配置规则');
	}

	$tpl = '';
	$param = array();

	// 判断验证码类型
	foreach ($app['value'] as $t) {
		if (preg_match_all('/'.$t['code'].'/iU', $content, $preg)) {
			$tpl = $t['tpl'];
			$p = explode(',', $t['value']);
			foreach($p as $i => $v) {
				$n = str_replace(array('${', '}'), '', $v);
				$param[$n] = $preg[$i+1][0];
			}
			break;
		}
	}

	if (!$tpl) {
		return array('status' => 0, 'msg' => '未知模板');
	}

	include_once FCPATH.'app/usualidayu/core/TopSdk.php';
	$c = new TopClient;
	$c->appkey = $app['key'];
	$c->secretKey = $app['secret'];
	$req = new AlibabaAliqinFcSmsNumSendRequest;
	$req->setSmsType("normal");
	$req->setSmsParam(json_encode($param));
	$req->setSmsFreeSignName($config['note']);
	$req->setRecNum($phone);
	$req->setSmsTemplateCode($tpl);
	$resp = dr_object2array($c->execute($req));

	if (isset($resp['result'])) {
		return array('status' => 1, 'msg' => '发送成功');
	} else {
		return array('status' => 0, 'msg' => $resp['sub_msg']);
	}


}