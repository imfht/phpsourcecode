<?php
namespace app\news\validate;

use think\Validate;

class Index extends Validate {
	//自定义规则
	protected $rule = [ 
			'title|标题' => 'require|max:128',
			'author|作者' => 'max:16',
			'url' => 'url',
			'title_img' => 'require',
			'send_time|发布时间' => 'require|checkSendTime',
			'outside_url|外链' => 'requireIf:is_open_outside,1|url',
			'content|正文' => 'requireIf:is_open_outside,0' 
	];
	
	//自定义错误信息
	protected $message = [ 
			'url.url' => '链接无效，请检查',
			'title_img.require'=>'请选择封面图片',
			'outside_url.requireIf' => '外链开启时不能为空',
			'outside_url.url' => '外链无效，请检查',
			'content.requireIf' => '正文必填（除非开启外链）' 
	];
	public function __construct() {
		// 开启批量验证
		$this->batch ();
	}
	/**
	 * checkSendTime
	 * 
	 * @param mixed $value 输入的数据
	 * @param mixed $rule 规则
	 * @param mixed $data 输入的完整数据
	 * @return boolean TRUE|FALSE
	 */
	protected function checkSendTime($value, $rule, $data) {
		$res = strtotime ( $value );
		if ($res) {
			return true;
		} else {
			return false;
		}
	}
}