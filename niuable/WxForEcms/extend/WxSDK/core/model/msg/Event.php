<?php

namespace WxSDK\core\model\msg;

class Event{
	public $event;
	public $eventKey;//事件KEY值，由开发者在创建菜单时设定
	public $ticket;
	public $scanCodeInfo;//扫二维码时发送的信息
	public $latitude;//地理位置纬度
	public $longitude;//地理位置经度
	public $precision;//地理位置精度
	public $menuID;//指菜单ID，如果是个性化菜单，则可以通过这个字段，知道是哪个规则的菜单被点击了。
	public $sendLocationInfo;//发送的位置信息
	function __construct(ScanCodeInfo $scan=null, SendLocationInfo $sendLocationInfo = null) {
		$this->scanCodeInfo = $scan;
		$this->sendLocationInfo = $sendLocationInfo;
	}
}

