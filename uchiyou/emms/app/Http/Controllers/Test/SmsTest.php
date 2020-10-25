<?php
namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use iscms\Alisms\SendsmsPusher as Sms;
use Illuminate\Http\Request;

class SmsTest extends Controller{
	
	public $sms;
	public function __construct(Sms $sms)
	{
		$this->sms = $sms;
	}
	
	public function testSms(Request $request){
		$smsParams = ['userName'=>'uchiyou','content'=>'testSMS'];
		$this->sms->send("17378118015","物资管理系统",json_encode($smsParams),'SMS_59600002');
		return "success";
	}
}