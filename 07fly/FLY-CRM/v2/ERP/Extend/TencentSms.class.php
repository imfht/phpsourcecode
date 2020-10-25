<?php

/**
 * Sms 文件处理类
 */
class TencentSms {

	//http://dx.ipyy.net/mms.aspx?action=send&userid=&account=账号&password=密码&mobile=15023239810,13527576163&subject=彩信主题&content=内容：短信接口测试&sendTime=&extno=
	//http://dx.ipyy.net/sms.aspx?action=send&userid=&account=账号&password=密码&mobile=15023239810,13527576163&content=内容&sendTime=&extno=
	public $sms_url = "http://dx.ipyy.net/smsJson.aspx";


	public function sms_send( $conf, $mobile, $params ,$tpl_id) {

		$random = rand( 1000, 9999 );

		$appkey = $conf[ 'appkey' ];
		$appid = $conf[ 'appid' ];
		$sign = $conf[ 'sign' ];
		$time = time();
		$sinstr = "appkey=$appkey&random=$random&time=$time&mobile=$mobile";
		//$sig 	=sha256($sinstr);
		$sig = hash( 'sha256', $sinstr );
		$post_data = array(
			'ext' => '',
			'extend' => '',
			'params' => $params,
			'sig' => "$sig",
			'sign' => "$sign",
			'tel' => array( 'mobile' => $mobile, 'nationcode' => 86 ),
			'time' => "$time",
			'tpl_id' => "$tpl_id"
		);

		print_r( $post_data );
		//$post_data=json_encode($post_data);

		$sms_url = "https://yun.tim.qq.com/v5/tlssmssvr/sendsms?sdkappid=$appid&random=$random";
		$rtn = $this->sendCurlPost( $sms_url, $post_data );
		return $rtn;
	}


	public function sms_overage( $acc, $pwd ) {
		$post_data = array(
			'action' => 'overage',
			'userid' => '',
			'account' => "$acc",
			'password' => "$pwd",
		);
		$rtn = $this->open_curl( $sms_url, $post_data );
		return $rtn;

	}

	/**
	 * 发送请求
	 *
	 * @param string $url      请求地址
	 * @param array  $dataObj  请求内容
	 * @return string 应答json字符串
	 */
	public
	function sendCurlPost( $url, $dataObj ) {
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, $url );
		curl_setopt( $curl, CURLOPT_HEADER, 0 );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $curl, CURLOPT_POST, 1 );
		curl_setopt( $curl, CURLOPT_CONNECTTIMEOUT, 60 );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, json_encode( $dataObj ) );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, 0 );
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, 0 );

		$ret = curl_exec( $curl );
		if ( false == $ret ) {
			// curl_exec failed
			$result = "{ \"result\":" . - 2 . ",\"errmsg\":\"" . curl_error( $curl ) . "\"}";
		} else {
			$rsp = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
			if ( 200 != $rsp ) {
				$result = "{ \"result\":" . - 1 . ",\"errmsg\":\"" . $rsp
					. " " . curl_error( $curl ) . "\"}";
			} else {
				$result = $ret;
			}
		}

		curl_close( $curl );

		return $result;
	}

	public
	function open_curl( $url, $post_data ) {
		$ch = curl_init(); //打开
		curl_setopt( $ch, CURLOPT_POST, true );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_URL, $url );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $post_data );
		$response = curl_exec( $ch );

		$errno = curl_errno( $ch );
		$info = curl_getinfo( $ch );
		//print_r($info);
		echo "<hr>";
		$error = curl_error( $ch );
		var_dump( $response );
		var_dump( $error );

		curl_close( $ch ); //关闭	
		return $response;
	}
}
?>