<?php
namespace app\common\upgrade;

class U6{
	public function up(){
		 copy('https://x1.php168.com/public/static/images/recom.png',
		         ROOT_PATH.'public/static/images/recom.png');
		 
	}
}