<?php
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 微信通信类
*/

defined('INPOP') or exit('Access Denied');

class weixinPlugin{

	public $token;
	public $appid;
	public $appsecret;

	public function __construct($token = '', $appid = '', $appsecret = ''){
		if($token) $this->token = $token;
		if($appid) $this->appid = $appid;
		if($appsecret) $this->appsecret = $appsecret;
	}
    
    //验证
	public function valid($echoStr, $signature, $timestamp, $nonce, $weixintoken){
        if($this->checkSignature($signature, $timestamp, $nonce, $weixintoken)){
        	echo $echoStr;
        	exit;
        }
    }
	public function sendTemplateMsg($toUsername='', $template_id='' ,$first='',$remark='',$performance=''){
		$tokenArray = $this->getToken();
		if($tokenArray['access_token']){
 			$postArray=array();
 			$postArray['touser']=$toUsername;
 			$postArray['template_id']=$template_id;

 			$postArray['data']['first']['value']=$first;
 			$postArray['data']['performance']['value']=$performance;
 			$postArray['data']['remark']['value']=$remark;

 			$postArray['data']['time']['value']=date('Y-m-d H:i:s');
 			
 			$json_template = json_encode($postArray);
 			//print_r($postArray);
			$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$tokenArray['access_token'];
			$returnString='';
			$returnString = $this->doCurl($url, true, urldecode($json_template));
			return $returnString;
		}	
	}
	//发送文本消息
	
	public function sendTextMsg($fromUsername, $toUsername, $contentStr){
		$time = time();
		$Tpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[text]]></MsgType>
				<Content><![CDATA[%s]]></Content>
				</xml>";
		$resultStr = sprintf($Tpl, $fromUsername, $toUsername, $time, $contentStr);
		return $resultStr;	
	}
	
	//发送图文消息
	public function sendNewsMsg($fromUsername, $toUsername, $contentArray){
		$time = time();
		$ArticleCount = count($contentArray);
		$ArticlesItemString = "";
		$itemTpl = "
				<item>
				<Title><![CDATA[%s]]></Title>
				<Description><![CDATA[%s]]></Description>
				<PicUrl><![CDATA[%s]]></PicUrl>
				<Url><![CDATA[%s]]></Url>
				</item>
				";
		foreach($contentArray as $content){
			$ArticlesItemString .= sprintf($itemTpl, $content['Title'], $content['Description'], $content['PicUrl'],  $content['Url']);
		}
		$Tpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[news]]></MsgType>
				<ArticleCount>%s</ArticleCount>
				<Articles>%s</Articles>
				<FuncFlag>1</FuncFlag>
				</xml>";
		$resultStr = sprintf($Tpl, $fromUsername, $toUsername, $time, $ArticleCount, $ArticlesItemString);
		return $resultStr;		
	}
	
	//生成菜单
	public function createMenu($postArray = array()){
		if(empty($postArray)) return false;
		$postString = urldecode(json_encode($postArray));
		$tokenArray = $this->getToken();
		if($tokenArray['access_token']){
			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$tokenArray['access_token'];
			$returnString = $this->doCurl($url, true, $postString);
			return $returnString;
		}
	}
	
	//上传多媒体
	public function uploadMedia($postArray = array(), $mediatype = 'thumb'){
		if(empty($postArray)) return false;
		$tokenArray = $this->getToken();
		if($tokenArray['access_token']){
			$url = "https://api.weixin.qq.com/cgi-bin/media/upload?access_token=".$tokenArray['access_token']."&type=".$mediatype;
			$returnString = $this->doCurl($url, true, $postArray);
			return $returnString;
		}		
	}
	
	//上传素材
	public function uploadNews($postString = ''){
		if(empty($postString)) return false;
		$postString = urldecode(json_encode($postString));
		$tokenArray = $this->getToken();
		if($tokenArray['access_token']){
			$url = "https://api.weixin.qq.com/cgi-bin/media/uploadnews?access_token=".$tokenArray['access_token'];
			$returnString = $this->doCurl($url, true, $postString);
			return $returnString;
		}		
	}
	
	//发送群发
	public function sendAll($media_id = "", $group_id = 0, $msgtype = "mpnews"){
		$postArray = array();
		if($media_id){
			$postArray['filter'] = array("group_id"=>$group_id);
			$postArray[$msgtype] = array("media_id"=>$media_id);
			$postArray['msgtype'] = $msgtype;
			$postString = urldecode(json_encode($postArray));
			$tokenArray = $this->getToken();
			if($tokenArray['access_token']){
				$url = "https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token=".$tokenArray['access_token'];
				$returnString = $this->doCurl($url, true, $postString);
				return $returnString;
			}		
		}
	
	}
	
	//获取二维码ticket
	public function getQrcodeTicket($uid = 1){
		$postArray = array();
		$postArray['action_name'] = "QR_LIMIT_SCENE";
		$postArray['action_info'] = array("scene"=>array("scene_id"=>$uid));
		$postString = urldecode(json_encode($postArray));
		$tokenArray = $this->getToken();
		if($tokenArray['access_token']){
			$url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$tokenArray['access_token'];
			$ticketString = $this->doCurl($url, true, $postString);
			$ticketArray = json_decode($ticketString, true);
			return $ticketArray;
		}		
	}
	
	//获取token
	public function getToken(){
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
		$tokenString = $this->doCurl($url);
		$tokenArray = json_decode($tokenString, true);
		return $tokenArray;
	}
	
	//获取用户信息
	public function getUserInfo($openid = ''){
		if(!$openid) return false;
		$tokenArray = $this->getToken();
		$token = $tokenArray['access_token'];
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$token."&openid=".$openid."&lang=zh_CN";
		$userInfoString = $this->doCurl($url);
		$userInfoArray = json_decode($userInfoString, true);
		return $userInfoArray;
	}
	
	//通过CURL执行通信
	public function doCurl($url, $isPost = false, $postfields = array()){
		if(!$url) return false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		if($isPost){
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
		}
		$return = curl_exec($ch);
		curl_close($ch);
		return $return;
	}
	
    //验签
	private function checkSignature($signature, $timestamp, $nonce, $weixintoken){
		$token = $weixintoken;
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}
}