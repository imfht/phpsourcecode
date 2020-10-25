<?php
namespace Aliyun\DySDKLite\Sms;
require_once "../../plugins/alisms/SignatureHelper.php";
use Aliyun\DySDKLite\SignatureHelper;
//以上为短信调用接口
include "../../libs/function.php";
	$id=date('YmdHis', time());
	//向数据库提交表单
	$database->insert("book", array(  
	    "id" => $id,
		"type" => $_POST["type"],
		"title" => htmlentities($_POST["title"]),
		"content" => htmlentities($_POST["content"]),
		"name" => htmlentities($_POST["name"]),
		"phone" => htmlentities($_POST["phone"]),
		"email" => htmlentities($_POST["email"]),
		"ip" => getIp(),
		"date" => time() 
	));
if($system_sendsms == 'on'){
	/**
	 * 发送短信
	 */
	$typename = $database->select("type","name",["id[=]" =>$_POST["type"]]);//获取分类名称
	function sendSms() {
		global $system_smsnumber,$system_sitename,$system_KeyId,$system_KeySecret,$system_SignName,$system_TemplateCode,$system_sendcontent,$typename;//引入function全局变量
		if($system_sendcontent == 'on'){
			$sendcontent = ",留言内容：".$_POST["content"];
		}else{
			$sendcontent = "";
		};
	    $params = array ();
	    $accessKeyId = $system_KeyId;
	    $accessKeySecret = $system_KeySecret;
	    $params["PhoneNumbers"] = $system_smsnumber;
	    $params["SignName"] = $system_SignName;
	    $params["TemplateCode"] = $system_TemplateCode;
	    $params['TemplateParam'] = Array (
	    	//请根据阿里短信模板修改对应数组键值=变量名；
	        "sitename" => $system_sitename,
	        "title" => $_POST["title"],
	        "type" => $typename[0].$sendcontent
	    );
	    $params['OutId'] = "12345";
	    $params['SmsUpExtendCode'] = "1234567";
	    if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
	        $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
	    }
	    $helper = new SignatureHelper();
	    $content = $helper->request(
	        $accessKeyId,
	        $accessKeySecret,
	        "dysmsapi.aliyuncs.com",
	        array_merge($params, array(
	            "RegionId" => "cn-hangzhou",
	            "Action" => "SendSms",
	            "Version" => "2017-05-25",
	        ))
	    );
	    return $content;
	}
	ini_set("display_errors", "on"); // 显示错误提示，仅用于测试时排查问题
	set_time_limit(0); // 防止脚本超时，仅用于测试使用，生产环境请按实际情况设置
	header("Content-Type: text/plain; charset=utf-8"); // 输出为utf-8的文本格式，仅用于测试
	//print_r(sendSms());
	$data = (array)sendSms();//对象转数组
	//print_r($data["Code"]);
}
echo "OK";
?>