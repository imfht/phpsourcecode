<?php
namespace app\common\upgrade;

class U7{
	public function up(){
	    set_time_limit(0);
	    mkdir(PUBLIC_PATH.'static/images/qqface');
	    for ($i = 1; $i < 13; $i++) {
	        copy('https://x1.php168.com/public/static/images/qqface/'.$i.'.gif',
	                PUBLIC_PATH.'static/images/qqface/'.$i.'.gif');	        
	    }
	    copy('https://x1.php168.com/public/static/images/qun-banner.jpg',
	            PUBLIC_PATH.'static/images/qun-banner.jpg');
		 
	}
}