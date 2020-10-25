<?php
namespace App\Lib\Sms;
define('TOP_SDK_WORK_DIR','../storage/logs/');
class AliCode{
	private $_name = '智慧水站';
	private $_template = 'SMS_12891602';
	private $_type = array(
		1 => array('name' => '登录验证','template' => 'SMS_12891602' ),
		2 => array('name' => '身份验证','template' => 'SMS_5007648' ),
		3 => array('name' => '注册验证','template' => 'SMS_5007645' ),
		4 => array('name' => '变更验证','template' => 'SMS_5007642' ),
		5 => array('name' => '活动验证','template' => 'SMS_5007644' ),
		6 => array('name' => '变更验证','template' => 'SMS_5007642' ),
		7 => array('name' => '登录验证','template' => 'SMS_5007647' ),
		8 => array('name' => '智慧水站','template' => 'SMS_15095022')
	);
	private $_param = '';

	// 发送验证码
	public function sendCode($tel,$type=1,$item="免费设计"){
		// $time = \Session::get('send_tel_time',0);
		// if($time > time() - 60){
		// 	return false;
		// }
		$code = rand(100000,999999);
		//\Session::set('tel',$tel);
		//\Session::set('code',$code);
		//\Session::set('send_tel_time',time());
		//这里有个坑 已经补上去了 不加下面这句代码 session偶尔为空
		//\Session::save();
		$this->setCodeType($type, $code, $item);
		$this->sendAliCode($tel);
		/*if($type==5){
			$this->sendAliCode($tel,$code,5,$item);
		}else{
			$this->sendAliCode($tel,$code,$type);
		}*/

		return $code;
	}

	// 发送短信
	private function sendAliCode($tel){
		if (empty($this->_param)) {
			return false;
		}

		$c = new TopClient;
		$c->appkey = '23435745';
		$c->secretKey = 'bb710227c9726d57168d3a20bc10e9b4';
		$req = new AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend($tel);
		$req->setSmsType("normal");
		$req->setSmsFreeSignName($this->_name);
		$req->setSmsParam($this->_param);
		$req->setRecNum($tel);
		$req->setSmsTemplateCode($this->_template);
		$resp = $c->execute($req);
		return $resp;
	}

	// 消息通知
	public function sendNofityDriver($tel, $username, $from_site, $to_site) {
		$this->setCodeType(8, $username, $from_site, $to_site);
		$rtn = $this->sendAliCode($tel);
		return $rtn === false ? false : (isset($rtn->result->success) ? $rtn->result->success : true);
	}

	// 选择模板
	private function setCodeType($type = 1){
		if(isset($this->_type[$type])){
			$this->_name = $this->_type[$type]['name'];
			$this->_template  = $this->_type[$type]['template'];
		}

		$args_num = func_num_args();	// 请求参数个数

		if ($type === 1 && $args_num > 1) {
			// code
			$this->_param = '{"code":"'.func_get_arg(1).'","product":"智慧水站"}';
		}
		else if ($type === 5 && $args_num > 2) {
			// code, item
			$this->_param = '{"code":"'.func_get_arg(1).'","product":"智慧水站","item":'.func_get_arg(2).'}';
		}
		else if ($type === 8 && $args_num > 3) {
			// username, from_site, to_site
			$this->_param = '{"username":"'.func_get_arg(1).'","from_site":"'.func_get_arg(2).'","to_site":"'.func_get_arg(3).'"}';
		}
	}

	function checkCode($input_tel,$input_code){
		if(empty($input_tel) || empty($input_code)){
			return false;
		}
		// $tel = \Session::get('tel','');
		// $code = \Session::get('code','');
		if($tel == $input_tel && $input_code == $code){
			// \Session::forget('tel');
			// \Session::forget('code');
			// \Session::forget('send_tel_time');
			return true;
		}else{
			return false;
		}
	}

  	function sendUrl($url,$data){

		$str_data = array();
		foreach ($data as $key => $value) {
			$str_data[] = $key.'='.$value;
		}
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // post数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // post的变量
        curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $str_data));
        $output = curl_exec($ch);
        $rinfo = curl_getinfo($ch);
        curl_close($ch);
  	}  
}