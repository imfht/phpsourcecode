<?php
class ValidationCode{
	
	private $width;
	private $height;
	private $number;
	private $codetype;
	private $randstr;
	private $codetext;
	private $image;
	
	function __construct($width = 80,$height = 30,$number = 4,$codetype = 3){
		$this->width = $width;
		$this->height = $height;
		$this->number = $number;
		$this->codetype = $codetype;
	}
	
	//输出验证码图像
	public function getCodeImage(){
		//创建图像背景
		$this->createBg();
		//生成干扰元素
		$this->createDisturb();
		//把验证码写入图像
		$this->drawTextCode();
		//输出图像
		$this->outImage();
		
	}
	
	//输出验证码字符串
	public function getCodeText(){
		return strtolower($this->codetext);
	}
	
	private function createBg(){
		//创建随机背景色黑色边框的画布
		$this->image = imagecreatetruecolor($this->width,$this->height);
		$background = imagecolorallocate($this->image,rand(200,255),rand(200,255),rand(200,255));
		imagefill($this->image,0,0,$background);
		$bordercolor = imagecolorallocate($this->image,0,0,0);
		imagerectangle($this->image,0,0,$this->width,$this->height,$bordercolor);
	}
	
	//生成验证码字符
	private function getRandStr(){
		//根据类型生成随机字符串
		switch ($this->codetype){
			case 1 :
				$randstr = '0123456789';
				break;
			case 2 :
				$randstr = 'asdfghjklqwertyuiopzxcvbnmASDFGHJKLQWERTYUIOPZXCVBNM';
				break;
			case 3 :
				$randstr = '0123456789asdfghjklqwertyuiopzxcvbnmASDFGHJKLQWERTYUIOPZXCVBNM';
				break;
		}
		
		for($i = 0;$i < $this->number;$i++){
			$randstr = str_shuffle($randstr);
			$this->codetext .= substr($randstr,0,1);
		}	
	}
	
	private function createDisturb(){
		//生成随机点
		for($i = 0; $i < 100; $i++){
			$pixcolor = imagecolorallocate($this->image,rand(150,200),rand(150,200),rand(150,200));
			imagesetpixel($this->image,rand(1,$this->width-2),rand(1,$this->height-2),$pixcolor);
		}
		//随机画线
		for($i = 0; $i < 10; $i++){
			$linecolor = imagecolorallocate($this->image,rand(150,200),rand(150,200),rand(150,200));
			imageline($this->image,rand(1,$this->width-2),rand(1,$this->height-2),rand(1,$this->width-2),rand(1,$this->height-2),$linecolor);
		}
	}
	
	
	private function drawTextCode(){
		//生成验证码字符
		$this->getRandStr();
		$font = "./arial.ttf";
		for($i = 0; $i < $this->number; $i++){
			$textcolor = imagecolorallocate($this->image,rand(0,100),rand(0,100),rand(0,100));
			$x = floor($this->width/$this->number)*$i+1;
			imagettftext($this->image,14,0,$x,$this->height-($this->height-14)/2,$textcolor,$font,substr($this->codetext,$i,1));
		}
	}
	
	//判断服务器支持的图像类型，并输出图像
	private function outImage(){
		
		if(imagetypes() & IMG_PNG){
			header("Content-Type:image/png");
			imagepng($this->image);
		}else if(imagetypes() & IMG_JPG){
			header("Content-Type:image/jpeg");
			imagejpeg($this->image);
		}else if(ifimagetypes() & IMG_GIF){
			header("Content-Type:image/gif");
			imagegif($this->image);
		}else{
			die("你的服务器不支持png、jpeg、jpg、gif图片格式，无法生成验证码...");
		}
	}
}
