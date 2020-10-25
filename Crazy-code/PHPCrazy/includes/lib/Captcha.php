<?php
/*
*	Package:		PHPCrazy
*	Link:			http://zhangyun.org/
*	Author: 		Crazy <mailzhangyun@qq.com>
*	Copyright:		2014-2015 Crazy
*	License:		Please read the LICENSE file.
*/

/**
*	算术验证码类
*/
class MathCaptcha {
	
	/*
	*	MathCaptcha(宽, 高, Session变量名)
	*/
	function __construct($w, $h, $v) {
		
		// 新建图像
		$im = imagecreate($w, $h);
		
		// 背景颜色
		// imagecolorallocate($im, 14, 114, 180);

		// 分配颜色
		$red = imagecolorallocate($im, 255, 0, 0);

		$white = imagecolorallocate($im, 255, 255, 255);

		$num1 = rand(1, 20);
		$num2 = rand(1, 20);

		// 指定算术
		$math_array = array('+'=>'+', '-'=>'-', '*'=>'x');
		$math = array_rand($math_array, 1);
		$evalStr = "\n".'$_SESSION[$v] = $num1'.$math.'$num2;';
		$mathStr = $math_array[$math];
		eval($evalStr);

		$gray = imagecolorallocate($im, 118, 151, 199);
		$black = imagecolorallocate($im, mt_rand(0, 100), mt_rand(0, 100), mt_rand(0, 100));

		//画背景
		imagefilledrectangle($im, 0, 0, 100, 24, $black);
		
		//在画布上随机生成大量点，起干扰作用;
		for ($i = 0; $i < 80; $i++) {
			imagesetpixel($im, rand(0, $w), rand(0, $h), $gray);
		}

		imagestring($im, 5, 5, 4, $num1, $red);
		imagestring($im, 5, 30, 3, $mathStr, $red);
		imagestring($im, 5, 45, 4, $num2, $red);
		imagestring($im, 5, 70, 3, "=", $red);
		imagestring($im, 5, 80, 2, "?", $white);

		header("Content-type: image/png");
		imagepng($im);
		imagedestroy($im);
	}
}
?>