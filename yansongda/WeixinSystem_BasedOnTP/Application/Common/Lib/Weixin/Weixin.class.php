<?php
namespace Common\Lib\Weixin;
use Common\Lib\Weixin\Api;
use Common\Lib\Weixin\AccessLog;
/**
* 	Thinkphp 微信公共类
* 	--------------------------
* 	@author JasonYan <yansong.da@qq.com>
* 	--------------------------
* 	lastmodified : 2014-10-10
*/
class Weixin extends Api
{
	var $data = array ();
	var $appid;
	var $appsecret;

	/**
	 * 构建函数
	 * 主要用途：获取从微信服务器发出的原始信息，并验证其有效性。
	 * 			 返回信息中的各部分内容
	 * @param [type] $appid     [微信appid]
	 * @param [type] $appsecret [微信appsecret]
	 */
	public function __construct($appid, $appsecret)
	{
		AccessLog::write();
		$this->appid = $appid;
		$this->appsecret = $appsecret;
		$postStr = file_get_contents('php://input');
        if (!empty($postStr)){
        	if ( !$this->checkSignature() ) {
				die('黑客行为是禁止的哦~');
			}
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            foreach ($postObj as $key => $value) {
            	$this->data [$key] = strval ( $value );
            }
        } else {
        	if ( $_GET['signature'] != '') {
        		echo $this->varify();
        		exit;
        	}
        	echo "Thanks for your visit !This msg is from a Thinkphp_Weixin Class~";
        	exit;
        }
	}

