<?php
namespace app\common\upgrade;

class U15{
	public function up(){
		 copy('https://x1.php168.com/public/static/index/login-regiser-icon.png',
		         ROOT_PATH.'public/static/index/login-regiser-icon.png');
		 copy('https://x1.php168.com/public/static/index/login-bg.jpg',
		         ROOT_PATH.'public/static/index/login-bg.jpg');
		 
	}
}