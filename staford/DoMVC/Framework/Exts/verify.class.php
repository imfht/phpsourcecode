<?php
/**
 * @author 暮雨秋晨
 * @copyright 2014
 */
session_start(); //为了将验证码的值保留在$_SESSION中
class Verify
{
    private $width;
    private $height;
    private $codeNum; //验证码的个数
    private $image; //图像资源
    private $checkCode; //验证码字符串

    function __construct($width = 60, $height = 20, $codeNum = 4)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codeNum = $codeNum;
        $this->checkCode = $this->createCheckCode();
    }
    //通过调用该方法向浏览器输出验证码图像
    function showImage()
    {
        $this->createImage(); //第一步：创建背景图像
        $this->setDisturbColor(); //第二步：设置干扰元素，此处只加了干扰直线
        $this->outputText(); //第三步：输出验证码
        $this->outputImage(); //第四步：输出图像
    }
    //通过调用该方法获取随机创建的验证码字符串
    function getCheckCode()
    {
        return $this->checkCode;
    }
    //创建背景图像
    private function createImage()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        //随机背景色
        $backColor = imagecolorallocate($this->image, rand(225, 255), rand(225, 255),
            rand(225, 255));
        //为背景填充颜色
        imagefill($this->image, 0, 0, $backColor);
        //设置边框颜色
        $border = imagecolorallocate($this->image, 0, 0, 0);
        //画出矩形边框
        imagerectangle($this->image, 0, 0, $this->width - 1, $this->height - 1, $border);
    }
    //输出干扰元素
    private function setDisturbColor()
    {
        $lineNum = rand(2, 4); //设置干扰线数量
        for ($i = 0; $i < $lineNum; $i++) {
            $x1 = rand(0, $this->width / 2);
            $y1 = rand(0, $this->height / 2);
            $x2 = rand($this->width / 2, $this->width);
            $y2 = rand($this->height / 2, $this->height);
            $color = imagecolorallocate($this->image, rand(100, 200), rand(100, 200), rand(100,
                200)); //颜色设置比背景深，比文字浅
            imageline($this->image, $x1, $y1, $x2, $y2, $color);
        }
    }
    //生成验证码字符串
    private function createCheckCode()
    { //或者这里可以通过前台传递过来的参数生成字符
        $code = "abdefghijklmnpqrtuwyzABDEFGHIJKLMNPQRTUWYZ";
        $string = "";
        for ($i = 0; $i < $this->codeNum; $i++) {
            $char = $code{rand(0, strlen($code) - 1)};
            $string .= $char;
        }
        return $string;
    }
    //输出验证码
    private function outputText()
    {
        //echo "<script type='text/javascript'>alert('".$this->checkCode."')</script>";
        $string = $this->checkCode;
        for ($i = 0; $i < $this->codeNum; $i++) {
            $x = rand(1, 4) + $this->width * $i / $this->codeNum;
            $y = rand(1, $this->height / 4);
            $color = imagecolorallocate($this->image, rand(0, 128), rand(0, 128), rand(0,
                128));
            $fontSize = rand(4, 5);
            imagestring($this->image, $fontSize, $x, $y, $string[$i], $color);
        }
    }
    //输出图像
    private function outputImage()
    {
        if (imagetypes() & IMG_GIF) {
            header("Content-type:image/gif");
            imagepng($this->image);
        } else
            if (imagetypes() & IMG_JPG) {
                header("Content-type:image/jpeg");
                imagepng($this->image);
            } else
                if (imagetypes() & IMG_PNG) {
                    header("Content-type:image/png");
                    imagepng($this->image);
                } else
                    if (imagetypes() & IMG_WBMP) {
                        header("Content-type:image/vnd.wap.wbmp");
                        imagepng($this->image);
                    } else {
                        die("PHP不支持图像创建");
                    }
    }
    function __destruct()
    {
        imagedestroy($this->image);
    }
}
?>