	/**
	 * 获取用户事件类型
	 * @return [type] [事件类型和与之相对应的信息]
	 */
	public function getTypedata()
	{
		switch ( $this->data['MsgType'] ) {
			case 'text':
				return array(
					'type' => 'text',
					'Content' => $this->data['Content'],
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'image':
				return array(
					'type' => 'image',
					'PicUrl' => $this->data['PicUrl'], //图片URL
					'MediaId' => $this->data['MediaId'],//多媒体ID
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'voice':
				return array(
					'type' => 'voice',
					'MediaId' => $this->data['MediaId'],//多媒体ID
					'Format' => $this->data['Format'],
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'video':
				return array(
					'type' => 'video',
					'MediaId' => $this->data['MediaId'],//多媒体ID
					'ThumbMediaId' => $this->data['ThumbMediaId'],
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'location':
				return array(
					'type' => 'location',
					'Location_X' => $this->data['Location_X'],
					'Location_Y' => $this->data['Location_Y'],
					'Scale' => $this->data['Scale'],//缩放大小
					'Label' => $this->data['Label'],//地理位置信息
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'link':
				return array(
					'type' => 'link',
					'Title' => $this->data['Title'],//
					'Description' => $this->data['Description'],
					'Url' => $this->data['Url'],
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;
			case 'event':
				return array(
					'type' => 'event',
					'Event' => $this->data,//
					'appid' => $this->appid,
					'appsecret' => $this->appsecret,
				);
				break;

			default:
				return array(
					'type' => 'unknown',
					'Description' => '消息类型未知',
				);
				break;
		}

	}

	/**
	 * 将处理过的数据返回给微信服务器
	 * @param  [array] $response [处理过的数据]
	 * @return [type]           [description]
	 */
	public function toWeixin($response)
	{
		switch ( $response['MsgType'] ) {
		 	case 'text':
		 		$reply = "
					<xml>
					<ToUserName><![CDATA[".$this->data['FromUserName']."]]></ToUserName>
					<FromUserName><![CDATA[".$this->data['ToUserName']."]]></FromUserName>
					<CreateTime>".time()."</CreateTime>
					<MsgType><![CDATA[text]]></MsgType>
					<Content><![CDATA[".$response['Content']."]]></Content>
					</xml>
				";
		 		break;

		 	case 'news':
		 		$c = count($response['Content']);
        		$reply = "<xml>
                    <ToUserName><![CDATA[".$this->data['FromUserName']."]]></ToUserName>
                    <FromUserName><![CDATA[".$this->data['ToUserName']."]]></FromUserName>
                    <CreateTime>".time()."</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>          
        			<ArticleCount>".$c."</ArticleCount>
                    <Articles>";
                foreach($response['Content'] as $v){
                $reply .= "<item>
                        <Title><![CDATA[".$v['0']."]]></Title> 
                        <Description><![CDATA[".$v['1']."]]></Description>
                        <PicUrl><![CDATA[".$v['2']."]]></PicUrl>
                        <Url><![CDATA[".$v['3']."]]></Url>
                        </item>";
                }
        		$reply .= "</Articles></xml>"; 
		 		break;
		 	
		 	default:
		 		# code...
		 		break;
		} 
		return $reply;
	}

	/**
	 * 创建菜单
	 * @return [type] [description]
	 */
	public function creatMenu($menu)
	{
		$jsonmenu = urldecode(json_encode($menu));
		if ( S('jsonmenu') && S('jsonmenu') == $jsonmenu ) {
			return true;
		} else {
			S('jsonmenu', $jsonmenu);
			$url = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->getToken();
			$ch = curl_init();
	        curl_setopt($ch,CURLOPT_URL,$url);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
			curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
	        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	        curl_setopt($ch,CURLOPT_POST,1);
	        curl_setopt($ch,CURLOPT_POSTFIELDS,$jsonmenu);
	        $resultj = curl_exec($ch);
	        curl_close($ch);
	        $result = json_decode($resultj, true);
	        if ( $result['errcode'] != '0' ) {
	        	$msg = "创建菜单错误\n\r\n错误代码：".$result['errcode']."\n\r错误信息：".$result['errmsg'];
				$this->err($msg);
	        } else {
	        	return true;
	        }
		}
	}

	/**
	 * 设置值
	 * @param [type] $key   [description]
	 * @param [type] $value [description]
	 * @param [type] $time  [description]
	 */
	public function setValue($key, $value, $time = NULL)
	{
		if ( $time != NULL ) {
			S($this->data['ToUserName'].$this->data['FromUserName'].'_'.$key, $value, $time);
		} else {
			S($this->data['ToUserName'].$this->data['FromUserName'].'_'.$key, $value);
		}
		
	}

	/**
	 * 获取缓存值
	 * @param  [type] $key [description]
	 * @return [type]      [description]
	 */
	public function getValue($key)
	{
		if ( S($this->data['ToUserName'].$this->data['FromUserName'].'_'.$key) ) {
			return S($this->data['ToUserName'].$this->data['FromUserName'].'_'.$key);
		} else {
			return false;
		}
	}

	/**
	 * 获取用户信息
	 * 
     *"subscribe": 1, 
     *"openid": "o6_bmjrPTlm6_2sgVt7hMZOPfL2M", 
     *"nickname": "Band", 
     *"sex": 1, 
     *"language": "zh_CN", 
     *"city": "广州", 
     *"province": "广东", 
     *"country": "中国", 
     *"headimgurl":    "http://wx.qlogo.cn/mmopen/g3MonUZtNHkdmzicIlibx6iaFqAc56vxLSUfpb6n5WKSYVY0ChQKkiaJSgQ1dZuTOgvLLrhJbERQQ4eMsv84eavHiaiceqxibJxCfHe/0", 
     *"subscribe_time": 1382694957,
     *"unionid": " o6_bmasdasdsad6_2sgVt7hMZOPfL"
     *
	 * @param  [type] $openid [用户的OPENID。如果为空则默认取当前发送消息用户]
	 * @return [type]         [description]
	 */
	public function getUserinfo($openid = NULL)
	{
		if ( !isset($openid) ) {
			$openid = $this->data['FromUserName'];
		}
		$url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->getToken()."&openid=".$openid."&lang=zh_CN";
		$userInfo_json = file_get_contents($url);
		return json_decode($userInfo_json, true);
	}

	/**
	 * 获取微信access_token
	 * @return [type] [description]
	 */
	private function getToken()
	{
		if ( S($this->data['ToUserName'].'access_token') ) {
			return S($this->data['ToUserName'].'access_token');
		} else {
			$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret;
			$tokenjson = file_get_contents($url);
			$dtoken = json_decode($tokenjson, true);

			if ( $dtoken['access_token'] != '') {
				S($this->data['ToUserName'].'access_token', $dtoken['access_token'], $dtoken['expires_in']);
				return $dtoken['access_token'];
				exit;
			} else {
				$msg = "获取access_token值错误：\n\r\n错误代码：".$dtoken['errcode']."\n\r错误信息：".$dtoken['errmsg'];
				$this->err($msg);
			}
		}
		
	}

	/**
	 * 首次接入微信时的验证
	 * @return [type] [description]
	 */
	private function varify()
	{
		if( $this->checkSignature() ){
        	return $_GET["echostr"];
        }
	}

	/**
	 * 验证信息的真实性（是否从微信服务器发出）
	 * @return [type] [description]
	 */
	private function checkSignature()
	{
        if (!C('TOKEN')) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        		
		$token = C('TOKEN');
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

	private function saveAccess()
	{
		$log = new \Common\Lib\Weixin\AccessLog();
		$log->write();
	}

	/**
	 * 错误输出
	 * @param  [type] $msg 错误信息
	 * @return [type]      [description]
	 */
	private function err($msg)
	{
		$response['MsgType'] = 'text';
		$response['Content'] = $msg;
		echo $this->toWeixin($response);
		exit;
	}
}