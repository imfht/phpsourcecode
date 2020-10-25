<?php
//通用验证码类
//@copyright wangsl@500wan.com
class Utilscaption
{
	var $Width      = 60;           	//图片宽
	var $Height     = 30;           	//图片高
	var $Length     = 4;            	//验证码位数
	var $BgColor    = "#FFFFFF";    	//背景色

	var $TFonts = array("font.ttf");
	var $TFontSize=array(17,20); 		//字体大小范围
	var $TFontAngle=array(-20,20); 		//旋转角度

	var $Chars   = "0123456789";        //验证码范围（字母数字）

	var $Code    = array();             //验证码
	var $Image   = "";              	//图形对象
	var $FontColors=array('#f36161','#6bc146','#5368bd');  //字体颜色,红绿蓝
	var $TPadden = 0.75;				//字符间距
	var $Txbase = 5;					//x轴两边距离
	var $Tybase =5 ;					//y轴两边距离
	var $TLine =true; 					//画干扰线


	public  function RandRSI() 			//生成验证码
	{
		$this->TFontAngle=range($this->TFontAngle[0],$this->TFontAngle[1]);
		$this->TFontSize=range($this->TFontSize[0],$this->TFontSize[1]);

		$arr=array();
		$Chars=$this->Chars;
		$TFontAngle=$this->TFontAngle;
		$TFontSize=$this->TFontSize;
		$FontColors=$this->FontColors;
		$code="";
		$font=dirname(__FILE__)."/font/".$this->TFonts[0];

		$charlen=strlen($Chars)-1;
		$anglelen=count($TFontAngle)-1; 									// 角度范围
		$fontsizelen=count($TFontSize)-1; 									// 字体范围
		$fontcolorlen=count($FontColors)-1; 								// 颜色范围

		for($i=0;$i<$this->Length;$i++) 									//得到字符与颜色
		{
			$char=$Chars[rand(0,$charlen)]; 								//得到字符
			$angle=$TFontAngle[rand(0,$anglelen)]; 							//旋转角度
			$fontsize=$TFontSize[rand(0,$fontsizelen)]; 					//字体大小
			$fontcolor=$FontColors[rand(0,$fontcolorlen)]; 					//字体颜色

			$bound=$this->_calculateTextBox($fontsize,$angle,$font,$char); 	//得到范围

			$arr[]=array($fontsize,$angle,$fontcolor,$char,$font,$bound);  	//得到矩形框
			$code.=$char;
		}
		$this->Code=$arr; 													//验证码
		return $code;
	}

	public function Draw() //画图
	{
		if(empty($this->Code)) $this->RandRSI();
		$codes=$this->Code; //用户验证码


		$wh=$this->_getImageWH($codes);

		$width=$wh[0];
		$height=$wh[1]; //高度

		$this->Width=$width;
		$this->Height=$height;

		$this->Image = imageCreate( $width, $height );
		$image=$this->Image;

		$back = $this->_getColor2($this->_getColor( $this->BgColor)); 	//背景颜色
		imageFilledRectangle($image, 0, 0, $width, $height, $back); 	//填充背景

		$TPadden=$this->TPadden;

		$basex=$this->Txbase;
		$color=null;
		foreach ($codes as $v) //逐个画字符
		{
			$bound=$v[5];
			$color=$this->_getColor2($this->_getColor($v[2]));
			imagettftext($image, $v[0], $v[1], $basex, $bound['height'],$color , $v[4], $v[3]);
			$basex=$basex+$bound['width']*$TPadden-$bound['left'];		//计算下一个左边距
		}
		$this->TLine?$this->_wirteSinLine($color,$basex):null; 			//画干扰线
		header("Content-type: image/png");
		imagepng( $image);
		imagedestroy($image);

	}

	/**
	 *通过字体角度得到字体矩形宽度*
	 *
	 * @param int $font_size 字体尺寸
	 * @param float $font_angle 旋转角度
	 * @param string $font_file 字体文件路径
	 * @param string $text 写入字符
	 * @return array 返回长宽高
	 */
	private function _calculateTextBox($font_size, $font_angle, $font_file, $text) {
		$box = imagettfbbox($font_size, $font_angle, $font_file, $text);

		$min_x = min(array($box[0], $box[2], $box[4], $box[6]));
		$max_x = max(array($box[0], $box[2], $box[4], $box[6]));
		$min_y = min(array($box[1], $box[3], $box[5], $box[7]));
		$max_y = max(array($box[1], $box[3], $box[5], $box[7]));

		return array(
		'left' => ($min_x >= -1) ? -abs($min_x + 1) : abs($min_x + 2),
		'top' => abs($min_y),
		'width' => $max_x - $min_x,
		'height' => $max_y - $min_y,
		'box' => $box
		);
	}

	private function  _getColor( $color ) //#ffffff
	{
		return array(hexdec($color[1].$color[2]),hexdec($color[3].$color[4]),hexdec($color[5].$color[6]));
	}

	private function  _getColor2( $color ) //#ffffff
	{
		return imagecolorallocate ($this->Image, $color[0], $color[1], $color[2]);
	}

	private function _getImageWH($data)
	{
		$TPadden=$this->TPadden;
		$w=$this->Txbase;
		$h=0;
		foreach ($data as $v)
		{
			$w=$w+$v[5]['width']*$TPadden-$v[5]['left'];
			$h=$h>$v[5]['height']?$h:$v[5]['height'];
		}
		return array(max($w,$this->Width),max($h,$this->Height));
	}

	//画正弦干扰线
	private function _wirteSinLine($color,$w)
	{
		$img=$this->Image;

		$h=$this->Height;
		$h1=rand(-5,5);
		$h2=rand(-1,1);
		$w2=rand(10,15);
		$h3=rand(4,6);

		for($i=-$w/2;$i<$w/2;$i=$i+0.1)
		{
			$y=$h/$h3*sin($i/$w2)+$h/2+$h1;
			imagesetpixel($img,$i+$w/2,$y,$color);
			$h2!=0?imagesetpixel($img,$i+$w/2,$y+$h2,$color):null;
		}
	}
}