<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 微信模型
 */
class WechatPay extends \think\Model {

	public static $fieldlist = [
		['name' => 'app_id', 'title' => '微信APPID', 'type' => 'text', 'is_must' => 1, 'help' => 'AppID'],
		['name' => 'mch_id', 'title' => '商户ID', 'type' => 'text', 'is_must' => 1, 'help' => '商户ID'],
		['name' => 'key', 'title' => 'API秘钥', 'type' => 'text', 'is_must' => 1, 'help' => 'API秘钥'],
		['name' => 'cert_path_id', 'title' => '证书', 'type' => 'attach', 'help' => '如需使用敏感接口（如退款、发送红包等）需要配置 API 证书路径(登录商户平台下载 API 证书)'],
		['name' => 'key_path_id', 'title' => '证书秘钥', 'type' => 'attach', 'help' => ''],
		['name' => 'notify_url', 'title' => '回调地址', 'type' => 'text', 'help' => '你也可以在下单时单独设置来想覆盖它'],
	];
}