<?php

namespace WxSDK\core\module;

use WxSDK\core\common\IApp;
use WxSDK\core\model\menu\Button;
use WxSDK\core\model\menu\Menu;
use WxSDK\core\utils\Tool;
use WxSDK\resource\Config;

class MenuKit {
	public static function sendMenu(IApp $App, Menu $menu){
		$ret = $App->getAccessToken();
		if($ret->ok()){
			$url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$create_menu);
// 			$json = json_encode($menu,JSON_UNESCAPED_UNICODE);
			return Tool::doCurl($url,$menu);
		}else{
			return $ret;
		}
	}
	
	public static function sendConditionMenu(IApp $App, Menu $menu){
	    $ret = $App->getAccessToken();
	    if($ret->ok()){
	        $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$create_condition_menu);
// 	        $json = json_encode($menu,JSON_UNESCAPED_UNICODE);
	        return Tool::doCurl($url,$menu);
	    }else{
	        return $ret;
	    }
	}
	
	public static function deleteConditionMenu(IApp $App, string $menuid){
	    $ret = $App->getAccessToken();
	    if($ret->ok()){
	        $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$delete_condition_menu);
	        $data = ["menuid"=>$menuid];
// 	        $json = json_encode($data);
	        return Tool::doCurl($url,$data);
	    }else{
	        return $ret;
	    }
	}
	/**
	 * 
	 * @param IApp $App
	 * @return \WxSDK\core\common\Ret
	 */
	public static function deleteMenu(IApp $App){
	    $ret = $App->getAccessToken();
	    if($ret->ok()){
	        $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$delete_menu);
	        return Tool::doCurl($url);
	    }else{
	        return $ret;
	    }
	}
	/**
	 * 
	 * @param IApp $App
	 * @param string $userId
	 * @return \WxSDK\core\common\Ret
	 */
	public static function tryMatchConditionMenu(IApp $App, string $userId){
	    $ret = $App->getAccessToken();
	    if($ret->ok()){
	        $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$trymatch_condition_menu);
	        $data = [];
	        $data["user_id"] = $userId;
// 	        $json = json_encode($data,JSON_UNESCAPED_UNICODE);
	        return Tool::doCurl($url,$data);
	    }else{
	        return $ret;
	    }
	}
	/**
	 * 
	 * @param IApp $App
	 * @return \WxSDK\core\common\Ret
	 */
	public static function getMenu(IApp $App){
	    $ret = $App->getAccessToken();
	    if($ret->ok()){
	        $url = str_replace("ACCESS_TOKEN",$ret->getData(),Config::$get_menu);
	        return Tool::doCurl($url);
	    }else{
	        return $ret;
	    }
	}
	
	public static function createParentButton(string $name,Button... $butons){
		$button = new Button();
		$button->name = $name;
		$button->sub_button = $butons;
		return $button;
	}
	public static function createButtonClick(string $name, string $key){
		$button = new Button();
		$button->type = "click";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	public static function createButtonView(string $name, string $url){
		$button = new Button();
		$button->type = "view";
		$button->name = $name;
		$button->url = $url;
		return $button;
	}
	/**
	 * 扫码推事件用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后显示扫描结果（如果是URL，将进入URL），且会将扫码的结果传给开发者，开发者可以下发消息。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonScancodePush(string $name, string $key){
		$button = new Button();
		$button->type = "scancode_push";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 扫码推事件且弹出“消息接收中”提示框用户点击按钮后，微信客户端将调起扫一扫工具，完成扫码操作后，将扫码的结果传给开发者，同时收起扫一扫工具，然后弹出“消息接收中”提示框，随后可能会收到开发者下发的消息。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonScancodeWaitmsg(string $name, string $key){
		$button = new Button();
		$button->type = "scancode_waitmsg";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 弹出系统拍照发图用户点击按钮后，微信客户端将调起系统相机，完成拍照操作后，会将拍摄的相片发送给开发者，并推送事件给开发者，同时收起系统相机，随后可能会收到开发者下发的消息。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonPicSysphoto(string $name, string $key){
		$button = new Button();
		$button->type = "pic_sysphoto";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 弹出拍照或者相册发图用户点击按钮后，微信客户端将弹出选择器供用户选择“拍照”或者“从手机相册选择”。用户选择后即走其他两种流程。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonPicPhotoOrAlbum(string $name, string $key){
		$button = new Button();
		$button->type = "pic_photo_or_album";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 弹出微信相册发图器用户点击按钮后，微信客户端将调起微信相册，完成选择操作后，将选择的相片发送给开发者的服务器，并推送事件给开发者，同时收起相册，随后可能会收到开发者下发的消息。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonPicWeixin(string $name, string $key){
		$button = new Button();
		$button->type = "pic_weixin";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 弹出地理位置选择器用户点击按钮后，微信客户端将调起地理位置选择工具，完成选择操作后，将选择的地理位置发送给开发者的服务器，同时收起位置选择工具，随后可能会收到开发者下发的消息。
	 * @param string $name
	 * @param string $key
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonLocationSelect(string $name, string $key){
		$button = new Button();
		$button->type = "location_select";
		$button->name = $name;
		$button->key = $key;
		return $button;
	}
	/**
	 * 下发消息（除文本消息）用户点击media_id类型按钮后，微信服务器会将开发者填写的永久素材id对应的素材下发给用户，永久素材类型可以是图片、音频、视频、图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。
	 * @param string $name
	 * @param string $mediaId
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonMediaId(string $name, string $mediaId){
		$button = new Button();
		$button->type = "media_id";
		$button->name = $name;
		$button->media_id = $mediaId;
		return $button;
	}
	/**
	 * 跳转图文消息URL用户点击view_limited类型按钮后，微信客户端将打开开发者在按钮中填写的永久素材id对应的图文消息URL，永久素材类型只支持图文消息。请注意：永久素材id必须是在“素材管理/新增永久素材”接口上传后获得的合法id。
	 * @param string $name
	 * @param string $mediaId
	 * @return \WxSDK\core\model\menu\Button
	 */
	public static function createButtonViewLimited(string $name, string $mediaId){
		$button = new Button();
		$button->type = "view_limited";
		$button->name = $name;
		$button->media_id = $mediaId;
		return $button;
	}
}

