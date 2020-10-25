<?php

namespace WxSDK\core\model\menu;

class Button{
	public $type;
	public $name;
	public $media_id;
	public $sub_button;
	//菜单KEY值，用于消息接口推送，不超过128字节
	public $key;
	//网页 链接，用户点击菜单可打开链接，不超过1024字节。 type为miniprogram时，不支持小程序的老版本客户端将打开本url。
	public $url;
	//小程序的appid（仅认证公众号可配置）
	public $appid;
	//小程序的页面路径
	public $pagepath;
}
