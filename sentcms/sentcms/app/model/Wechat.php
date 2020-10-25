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
class Wechat extends \think\Model {

	public static $fieldlist = [
		['name' => 'title', 'title' => '名称', 'type' => 'text', 'is_must' => 1, 'help' => '微信名称'],
		['name' => 'type', 'title' => '微信类型', 'type' => 'select', 'is_must' => 1, 'option' => [['key' => 1, 'label' => '公众号'],['key' => 2, 'label' => '小程序'],['key' => 3, 'label' => '企业号']], 'help' => 'AppID'],
		['name' => 'app_id', 'title' => '微信APPID', 'type' => 'text', 'is_must' => 1, 'help' => 'AppID'],
		['name' => 'secret', 'title' => '微信秘钥', 'type' => 'text', 'help' => 'AppSecret'],
		['name' => 'token', 'title' => '微信Token', 'type' => 'text', 'help' => 'Token'],
		['name' => 'aes_key', 'title' => 'EncodingAESKey', 'type' => 'text', 'help' => 'EncodingAESKey，兼容与安全模式下请一定要填写！！！'],
	];

	protected function getTypeTextAttr($value, $data){
		$type = self::$fieldlist[1]['option'];
		$type_text = "";
		foreach($type as $val){
			if($data['type'] && $data['type'] == $val['key']){
				$type_text = $val['label'];
			}
		}
		return $type_text;
	}
}