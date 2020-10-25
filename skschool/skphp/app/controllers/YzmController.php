<?php
namespace App\Controllers;
use Skschool\Controller;

class YzmController extends Controller {
	
	public function index()
	{
		if(IS_POST)
		{
			session_start();
			$yzm = $_POST['yzm'];
			$Verify = new \Skschool\Verify();
			if($Verify->check($yzm,2) == 0) echo '验证码错误';exit;
		}else{
			$this->display();
		}
	}
	
	public function yzm(){
		ob_clean();
		$config =    array(
			'fontSize'    =>    30,    // 验证码字体大小
			'length'      =>    3,     // 验证码位数
			'useNoise'    =>    false, // 关闭验证码杂点
		);
		$Verify = new \Skschool\Verify();
		$Verify->init($config);
		$Verify->entry(2);
	}
}