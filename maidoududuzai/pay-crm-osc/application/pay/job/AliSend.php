<?php

namespace app\pay\job;

use \think\Db;
use \think\queue\Job;

require_once EXTEND_PATH . 'sdk/aliyun-php-sdk-core/Config.php';

use \Dm\Request\V20151123 as Dm;
use \Dysmsapi\Request\V20170525 as Dysmsapi;

class AliSend
{

	public function set($request, $options = []) {
		foreach($options as $key => $val) {
			$method = 'set' . $key;
			if(is_callable([$request, $method])) {
				$request->$method($val);
			}
		}
	}

	public function reset_password(Job $job, $data)
	{
		echo Tool::show($data);
		$value = [];
		if(isset($data['agent_id'])) {
			$value = Db::name('agent')->where('agent_id', '=', $data['agent_id'])->field('per_phone, per_email, password')->find();
		}
		if(isset($data['merchant_id'])) {
			$value = Db::name('merchant')->where('merchant_id', '=', $data['merchant_id'])->field('per_phone, per_email, password')->find();
		}
		if(!$value) {
			$job->delete();
		} else {
			$sms = model('\app\common\model\Config')->config(null, 'sms_');
			$AcsClient = new \DefaultAcsClient(\DefaultProfile::getProfile('cn-hangzhou', $sms['sms_AccessKeyId'], $sms['sms_AccessKeySecret']));
			try {
				$request = new Dysmsapi\SendSmsRequest();
				$this->set($request, [
					'PhoneNumbers' => $value['per_phone'],
					'SignName' => $sms['sms_SignName'],
					'TemplateCode' => $sms['sms_TemplateCode'],
					'TemplateParam' => JSON(['password'=> authcode($value['password'], 'DECODE')])
				]);
				$res = $AcsClient->getAcsResponse($request);
				if($res->Code == 'OK') {
					//
					//$job->delete();
				}
				echo Tool::show($res);
			} catch (\ClientException $e) {
				echo Tool::show($e->getErrorMessage());
			} catch (\ServerException $e) {
				echo Tool::show($e->getErrorMessage());
			}
			$job->delete();
		}
	}

	public function add_agent(Job $job, $data)
	{
		$agent = Db::name('agent')->where('agent_id', '=', $data['agent_id'])->find();
		if(!$agent) {
			$job->delete();
		} else {
			$mail = model('\app\common\model\Config')->config(null, 'mail_');
			$AcsClient = new \DefaultAcsClient(\DefaultProfile::getProfile('cn-hangzhou', $mail['mail_AccessKeyId'], $mail['mail_AccessKeySecret']));
			try {
				$request = new Dm\SingleSendMailRequest();
				$this->set($request, [
					'AccountName' => $mail['mail_AccountName'],
					'AddressType' => '1',
					'ReplyToAddress' => 'false',
					'ToAddress' => $agent['per_email'],
					'FromAlias' => $mail['mail_FromAlias'],
					'Subject' => '代理帐号创建成功',
					'HtmlBody' => '尊敬的' . $agent['per_name'] . '：您的代理帐号创建成功' . '<br>' . 
					'代理名称：' . $agent['agent_name'] . '<br>' . 
					'代理编号：' . $agent['agent_no'] . '<br>' . 
					'手机号码：' . $agent['per_phone'] . '<br>' . 
					'登录密码：' . authcode($agent['password'], 'DECODE')
				]);
				$res = $AcsClient->getAcsResponse($request);
				echo Tool::show($res);
			} catch (\ClientException $e) {
				echo Tool::show($e->getErrorMessage());
			} catch (\ServerException $e) {
				echo Tool::show($e->getErrorMessage());
			}
			$job->delete();
		}
	}

	public function add_merchant(Job $job, $data)
	{
		$merchant = Db::name('merchant')->where('merchant_id', '=', $data['merchant_id'])->find();
		if(!$merchant) {
			$job->delete();
		} else {
			$mail = model('\app\common\model\Config')->config(null, 'mail_');
			$AcsClient = new \DefaultAcsClient(\DefaultProfile::getProfile('cn-hangzhou', $mail['mail_AccessKeyId'], $mail['mail_AccessKeySecret']));
			try {
				$request = new Dm\SingleSendMailRequest();
				$this->set($request, [
					'AccountName' => $mail['mail_AccountName'],
					'AddressType' => '1',
					'ReplyToAddress' => 'false',
					'ToAddress' => $merchant['per_email'],
					'FromAlias' => $mail['mail_FromAlias'],
					'Subject' => '商户帐号创建成功',
					'HtmlBody' => '尊敬的' . $merchant['per_name'] . '：您的商户帐号创建成功' . '<br>' . 
					'商户名称：' . $merchant['merchant_name'] . '<br>' . 
					'商户编号：' . $merchant['merchant_no'] . '<br>' . 
					'手机号码：' . $merchant['per_phone'] . '<br>' . 
					'登录密码：' . authcode($merchant['password'], 'DECODE')
				]);
				$res = $AcsClient->getAcsResponse($request);
				echo Tool::show($res);
			} catch (\ClientException $e) {
				echo Tool::show($e->getErrorMessage());
			} catch (\ServerException $e) {
				echo Tool::show($e->getErrorMessage());
			}
			$job->delete();
		}
	}

	public function audit_merchant(Job $job, $data)
	{
		echo Tool::show($data);
		$agent = Db::name('agent')->where('agent_id', '=', $data['agent_id'])->find();
		$merchant = Db::name('merchant')->where('merchant_id', '=', $data['merchant_id'])->find();
		if(!$agent || !$merchant || empty($agent['per_email'])) {
			$job->delete();
		} else {
			if($merchant['check_status'] == '-1') {
				$result_message = '审核通过。';
			} else {
				$result_message = '审核不通过，请完善资料后重新提交。';
			}
			$mail = model('\app\common\model\Config')->config(null, 'mail_');
			$AcsClient = new \DefaultAcsClient(\DefaultProfile::getProfile('cn-hangzhou', $mail['mail_AccessKeyId'], $mail['mail_AccessKeySecret']));
			try {
				$request = new Dm\SingleSendMailRequest();
				$this->set($request, [
					'AccountName' => $mail['mail_AccountName'],
					'AddressType' => '1',
					'ReplyToAddress' => 'false',
					'ToAddress' => $agent['per_email'],
					'FromAlias' => $mail['mail_FromAlias'],
					'Subject' => '商户审核结果通知',
					'HtmlBody' => '尊敬的' . $agent['per_name'] . '：您提交的商户[' .$merchant['merchant_name'] . ']' . $result_message
				]);
				$res = $AcsClient->getAcsResponse($request);
				echo Tool::show($res);
			} catch (\ClientException $e) {
				echo Tool::show($e->getErrorMessage());
			} catch (\ServerException $e) {
				echo Tool::show($e->getErrorMessage());
			}
			$job->delete();
		}
	}

}

