<?php

namespace WxSDK\core\model\msg;

class SendPicsInfoItem {
	public $picMd5Sum;//图片的MD5值，开发者若需要，可用于验证接收到图片
	function __construct(string $picMd5Sum){
		$this->picMd5Sum = $picMd5Sum;
	}
}

