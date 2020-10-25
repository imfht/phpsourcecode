<?php
namespace App\Http\Controllers\Test;

use App\Util\SendEmail;
use App\Http\Controllers\Controller;

class EmailTest extends Controller{
	
	public function testEmail(){
		SendEmail::sendEmailTest();
		//return SendEmail::sendPhpEmailTest();
		//return SendEmail::testPHPMailer();
		//SendEmail::postPHPMailer('1373918920@qq.com','test email','test email body');
		//return "<br>sucess";
	}
}