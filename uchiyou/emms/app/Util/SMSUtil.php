<?php
namespace App\Util;

use iscms\Alisms\SendsmsPusher as Sms;

class SMSUtil{
	
	public static function sendDeliver(Sms $sms,$phone,$address){
		// 地址参数过长，所以只保留最后一部分
		if(!is_array($address)){
			$address = StringUtil::recordToRequestAddress($address);
		}
		// 一次发送多条短信
		if(is_array($phone)){
			$phone = implode(',', $phone);
		}
		$smsParams = ['address'=>$address[3]]; // 参数不能超过15个汉字
		$result = $sms->send($phone,"物资管理系统",json_encode($smsParams),'SMS_60900132');
		return true;
	}
	public static function sendReturnNotice(Sms $sms,$phone,$materialName){
		$smsParams = ['name'=>$materialName];
		if(is_array($phone)){
			$phone = implode(',', $phone);
		}
		$sms->send($phone,"物资管理系统",json_encode($smsParams),'SMS_61130065');
		return true;
	}
	public static function sendAppointmentAvailable(Sms $sms,$phone,$materialName){
		$smsParams = ['name'=>$materialName];
		if(is_array($phone)){
			$phone = implode(',', $phone);
		}
		$sms->send($phone,"物资管理系统",json_encode($smsParams),'SMS_61105087');
		return true;
	}
}