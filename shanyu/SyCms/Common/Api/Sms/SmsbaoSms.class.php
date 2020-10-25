<?php
use Common\Api\SmsApi;

//短信宝
class SmsbaoSms extends SmsApi{

	private $api_url='http://www.smsbao.com/';

//发送已备案的模板短信
/*
0
*/
	public function send($mobile='',$content=''){
		$content=urlencode($content);
		$sendurl = $this->api_url."sms?u=".$this->username."&p=".md5($this->password)."&m=".$mobile."&c=".$content;
		$result =file_get_contents($sendurl) ;

		if($result === '0' ){
			return true;
		}else{
			$this->error = $this->api_status[$result];
			return false;
		}
	}

//查询剩余短信条数
/*
0
0,2
*/
	public function query(){
		$sendurl = $this->api_url."query?u=".$this->username."&p=".md5($this->password);

		$result =file_get_contents($sendurl) ;
		$result=str_replace("\n", ',', $result);
		$status=explode(',', $result);
		if($status[0] == '0'){
			return $status[2];
		}else{
			$this->error = $this->api_status[$status[0]];
			return false;
		}
	}

	private $api_status = array(
		"0" => "短信发送成功",
		"-1" => "参数不全",
		"-2" => "服务器空间不支持,请确认支持curl或者fsocket，联系您的空间商解决或者更换空间！",
		"30" => "密码错误",
		"40" => "账号不存在",
		"41" => "余额不足",
		"42" => "帐户已过期",
		"43" => "IP地址限制",
		"50" => "内容含有敏感词"
	);


}