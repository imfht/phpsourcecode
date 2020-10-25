<?php 
/**
* POPFrame
*
* 泡泡框架（murray.cn）
* @author Murray Wang <wjn_84@163.com>
* @version 1.0
* @package 搜狐邮件调用类
*/

defined('INPOP') or exit('Access Denied');

class sendcloud{

	public $apiKey; //密钥
	public $apiUser; //用户
	public $from;
	public $fromName;

	//初始化
    public function __construct($from = "", $fromName = "", $apiKey = "", $apiUser = ""){
		$this->apiKey = $apiKey;
		$this->apiUser = $apiUser;
		$this->from = $from;
		$this->fromName = $fromName;
    }

	//执行发送
	function sendMail($to = "", $subject = "", $var = "", $templateName = ""){
		if(!$to) return false;
		if(!$subject) return false;
		if(!$templateName) return false;
		if(!$var) return false;
        $url = 'http://sendcloud.sohu.com/webapi/mail.send_template.json';
        $vars = json_encode( array("to" => array($to),
                                   "sub" => array($var)
                                   )
                );
        $param = array(
            'api_user' => $this->apiUser,
            'api_key' => $this->apiKey,
            'from' => $this->from,
            'fromname' => $this->fromName,
            'substitution_vars' => $vars,
            'template_invoke_name' => $templateName,
            'subject' => $subject,
            'resp_email_id' => 'true'
        );
        $data = http_build_query($param);
		echo $data;
		$options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => $data
        ));
        $context  = stream_context_create($options);
        $return = file_get_contents($url, FILE_TEXT, $context);
		print_r($return);exit;
		return $return;
	}

}
?>