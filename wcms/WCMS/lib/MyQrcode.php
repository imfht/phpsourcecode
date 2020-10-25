<?php
require 'phpqrcode/phpqrcode.php';
class MyQrcode {
	public $logo = "../logo.png";
	public $islogo = false;
	public $dir = "";
	public $size = 22;
	
	public function __construct() {
		if (! file_exists ( ROOT . '/config/logo.png' )) {
			echo "二维码logo不存在,请检查根目录config文件夹是否存在logo.png";
			exit ();
		}
	}
	public function create($url, $name) {
		
		//set it to writable location, a place for temp generated PNG files
		$PNG_TEMP_DIR = $this->dir;
		//html PNG location prefix
		$PNG_WEB_DIR = 'temp/';
		//ofcourse we need rights to create temp dir
		if (! file_exists ( $PNG_TEMP_DIR )) {
			mkdir ( $PNG_TEMP_DIR, 0777, true );
		}
		//remember to sanitize user input in real-life solution !!!
		$errorCorrectionLevel = 'Q';
		$matrixPointSize = $this->size;
		$filename = $PNG_TEMP_DIR . $name . '.png';
		Qrcode::png ( $url, $filename, $errorCorrectionLevel, $matrixPointSize, 2 );
		
		if ($this->islogo) {
			$this->logo ( $filename );
		}
	}
	
	protected function logo($QR) {
		//人物图片
		$path_1 = $QR;
		//装备图片
		$path_2 = $this->logo;
		//将人物和装备图片分别取到两个画布中
		$image_1 = imagecreatefrompng ( $path_1 );
		$image_2 = imagecreatefrompng ( $path_2 );
		//创建一个和人物图片一样大小的真彩色画布（ps：只有这样才能保证后面copy装备图片的时候不会失真）
		$image_3 = imageCreatetruecolor ( imagesx ( $image_1 ), imagesy ( $image_1 ) );
		//为真彩色画布创建白色背景，再设置为透明
		$color = imagecolorallocate ( $image_3, 255, 255, 255 );
		imagefill ( $image_3, 0, 0, $color );
		//  imageColorTransparent($image_3, $color);
		//首先将人物画布采样copy到真彩色画布中，不会失真
		imagecopyresampled ( $image_3, $image_1, 0, 0, 0, 0, imagesx ( $image_1 ), imagesy ( $image_1 ), imagesx ( $image_1 ), imagesy ( $image_1 ) );
		//再将装备图片copy到已经具有人物图像的真彩色画布中，同样也不会失真
		imagecopymerge ( $image_3, $image_2, 160, 150, 0, 0, imagesx ( $image_2 ), imagesy ( $image_2 ), 100 );
		//将画布保存到指定的gif文件
		imagepng ( $image_3, $path_1 );
	}
}