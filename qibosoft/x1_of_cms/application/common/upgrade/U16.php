<?php
namespace app\common\upgrade;

class U16{
	public static function up(){
	    set_time_limit(0);
	    if (!is_dir(ROOT_PATH.'public/static/libs/amazeui/images')) {
	        mkdir(ROOT_PATH.'public/static/libs/amazeui');
	        mkdir(ROOT_PATH.'public/static/libs/amazeui/css');
	        mkdir(ROOT_PATH.'public/static/libs/amazeui/js');
	        mkdir(ROOT_PATH.'public/static/libs/amazeui/images');
	        mkdir(ROOT_PATH.'public/static/libs/amazeui/images/icon');
	    }
// 		 copy('https://x1.php168.com/public/static/libs/amazeui/css/amazeui.min.css',
// 		     ROOT_PATH.'public/static/libs/amazeui/css/amazeui.min.css');
// 		 copy('https://x1.php168.com/public/static/libs/amazeui/css/main.css',
// 		     ROOT_PATH.'public/static/libs/amazeui/css/main.css');
		 
// 		 copy('https://x1.php168.com/public/static/libs/amazeui/js/amazeui.min.js',
// 		     ROOT_PATH.'public/static/libs/amazeui/js/amazeui.min.js');
// 		 copy('https://x1.php168.com/public/static/libs/amazeui/js/wechat.js',
// 		     ROOT_PATH.'public/static/libs/amazeui/js/wechat.js');
// 		 copy('https://x1.php168.com/public/static/libs/amazeui/js/zUI.js',
// 		     ROOT_PATH.'public/static/libs/amazeui/js/zUI.js');
		 
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/bg.jpg',
		     ROOT_PATH.'public/static/libs/amazeui/images/bg.jpg');
		 
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_2.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_2.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_2_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_2_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_3.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_3.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_3_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_3_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_4.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_4.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_4_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_4_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/head_5.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/head_5.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon7.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon7.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon8.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon8.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon9.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon9.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon10.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon10.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon11.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon11.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon12.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon12.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon13.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon13.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon13_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon13_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon14.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon14.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon14_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon14_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon16.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon16.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon16_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon16_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon17.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon17.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon17_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon17_1.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon18.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon18.png');
		 copy('https://x1.php168.com/public/static/libs/amazeui/images/icon/icon18_1.png',
		     ROOT_PATH.'public/static/libs/amazeui/images/icon/icon18_1.png');
		 
	}
}