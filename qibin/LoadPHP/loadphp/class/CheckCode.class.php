<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

/**
 +------------------------------------------------------------------------------
 * 验证码类
 +------------------------------------------------------------------------------
 */
class CheckCode {
    private $width;         //宽度
    private $height;        //高度
    private $image;         //图像资源
    private $codeNum;       //字符数
    private $pixelNum;      //点数
    private $code;          //验证码
    
    public function __construct($width=80,$height=20,$codeNum=4) {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $this->code = $this->createCode();
        $number = floor(($this->height*$this->width)/15);
        if($number < 240-$this->codeNum) {
            $this->pixelNum = $number;
        }else {
            $this->pixelNum = 240-$this->codeNum;
        }
    }
    
    // +创建随机文本
    private function createCode() {
        $str = "23456789abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ";
        $string = '';
        for($i=0;$i<$this->codeNum;$i++) {
            $string .= $str{rand(0,strlen($str)-1)};
        }
        return $string;
    }
    
    // +返回文本内容，常在session中使用
    public function getCode() {
        return $this->code;
    }
    
    // +创建图像
    private function createImage() {
        //创建图像
        $this->image = imagecreatetruecolor($this->width,$this->height);
        //填充背景
        $bgColor = imagecolorallocate($this->image,rand(220,255),rand(220,255),rand(220,255));
        imagefill($this->image,0,0,$bgColor);
        //画边框
        $borderColor = imagecolorallocate($this->image,0,0,0);
        imagerectangle($this->image,0,0,$this->width-1,$this->height-1,$borderColor);
    }
    
    // +设置干扰项
    private function setDisturb() {
        //设置干扰点
        for($i=0;$i<$this->pixelNum;$i++) {
            $pixelColor = imagecolorallocate($this->image,rand(100,255),rand(100,255),rand(100,255));
            imagesetpixel($this->image,rand(1,$this->width-2),rand(1,$this->height-2),$pixelColor);
        }
        //设置干扰弧线
        for($i=0;$i<10;$i++) {
            $lineColor = imagecolorallocate($this->image,rand(100,255),rand(100,255),rand(100,255));
            imagearc($this->image,rand(-10,$this->width),rand(-10,$this->height),rand(30,300),rand(20,200),55,44,$lineColor);
        }
    }
    
    // +画文本
    private function setText() {
        for($i=0;$i<$this->codeNum;$i++) {
            $fontColor = imagecolorallocate($this->image,rand(0,128),rand(0,128),rand(0,128));
            $fontsize = rand(3,5);
            $x = floor($this->width/$this->codeNum)*$i+3;
            $y = rand(0,$this->height-15);
            imagechar($this->image,$fontsize,$x,$y,$this->code{$i},$fontColor);
        }
    }
    
    // +输出图像
    private function headerImage() {
        if(imagetypes() & IMG_GIF) {
            header("Content-Type:image/gif");
            imagegif($this->image);
        }else if(imagetypes() & IMG_JPG) {
            header("Content-Type:image/jpeg");
            imagejpeg($this->image);
        }else if(imagetypes() & IMG_PNG) {
            header("Content-Type:image/png");
            imagepng($this->image);
        }else return false;
    }
    
    // +通过调用此方法显示验证码
    public function showCode() {
        $this->createImage();
        $this->setDisturb();
        $this->setText();
        $this->headerImage();
    }
    
    public function __destruct() {
        imagedestoy($this->image);
    }
}
?>