<?php
namespace app\mass\validate;

use think\Validate;

class Index extends Validate {
	protected $rule = [ 
			'text' => 'requireif:msg_type,text',
			'img' => 'requireif:msg_type,img',
			'voice' => 'requireif:msg_type,voice',
			'video|视频' => 'requireif:msg_type,video',
			'news' => 'checkNewsType|checkNewsContent' 
	] // requireif:msg_type,news
;
	protected $message = [ 
			'text' => '文本回复时，文本回复不能为空',
			'news.checkNewsType' => '图文数据类型出错',
			'news.checkNewsContent' => '图文数据不存在' 
	];
	public function __construct() {
		// 开启批量验证
		$this->batch ();
	}
	public function checkNewsType($value, $rule, $data) {
		if ($data ['msg_type'] == 'news') {
			if (is_array ( $value )) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	public function checkNewsContent($value, $rule, $data) {
		if ($data ['msg_type'] == 'news') {
			$r = array_filter ( $value );
			$r = array_values ( $r );
			if (empty ( $r )) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}
}