<?php

namespace WxSDK\core\model\msg;

class SendLocationInfo{
	public $locationX;//地理位置维度
	public $locationY;//地理位置经度
	public $scale;//精度，可理解为精度或者比例尺、越精细的话 scale越高
	public $label;//地理位置信息
	public $poiname;//朋友圈POI的名字，可能为空
}

