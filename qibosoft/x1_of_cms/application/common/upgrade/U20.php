<?php
namespace app\common\upgrade;

class U20{
	public function up(){
	    set_time_limit(0);
	    mkdir(PUBLIC_PATH.'static/images/qqface');
	    for ($i = 1; $i < 23; $i++) {
			if(!is_file(PUBLIC_PATH.'static/images/qqface/'.$i.'.gif')){
				copy('https://x1.php168.com/public/static/images/qqface/'.$i.'.gif',
	                PUBLIC_PATH.'static/images/qqface/'.$i.'.gif');
			}	        	        
	    }		 
	}
}