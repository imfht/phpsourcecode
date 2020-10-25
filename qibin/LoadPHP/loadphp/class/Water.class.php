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
 * 图片水印类
 +------------------------------------------------------------------------------
 */
class Water {
    private $imgSrc;        //原始图片地址
    private $wImgSrc;       //水印图片地址
    private $imgName;       //图片名称
    private $im;            //图片资源
    private $wIm;           //水印图片资源
    private $bg;            //背景图片资源
    private $width;         //指定宽度
    private $path;          //指定存放目录
    
    public function __construct($width="",$path="") {
        $this->width = $width;
        $this->path = $path;
    }
    
    // +检查目录,当需要存盘图片时调用
    private function checkPath() {
        if(!file_exists($this->path) || !is_writable($this->path)) {
            if(!@mkdir($this->path)) {
                return false;
            }
        }
        $this->path = rtrim($this->path,"/")."/";
        return true;
    }
    
    // +创建图片信息,返回数组
    private function getInfo($imgSrc) {
        $info = getimagesize($imgSrc);
        return $info;
    }
    
    // +生成图片资源
    private function createImage($imgSrc) {
        $imageInfo = $this->getInfo($imgSrc);
        switch($imageInfo[2]) {
            case 1 : $im = imagecreatefromgif($imgSrc);break;
            case 2 : $im = imagecreatefromjpeg($imgSrc);break;
            case 3 : $im = imagecreatefrompng($imgSrc);break;
        }
        return $im;
    }
    
    // +创建模板
    private function createModel() {
        if(!empty($this->width)) {
            if(!is_numeric($this->width)) {
                return false;
            }
            $x = imagesx($this->im);
            $y = imagesy($this->im);
            for(;;) {
                if($this->width > $x) break;
                $x *= 0.9;
                $y *= 0.9;
            }
            $this->bg = imagecreatetruecolor($x,$y);
        }else {
              $this->bg = imagecreatetruecolor(imagesx($this->im),imagesy($this->im));
        }
        $bgColor = imagecolorallocate($this->bg,255,255,255);
        imagefill($this->bg,0,0,$bgColor);//填充背景颜色为白色
    }
    
    // +图片与模板合成
    private function setImage() {
        imagecopyresampled($this->bg,$this->im,0,0,0,0,imagesx($this->bg),imagesy($this->bg),imagesx($this->im),imagesy($this->im));
    }
    
    private function waterImg() {
        imagecopy($this->bg,$this->wIm,imagesx($this->bg)-imagesx($this->wIm),imagesy($this->bg)-imagesy($this->wIm),0,0,imagesx($this->wIm),imagesy($this->wIm));
    }
    
    // +得到文件名
    private function getImageName() {
        $this->imgName = basename($this->imgSrc);
    }
    
    // +输出图像
    private function headerImage() {
        if(imagetypes() & IMG_GIF) {
            header("Content-Type:image/gif");
            empty($this->path) ? imagegif($this->bg) : imagegif($this->bg,$this->path.$this->imgName);
        }else if(imagetypes() & IMG_JPG) {
            header("Content-Type:image/jpeg");
            empty($this->path) ? imagejpeg($this->bg) : imagejpeg($this->bg,$this->path.$this->imgName);
        }else {
            header("Content-Type:image/png");
            empty($this->path) ? imagepng($this->bg) : imagepng($this->bg,$this->path.$this->imgName);
        }
    }
    
    public function waterImage($img,$wimg) {
        if(!empty($this->path)) {
            if(!$this->checkPath()) {
                return false;
            }
        }
        $this->imgSrc = $img;
        $this->wImgSrc = $wimg;
        //生成主图片资源
        $this->im = $this->createImage($this->imgSrc);
        //生成水印图片资源
        $this->wIm = $this->createImage($this->wImgSrc);
        //创建模板
        $this->createModel();
        //主图与背景合成
        $this->setImage();
        //添加水印
        $this->waterImg();
        //获取文件名
        $this->getImageName();
        //输出或存盘
        $this->headerImage();
    }
    
    // +析构方法
    public function __destruct() {
        imagedestroy($this->im);
        imagedestroy($this->wIm);
        imagedestroy($this->bg);
    }
}

/*
****************HOW TO USE**********************
 使用范例1(图片存盘，指定图片宽度不能超过200px) :
    require("water.class.php");
    $water = new Water(200,"./images/");//200指定缩放大小，./images/指定存放目录
    $water->waterImage("1.gif","water.png");//1.gif需要添加水印的图片，water.png水印图片


使用范例2(显示，图片按原先大小) :
    require("water.class.php");
    $water = new Water();
    $water->waterImage("1.gif","water.png");//1.gif需要添加水印的图片，water.png水印图片
*/

?>