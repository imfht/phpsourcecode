<?php
namespace app\common\service;

class Tool{
	public static function transURL($list, $aid){
		$common = new \app\common\controller\Index ();
		$config = $common->getConfig();
		$URL = $config ['wx_url'] ['get_media_file'];
		$res = $common->getAccessToken($aid);
		if(0==$res["errCode"]){
			$access_token = $res["data"];
		}else{
			return $res;
		}
		$_url = str_replace('ACCESS_TOKEN',$access_token, $URL);
		foreach ($list as $k => $v){
			/*
			 * 语音voice
			 */
			if("voice" ==$v["msg_type"]){
				$v["voice_url"]= str_replace('MEDIA_ID',$v["media_id"], $_url);
			}else if("video"==$v["msg_type"]){
				$url = str_replace('MEDIA_ID',$v["media_id"], $_url);
				$thumb_url = str_replace('MEDIA_ID',$v["thumb_media_id"],$_url);
				$v["video_url"]=$url;
				$v["thumb_media_url"] = $thumb_url;
			}else if("img"==$v["msg_type"]){
				/*
				 * 微信的图片只能在微信手机端浏览，故，此处对链接进行转换
				 * ，并命名为image_url，保留img_url。方便扩展。
				 * 未来如果开发微信管理段，可直接在前端引用img_url。
				 */
				$v["image_url"]= str_replace('MEDIA_ID',$v["media_id"], $_url);
			}
			/*
			 * 视频
			 */
			
			$list[$k]=$v;
		}
		$res["data"] = $list;
		return $res;
	}
}