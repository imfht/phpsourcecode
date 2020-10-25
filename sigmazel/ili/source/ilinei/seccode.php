<?php
//版权所有(C) 2014 www.ilinei.com

namespace ilinei;

class seccode{
	private $mode;
	private $width;
	private $height;
	private $num = 5;
	private $pixelnum = 10;
	private $linenum = 0;
	private $fontdir;
	private $border = false;
	private $borderColor = '255,255,255';
	private $seccode = '';
	
	public function __construct($mode, $width, $height, $fontdir = '/static/fonts/tempsitc.ttf'){
		$this->mode = $mode;
		$this->width = $width;
		$this->height = $height;
		$this->fontdir = $fontdir;
	}
	
	public function random(){
		$chars = explode(',', $this->getchars());
		$this->seccode = '';
		$charmax = count($chars) - 1;
		
		for($i = 0 ; $i < $this->num; $i++){
			$random = mt_rand(0, $charmax);
			$this->seccode .= $chars[$random];
			unset($random);
		}
		
		return $this->seccode;
	}
	
	public function display(){
		$im = imagecreate($this->width, $this->height);
		
		$black = imagecolorallocate($im, 0, 0, 0);
		$white = imagecolorallocate($im, 255, 255, 255);
		$bgcolor = imagecolorallocate($im, 250, 250, 250);
		
		imagefill($im, 0, 0, $bgcolor);
		
		$x = 4;
		if(empty($this->seccode)) $this->random();
		
		for($i = 0; $i < strlen($this->seccode); $i++){
			$size = 14;
			$angle = mt_rand(-20, 20);
			
			if($this->border) $y = mt_rand(($size + 3), ($this->height - 3));
			else $y = mt_rand(($size + 2), ($this->height - 2));
			
			$rand_color = $this->randcolor(0, 10, 0, 10, 0, 10);
			$randcolor = imagecolorallocate($im, $rand_color[0], $rand_color[1], $rand_color[2]);
			
			imagettftext($im, $size, $angle, $x, $y, $randcolor, ROOTPATH.$this->fontdir, $this->seccode{$i});
			
			$x = $x + intval($this->width / ($this->num + 1)) + 2;
		}
		
		for($i = 0; $i < $this->pixelnum; $i++){
			$rand_color = $this->randcolor(50, 250, 0, 250, 50, 250);
			$rand_color_pixel = imagecolorallocate($im, $rand_color[0], $rand_color[1], $rand_color[2]);
			
			imagesetpixel($im, mt_rand() % $this->width, mt_rand() % $this->height, $rand_color_pixel);
		}
		
		for($i = 0; $i < $this->linenum; $i++){
			$rand_color = $this->randcolor(100, 100, 100, 100, 100, 100);
			$rand_color_line = imagecolorallocate($im, $rand_color[0], $rand_color[1], $rand_color[2]);
			imageline($im, mt_rand(0, intval($this->width / 3)), mt_rand(0, $this->height), 
							mt_rand(intval($this->width - ($this->width / 3)), $this->width), 
							mt_rand(0,$this->height), $rand_color_line);
		}
		
		if($this->border){
			$borderColor = explode(',', $this->borderColor);
			$border_color_line = imagecolorallocate($im, $borderColor[0], $borderColor[1], $borderColor[2]);
			
			imageline($im, 0, 0, $this->width, 0, $border_color_line);
			imageline($im, 0, 0, 0, $this->height, $border_color_line);
			imageline($im, 0, $this->height - 1, $this->width, $this->height - 1, $border_color_line);
			imageline($im, $this->width - 1, 0, $this->width - 1, $this->height, $border_color_line);
		}
		
		//imageantialias($im, true);
		
		if(function_exists("imagegif")){
			header("Content-type: image/gif");
			imagegif($im);
		}elseif(function_exists("imagepng")){
			header("Content-type: image/png");
			imagepng($im);
		}elseif(function_exists("imagejpeg")){
			header("Content-type: image/jpeg");
			imagejpeg($im, "", 80);
		}elseif (function_exists("imagewbmp")){
			header ("Content-type: image/vnd.wap.wbmp");
			imagewbmp($im);
		}
		
		imagedestroy($im);
	}
	
	private function getchars(){
		if($this->mode == 1) return "2,3,4,5,6,7,8,9";
		elseif ($this->mode == 2) return "a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
		elseif ($this->mode == 3) return "2,3,4,5,6,7,8,9,a,b,c,d,e,f,g,h,i,j,k,m,n,p,q,r,s,t,u,v,w,x,y,z";
		else return "3,4,5,6,7,8,9,a,b,c,d,h,k,p,r,s,t,w,x,y";
	}
	
	private function randcolor($rs, $re, $gs, $ge, $bs, $be){
		$r = mt_rand($rs, $re);
		$g = mt_rand($gs, $ge);
		$b = mt_rand($bs, $be);
		return array($r, $g, $b);
	}
}
?>