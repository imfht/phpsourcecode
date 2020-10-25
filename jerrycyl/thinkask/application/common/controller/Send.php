<?php

// namespace app\index\controller;
// use app\index\model\Send;
// error_reporting(0);
// class Index extends \think\Controller
// {

// 	public function sms()
// 	{
// 		if(request()->isPost()){
// 			$Send = new Send;
// 			$result = $Send->sms([
// 				'param'  => ['code'=>'123456','product'=>'安德兔'],
// 				'mobile'  => input('post.mobile/s','','trim,strip_tags'),
// 				'template'  => 'SMS_12940581',
// 			]);
// 			if($result !== true){
// 				return $this->error($result);
// 			}
// 			return $this->success('短信下发成功！');
// 		}
// 		return $this->fetch();
// 	}
// }


namespace app\common\Controller;
use think\Validate;
class Send extends \think\Controller
{
	public static $instance;

	public static function send(){
		if(empty($instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public static $sms_config = [
		'appkey'		=> '',//阿里大于APPKEY
		'secretKey' 	=> '',//阿里大于secretKey
		'FreeSignName' 	=> 'test',//短信签名
	];
	/**
	 * [sms 发送短信]
	 * @Author   Jerry
	 * @DateTime 2017-04-17T10:50:27+0800
	 * @Example  eg:
	 * @param    array                    $data [description]
	 * @return   [type]                         [description]
	 */
	public function sms($data=[])
	{
		$validate = new Validate([
			['param','require|array','参数必填|参数必须为数组'],
			['mobile','require|/1[34578]{1}\d{9}$/','手机号错误|手机号错误'],
			['template','require','模板id错误'],
		]);
		if (!$validate->check($data)) {
			return $validate->getError();
		}
		define('TOP_SDK_WORK_DIR', CACHE_PATH.'sms_tmp/');
		define('TOP_SDK_DEV_MODE', false);
		import('alidayu.TopClient',EXTEND_PATH);
		import('alidayu.AlibabaAliqinFcSmsNumSendRequest',EXTEND_PATH);
		import('alidayu.RequestCheckUtil',EXTEND_PATH);
		import('alidayu.ResultSet',EXTEND_PATH);
		import('alidayu.TopLogger',EXTEND_PATH);
		$config = self::$sms_config;
		$c = new \TopClient;
		$c->appkey = $config['appkey'];
		$c->secretKey = $config['secretKey'];
		$req = new \AlibabaAliqinFcSmsNumSendRequest;
		$req->setExtend('');
		$req->setSmsType('normal');
		$req->setSmsFreeSignName($config['FreeSignName']);
		$req->setSmsParam(json_encode($data['param']));
		$req->setRecNum($data['mobile']);
		$req->setSmsTemplateCode($data['template']);
		$result = $c->execute($req);
		$result = $this->_simplexml_to_array($result);
		if(isset($result['code'])){
			return $result['sub_code'];
		}
		return true;
	}
	/**
	 * [sms_list 发送列表短信列表]
	 * @Author   Jerry
	 * @DateTime 2017-04-17T10:50:48+0800
	 * @Example  eg:
	 * @return   [type]                   [description]
	 */
	public function sms_list(){
		$validate = new Validate([
			['param','require|array','参数必填|参数必须为数组'],
			['mobile','require|/1[34578]{1}\d{9}$/','手机号错误|手机号错误'],
			['template','require','模板id错误'],
		]);
		if (!$validate->check($data)) {
			return $validate->getError();
		}
		import('alidayu.TopClient',EXTEND_PATH);
		import('alidayu.AlibabaAliqinFcSmsNumSendRequest',EXTEND_PATH);
		import('alidayu.RequestCheckUtil',EXTEND_PATH);
		import('alidayu.ResultSet',EXTEND_PATH);
		import('alidayu.TopLogger',EXTEND_PATH);
		$c = new \TopClient;
		$c ->appkey = $appkey ;
		$c ->secretKey = $secret ;
		$req = new \AlibabaAliqinFcSmsNumQueryRequest;
		// $req ->setBizId( "1234^1234" );
		// $req ->setRecNum($data['mobile']);
		// $req ->setQueryDate( "20151215" );
		// $req ->setCurrentPage( "1" );
		// $req ->setPageSize( "10" );
		// $resp = $c ->execute( $req );

	}

	private function _simplexml_to_array($obj)
	{
		if(count($obj) >= 1){
			$result = $keys = [];
			foreach($obj as $key=>$value){
				isset($keys[$key]) ? ($keys[$key] += 1) : ($keys[$key] = 1);
				if( $keys[$key] == 1 ){
					$result[$key] = $this->_simplexml_to_array($value);
				}elseif( $keys[$key] == 2 ){
					$result[$key] = [$result[$key], $this->_simplexml_to_array($value)];
				}else if( $keys[$key] > 2 ){
					$result[$key][] = $this->_simplexml_to_array($value);
				}
			}
			return $result;
		}else if(count($obj) == 0){
			return (string)$obj;
		}
	}
	//==========================邮件====================================
	//// 	'EMAIL_FROM_NAME'        => '发件人',        // 发件人
	// 'EMAIL_SMTP'             => 'smtp.qq.com',  // smtp
	// 'EMAIL_USERNAME'         => 'QQ邮箱',        // 账号
	// 'EMAIL_PASSWORD'         => '授权码',        // 密码  注意: 163和QQ邮箱是授权码；不是登录的密码
	// 'EMAIL_SMTP_SECURE'      => 'ssl',          // 如果使用QQ邮箱；需要把此项改为  ssl
	// 'EMAIL_PORT'             => '465',          // 如果使用QQ邮箱；需要把此项改为  465
	/**
	 * [mail 发送邮件]
	 * @Author   Jerry
	 * @DateTime 2017-04-17T12:36:03+0800
	 * @Example  eg:
	 * @param    [type]                   $cusMail [description]
	 * @param    string                   $subject [description]
	 * @param    string                   $content [description]
	 * @return   [type]                            [description]
	 * 
	 */
	public function mail($customsmail,$data=[],$template="public"){
		$set = getset('mail_config');
    	$address = $customsmail; 
    	$email_smtp=$set['Host'];
	    $email_username=$set['username'];
	    $email_password=$set['password'];
	    $email_from_name=$set['from_email'];

	    $subject = $data['subject']?$data['subject']:"邮箱测试";
	    
	    if($template){
	    	$this->assign($data);
	    	$data['content'] = $this->fetch(APP_PATH."common/view/mail/{$template}.html");
	    }

	    $content = $data['content']?$data['content']:"这是一封测试邮件";
   		 $email_port=$set['port'];
	    if(empty($email_smtp) || empty($email_username) || empty($email_password) || empty($email_from_name)){
	        return array("error"=>1,"message"=>'邮箱配置不完整');
	    }
	    import('mail.PHPMailer',EXTEND_PATH);
	    import('mail.SMTP',EXTEND_PATH);
	    $phpmailer=new \Phpmailer();
	    // 设置PHPMailer使用SMTP服务器发送Email
	    $phpmailer->IsSMTP();
	    // 设置设置smtp_secure
	    // $phpmailer->SMTPSecure=$email_smtp_secure;
	    // 设置port
	    $phpmailer->Port=$email_port;
	    // 设置为html格式
	    $phpmailer->IsHTML(true);
	    // 设置邮件的字符编码'
	    $phpmailer->CharSet='UTF-8';
	    // 设置SMTP服务器。
	    $phpmailer->Host=$email_smtp;
	    // 设置为"需要验证"
	    $phpmailer->SMTPAuth=true;
	    // 设置用户名
	    $phpmailer->Username=$email_username;
	    // 设置密码
	    $phpmailer->Password=$email_password;
	    // 设置邮件头的From字段。
	    $phpmailer->From=$email_username;
	    // 设置发件人名字
	    $phpmailer->FromName=$email_from_name;
	    // 添加收件人地址，可以多次使用来添加多个收件人
	    if(is_array($address)){
	        foreach($address as $addressv){
	            $phpmailer->AddAddress($addressv);
	        }
	    }else{
	        $phpmailer->AddAddress($address);
	    }
	    // 设置邮件标题
	    $phpmailer->Subject=$subject;
	    // 设置邮件正文
	    $phpmailer->Body=$content;
	    // 发送邮件。
	    if(!$phpmailer->Send()) {
	        $phpmailererror=$phpmailer->ErrorInfo;
	        return array("error"=>1,"message"=>$phpmailererror);
	    }else{
	        return array("error"=>0);
	    }
	}
}
