<?php
namespace app\common\upgrade;

class U9{
	public function up(){
	    set_time_limit(0);
	    mkdir(PUBLIC_PATH.'static/images/editor');
	    
		copy('https://x1.php168.com/public/static/images/editor/0.png',
		         PUBLIC_PATH.'static/images/editor/0.png');

		copy('https://x1.php168.com/public/static/images/editor/bg02.jpg',
		         PUBLIC_PATH.'static/images/editor/bg02.jpg');

		copy('https://x1.php168.com/public/static/images/editor/bg03.png',
		         PUBLIC_PATH.'static/images/editor/bg03.png');

		copy('https://x1.php168.com/public/static/images/editor/bg04.png',
		         PUBLIC_PATH.'static/images/editor/bg04.png');

		copy('https://x1.php168.com/public/static/images/editor/bg05.png',
		         PUBLIC_PATH.'static/images/editor/bg05.png');

		copy('https://x1.php168.com/public/static/images/editor/bg06.png',
		         PUBLIC_PATH.'static/images/editor/bg06.png');

		copy('https://x1.php168.com/public/static/images/editor/img01.jpg',
		         PUBLIC_PATH.'static/images/editor/img01.jpg');

		copy('https://x1.php168.com/public/static/images/editor/img02.jpg',
		         PUBLIC_PATH.'static/images/editor/img02.jpg');

		copy('https://x1.php168.com/public/static/images/editor/img03.jpg',
		         PUBLIC_PATH.'static/images/editor/img03.jpg');

		copy('https://x1.php168.com/public/static/images/editor/img04.jpg',
		         PUBLIC_PATH.'static/images/editor/img04.jpg');

		copy('https://x1.php168.com/public/static/images/editor/img05.jpg',
		         PUBLIC_PATH.'static/images/editor/img05.jpg');

		copy('https://x1.php168.com/public/static/images/editor/img06.jpg',
		         PUBLIC_PATH.'static/images/editor/img06.jpg');

		copy('https://x1.php168.com/public/static/images/editor/left_quote.jpg',
		         PUBLIC_PATH.'static/images/editor/left_quote.jpg');

		copy('https://x1.php168.com/public/static/images/editor/logo-135-web.png',
		         PUBLIC_PATH.'static/images/editor/logo-135-web.png');
		 
	}
}