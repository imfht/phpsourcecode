<?php
namespace app\common\upgrade;

class U8{
	public function up(){
	    set_time_limit(0);
	    mkdir(PUBLIC_PATH.'static/libs/html2canvas');
	    
		copy('https://x1.php168.com/public/static/libs/html2canvas/blank.png',
		         ROOT_PATH.'public/static/libs/html2canvas/blank.png');

		copy('https://x1.php168.com/public/static/libs/html2canvas/close.png',
		         ROOT_PATH.'public/static/libs/html2canvas/close.png');

		copy('https://x1.php168.com/public/static/libs/html2canvas/loading.gif',
		         ROOT_PATH.'public/static/libs/html2canvas/loading.gif');

		copy('https://x1.php168.com/public/static/libs/html2canvas/icon-qr-code.png',
		         ROOT_PATH.'public/static/libs/html2canvas/icon-qr-code.png');

		copy('https://x1.php168.com/public/static/libs/html2canvas/html2canvas.js',
		         ROOT_PATH.'public/static/libs/html2canvas/html2canvas.js');
		 
	}
}