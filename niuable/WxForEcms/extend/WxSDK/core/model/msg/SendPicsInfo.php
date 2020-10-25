<?php

namespace WxSDK\core\model\msg;

class SendPicsInfo{
	public $count;
	public $picList;//图片列表
	function __construct($count=0, SendPicsInfoItem... $item) {
		$this->picList = $item;
	}
}

