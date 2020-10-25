<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 云片调用类
*/

defined('INPOP') or exit('Access Denied');

class yunpian{

	public $apikey; //密钥

	//初始化
    public function __construct($apikey = ""){
		$this->apikey = $apikey;
    }

	//获得账户
	function get_user($ch,$apikey){
		curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/user/get.json');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('apikey' => $this->apikey)));
		return curl_exec($ch);
	}

	//短信发送
	function send($ch,$data){
		curl_setopt ($ch, CURLOPT_URL, 'https://sms.yunpian.com/v1/sms/send.json');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		return curl_exec($ch);
	}

	//语音发送
	function voice_send($ch,$data){
		curl_setopt ($ch, CURLOPT_URL, 'http://voice.yunpian.com/v1/voice/send.json');
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
		return curl_exec($ch);
	}

	//执行发送短信
	function doSend($sendData = "", $mobile = ""){
		if(!$sendData) return false;
		if(!$mobile) return false;
		$ch = curl_init();
		//设置验证方式
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept:text/plain;charset=utf-8', 'Content-Type:application/x-www-form-urlencoded','charset=utf-8'));
		//设置返回结果为流
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//设置超时时间
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		//设置通信方式
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = array('text'=>$sendData, 'apikey'=>$this->apikey, 'mobile'=>$mobile);
		$json_data = $this->send($ch,$data);
		$return_data = json_decode($json_data, true);
		if($return_data['code'] == 0){
			$return = "success";
		}else{
			$return = "failure".$json_data;
		}
		curl_close($ch);
		return $return;
	}

}
?>