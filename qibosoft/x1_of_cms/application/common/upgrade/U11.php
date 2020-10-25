<?php
namespace app\common\upgrade;

class U11{
	public function up(){
	    set_time_limit(0);
	    @mkdir(PUBLIC_PATH.'static/haibao_style');
	    @mkdir(PUBLIC_PATH.'static/haibao_style/default');
	    
		copy('https://x1.php168.com/public/static/haibao_style/default/demo.jpg',
		         PUBLIC_PATH.'static/haibao_style/default/demo.jpg');
		
		copy('https://x1.php168.com/public/static/haibao_style/default/haibao_hua.png',
		        PUBLIC_PATH.'static/haibao_style/default/haibao_hua.png');
		
		copy('https://x1.php168.com/public/static/haibao_style/default/haibao_bg.png',
		        PUBLIC_PATH.'static/haibao_style/default/haibao_bg.png');
		 
	}
}