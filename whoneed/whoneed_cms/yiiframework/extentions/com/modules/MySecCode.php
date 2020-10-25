<?php
class MySecCode extends CComponent
{
	static public function show()
	{
		Yii::app()->user->setState('re_code', '');
		$type = 'gif';
		$width= 60;
		$height= 20;
		header("Content-type: image/".$type);
		srand((double)microtime()*1000000);
		$randval = self::randStr(4,"ALL");
		if($type!='gif' && function_exists('imagecreatetruecolor')){
			$im = @imagecreatetruecolor($width,$height);
		}else{
			$im = @imagecreate($width,$height);
		}
		$r = Array(100,111,155,123);
		$g = Array(100,136,137,115);
		$b = Array(100,136,166,125);

		$key = rand(0,3);

		$backColor = ImageColorAllocate($im,$r[$key],$g[$key],$b[$key]);//背景色（随机）
		$borderColor = ImageColorAllocate($im, 20, 66, 111);//边框色
		//$pointColor = ImageColorAllocate($im, 255, 170, 255);//点颜色

		@imagefilledrectangle($im, 0, 0, $width - 1, $height - 1, $backColor);//背景位置
		@imagerectangle($im, 0, 0, $width-1, $height-1, $borderColor); //边框位置
		$stringColor = ImageColorAllocate($im, 255,255,255);

		for($i=0;$i<=100;$i++){
			$pointX = rand(2,$width-2);
			$pointY = rand(2,$height-2);
			//@imagesetpixel($im, $pointX, $pointY, $pointColor);
		}

		@imagestring($im, 7, 15, 1, $randval, $stringColor);
		$ImageFun='Image'.$type;
		$ImageFun($im);
		@ImageDestroy($im);
		Yii::app()->user->setState('re_code', $randval);
	}

	//产生随机字符串
	static public function randStr($len=6,$format='ALL')
	{
		switch($format) {
			case 'ALL':
				$chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
				break;
			case 'CHAR':
				$chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz';
				break;
			case 'NUMBER':
				$chars='123456789';
				break;
			default :
				$chars='ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
				break;
		}
		$string="";
		while(strlen($string)<$len)
		$string.=substr($chars,(mt_rand()%strlen($chars)),1);
		return $string;
	}
}