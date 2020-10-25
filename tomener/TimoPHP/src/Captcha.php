<?php
/**
 * TimoPHP a Fast Simple Smart PHP FrameWork
 * Author: Tommy 863758705@qq.com
 * Link: http://www.TimoPHP.com/
 * Since: 2016
 */

namespace Timo;

/**
 * 验证码类
 *
 * Class Captcha
 * @package Timo
 */
class Captcha
{
    /**
     * 随机因子
     *
     * @var string
     */
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';

    //验证码
    private $code;

    //验证码长度
    private $codeLen;

    //宽度
    private $width;

    //高度
    private $height;

    //图形资源句柄
    private $img;

    //指定的字体
    private $font;

    //指定字体大小
    private $fontSize;

    //指定字体颜色
    private $fontColor;

    /**
     * 构造方法初始化
     *
     * @param int $width
     * @param int $height
     * @param int $codeLen
     * @param int $fontSize
     */
    public function __construct($width = 130, $height = 50, $codeLen = 4, $fontSize = 20)
    {
        $this->width = $width;
        $this->height = $height;
        $this->codeLen = $codeLen;
        $this->fontSize = $fontSize;

        $this->createCode();

        $this->font = FRAME_PATH . 'source/font/Elephant.ttf';
    }

    /**
     * 生成随机码
     */
    private function createCode()
    {
        $_len = strlen($this->charset) - 1;
        for ($i = 0; $i < $this->codeLen; $i++) {
            $this->code .= $this->charset[mt_rand(0, $_len)];
        }
    }

    /**
     * 生成背景
     */
    private function createBg()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157, 255), mt_rand(157, 255), mt_rand(157, 255));
        imagefilledrectangle($this->img, 0, $this->height, $this->width, 0, $color);
    }

    /**
     * 生成文字
     */
    private function createFont()
    {
        $_x = ceil($this->width / $this->codeLen);
        $_y = floor($this->height * 0.75);
        for ($i = 0; $i < $this->codeLen; $i++) {
            $x = $_x * $i + mt_rand(1, 5);
            $y = mt_rand($_y - 10, $_y + 10);
            $angle = mt_rand(-30, 30);
            $this->fontColor = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imagettftext(
                $this->img,
                $this->fontSize,
                $angle,
                $x,
                $y,
                $this->fontColor,
                $this->font,
                $this->code[$i]
            );
        }
    }

    /**
     * 生成线条、雪花
     */
    private function createLine()
    {
        for ($i = 0; $i < 6; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(0, 156), mt_rand(0, 156), mt_rand(0, 156));
            imageline(
                $this->img,
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                $color
            );
        }
        for ($i = 0; $i < 100; $i++) {
            $color = imagecolorallocate($this->img, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
            imagestring(
                $this->img,
                mt_rand(1, 5),
                mt_rand(0, $this->width),
                mt_rand(0, $this->height),
                '*',
                $color
            );
        }
    }

    /**
     * 输出图片
     */
    private function outPut()
    {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }

    /**
     * 获取验证码图片
     */
    public function getImage()
    {
        $this->createBg();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }

    /**
     * 获取base64格式图片
     *
     * @return string
     */
    public function getBase64Image()
    {
        $this->createBg();
        $this->createLine();
        $this->createFont();
        ob_start();
        imagepng($this->img);
        $imgString = ob_get_clean();
        return base64_encode($imgString);
    }

    /**
     * 获取验证码
     *
     * @return string
     */
    public function getCode()
    {
        return strtolower($this->code);
    }
}
