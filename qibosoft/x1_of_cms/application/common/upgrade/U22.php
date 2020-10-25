<?php
namespace app\common\upgrade;

class U22{
	public function up(){		 
		 
		 copy('https://x1.soyixia.net/bak/public/static/libs/bui/givemoney.jpg',
		         ROOT_PATH.'public/static/libs/bui/givemoney.jpg');

		 if(!is_file(ROOT_PATH.'public/static/libs/bui/givemoney.jpg')){
			 copy('https://x1.php168.com/public/static/libs/bui/givemoney.jpg',
		         ROOT_PATH.'public/static/libs/bui/givemoney.jpg');
		 }

	}
}