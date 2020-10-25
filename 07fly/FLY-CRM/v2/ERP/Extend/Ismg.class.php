<?php
 
/**
 * Sms 文件处理类
 */
class Ismg
{
	
	//http://dx.ipyy.net/mms.aspx?action=send&userid=&account=账号&password=密码&mobile=15023239810,13527576163&subject=彩信主题&content=内容：短信接口测试&sendTime=&extno=
	//http://dx.ipyy.net/sms.aspx?action=send&userid=&account=账号&password=密码&mobile=15023239810,13527576163&content=内容&sendTime=&extno=
	public $sms_url="http://dx.ipyy.net/smsJson.aspx";	
	
	public function sms_send($acc,$pwd,$mobile,$msg){
		$post_data= array(
						'action'=>'send',
						'userid'=>'',
						'account'=>"$acc",
						'password'=>"$pwd",
						'mobile'=>"$mobile",
						'content'=>"$msg",
						'sendTime'=>'',
						'extno'=>'',
					);
		$rtn=$this->open_curl($this->sms_url,$post_data);
		return $rtn;
	}
	
	
	public function sms_overage($acc,$pwd){
		$post_data= array(
						'action'=>'overage',
						'userid'=>'',
						'account'=>"$acc",
						'password'=>"$pwd",
					);
		$rtn=$this->open_curl($this->sms_url,$post_data);			
		return $rtn;		
		
	}
	
	
	public function open_curl($url,$post_data){
		$ch = curl_init();//打开
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		$response  = curl_exec($ch);
		curl_close($ch);//关闭	
		return $response;
	}
}
?>