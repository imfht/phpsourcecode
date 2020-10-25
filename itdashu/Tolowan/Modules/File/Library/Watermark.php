<?php
namespace Modules\File\Library;
/**
 * 加水印类，支持文字图片水印的透明度设置、水印图片背景透明。
 * 日期：2011-09-27
 * 作者：www.itwhy.org
 * 使用：
 *      $obj = new WaterMask($imgFileName);         //实例化对象
 *      $obj->$waterType = 1;                        //类型：0为文字水印、1为图片水印
 *      $obj->$transparent = 45;                 //水印透明度
 *      $obj->$waterStr = 'www.itwhy.org';           //水印文字
 *      $obj->$fontSize = 16;                        //文字字体大小
 *      $obj->$fontColor = array(255,0255);          //水印文字颜色（RGB）
 *      $obj->$fontFile = = 'AHGBold.ttf';           //字体文件
 *      $obj->output();                              //输出水印图片文件覆盖到输入的图片文件
 */
class Watermark
{
    public $waterType = 1;                //水印类型：0为文字水印、1为图片水印
    public $pos = 0;                //水印位置
    public $transparent = 45;               //水印透明度

    public $waterStr = 'aaaaaaaaaaaaaa';  //水印文字
    public $fontSize = 16;               //文字字体大小
    public $fontColor = array(255, 0, 255); //水印文字颜色（RGB）
    public $fontFile = 'AHGBold.ttf';    //字体文件

    public $waterImg = MODULES_DIR.'File/images/watermark.png';       //水印图片

    private $srcImg = '';               //需要添加水印的图片
    private $im = '';               //图片句柄
    private $water_im = '';               //水印图片句柄
    private $srcImg_info = '';               //图片信息
    private $waterImg_info = '';               //水印图片信息
    private $str_w = '';               //水印文字宽度
    private $str_h = '';               //水印文字高度
    private $x = '';               //水印X坐标
    private $y = '';               //水印y坐标

    function __construct($img)
    {        //析构函数
        $this->srcImg = file_exists($img) ? $img : die('"' . $img . '" 源文件不存在！');
    }

    private function imginfo()
    {                //获取需要添加水印的图片的信息，并载入图片。
        $this->srcImg_info = getimagesize($this->srcImg);
        switch ($this->srcImg_info[2]) {
            case 3:
                $this->im = imagecreatefrompng($this->srcImg);
                break 1;
            case 2:
                $this->im = imagecreatefromjpeg($this->srcImg);
                break 1;
            case 1:
                $this->im = imagecreatefromgif($this->srcImg);
                break 1;
            default:
                die('原图片（' . $this->srcImg . '）格式不对，只支持PNG、JPEG、GIF。');
        }
    }

    private function waterimginfo()
    {           //获取水印图片的信息，并载入图片。
        $this->waterImg_info = getimagesize($this->waterImg);
        switch ($this->waterImg_info[2]) {
            case 3:
                $this->water_im = imagecreatefrompng($this->waterImg);
                break 1;
            case 2:
                $this->water_im = imagecreatefromjpeg($this->waterImg);
                break 1;
            case 1:
                $this->water_im = imagecreatefromgif($this->waterImg);
                break 1;
            default:
                die('水印图片（' . $this->srcImg . '）格式不对，只支持PNG、JPEG、GIF。');
        }
    }

    private function waterpos()
    {               //水印位置算法
        switch ($this->pos) {
            case 0:     //随机位置
                $this->x = rand(0, $this->srcImg_info[0] - $this->waterImg_info[0]);
                $this->y = rand(0, $this->srcImg_info[1] - $this->waterImg_info[1]);
                break 1;
            case 1:     //上左
                $this->x = 0;
                $this->y = 0;
                break 1;
            case 2:     //上中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = 0;
                break 1;
            case 3:     //上右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = 0;
                break 1;
            case 4:     //中左
                $this->x = 0;
                $this->y = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 5:     //中中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 6:     //中右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = ($this->srcImg_info[1] - $this->waterImg_info[1]) / 2;
                break 1;
            case 7:     //下左
                $this->x = 0;
                $this->y = $this->srcImg_info[1] - $this->waterImg_info[1];
                break 1;
            case 8:     //下中
                $this->x = ($this->srcImg_info[0] - $this->waterImg_info[0]) / 2;
                $this->y = $this->srcImg_info[1] - $this->waterImg_info[1];
                break 1;
            default:    //下右
                $this->x = $this->srcImg_info[0] - $this->waterImg_info[0];
                $this->y = $this->srcImg_info[1] - $this->waterImg_info[1];
                break 1;
        }
    }

    private function waterimg()
    {
        if ($this->srcImg_info[0] <= $this->waterImg_info[0] || $this->srcImg_info[1] <= $this->waterImg_info[1]) {
            die('水印比原图大！');
        }
        $this->waterpos();
        $cut = imagecreatetruecolor($this->waterImg_info[0], $this->waterImg_info[1]);
        imagecopy($cut, $this->im, 0, 0, $this->x, $this->y, $this->waterImg_info[0], $this->waterImg_info[1]);
        $pct = $this->transparent;
        imagecopy($cut, $this->water_im, 0, 0, 0, 0, $this->waterImg_info[0], $this->waterImg_info[1]);
        imagecopymerge($this->im, $cut, $this->x, $this->y, 0, 0, $this->waterImg_info[0], $this->waterImg_info[1], $pct);
    }

    private function waterstr()
    {
        $rect = imagettfbbox($this->fontSize, 0, $this->fontFile, $this->waterStr);
        $w = abs($rect[2] - $rect[6]);
        $h = abs($rect[3] - $rect[7]);
        $fontHeight = $this->fontSize;
        $this->water_im = imagecreatetruecolor($w, $h);
        imagealphablending($this->water_im, false);
        imagesavealpha($this->water_im, true);
        $white_alpha = imagecolorallocatealpha($this->water_im, 255, 255, 255, 127);
        imagefill($this->water_im, 0, 0, $white_alpha);
        $color = imagecolorallocate($this->water_im, $this->fontColor[0], $this->fontColor[1], $this->fontColor[2]);
        imagettftext($this->water_im, $this->fontSize, 0, 0, $this->fontSize, $color, $this->fontFile, $this->waterStr);
        $this->waterImg_info = array(0 => $w, 1 => $h);
        $this->waterimg();
    }

    function output()
    {
        $this->imginfo();
        if ($this->waterType == 0) {
            $this->waterstr();
        } else {
            $this->waterimginfo();
            $this->waterimg();
        }
        switch ($this->srcImg_info[2]) {
            case 3:
                imagepng($this->im, $this->srcImg);
                break 1;
            case 2:
                imagejpeg($this->im, $this->srcImg);
                break 1;
            case 1:
                imagegif($this->im, $this->srcImg);
                break 1;
            default:
                die('添加水印失败！');
                break;
        }
        imagedestroy($this->im);
        imagedestroy($this->water_im);
    }
}

?>