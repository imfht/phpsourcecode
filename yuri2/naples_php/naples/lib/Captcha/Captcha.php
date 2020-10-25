<?php
//验证码生成插件
namespace naples\lib\Captcha;

//验证码类
use naples\lib\base\Service;

class Captcha extends Service {
    private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';//随机因子
    private $code;//验证码
    private $codelen = 4;//验证码长度
    private $width = 130;//宽度
    private $height = 50;//高度
    private $img;//图形资源句柄
    private $font;//指定的字体
    private $fontsize = 20;//指定字体大小
    private $fontcolor;//指定字体颜色

    public function init(){
        $this->font = dirname(__FILE__).'/font.ttf';//注意字体路径要写对，否则显示不了图片
        $this->charset=$this->config('charset');
        $this->codelen=$this->config('codelen');
        $this->width=$this->config('width');
        $this->height=$this->config('height');
        $this->fontsize=$this->config('fontsize');
    }

    //设置尺寸
    public function setSize($width=0,$height=0){
        if ($width)
            $this->width=$width;
        if ($height)
            $this->height=$height;
        return $this;
    }

    //生成随机码
    private function createCode() {
        $_len = strlen($this->charset)-1;
        for ($i=0;$i<$this->codelen;$i++) {
            $this->code .= $this->charset[mt_rand(0,$_len)];
        }
    }
    //生成背景
    private function createBg() {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $color = imagecolorallocate($this->img, mt_rand(157,205), mt_rand(157,205), mt_rand(157,205));
        imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
    }
    //生成文字
    private function createFont() {
        $_x = $this->width / $this->codelen;
        for ($i=0;$i<$this->codelen;$i++) {
            $this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imagettftext($this->img,$this->fontsize,mt_rand(-30,30),$_x/5+$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
        }
    }
    //生成线条、雪花
    private function createLine() {
        //线条
        for ($i=0;$i<6;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
            imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
        }
        //雪花
        $_len = strlen($this->charset)-1;
        ;
        for ($i=0;$i<$this->width/2;$i++) {
            $color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
            imagestring($this->img,mt_rand(1,4),mt_rand(0,$this->width),mt_rand(0,$this->height),$this->charset[mt_rand(0,$_len)],$color);
        }
    }
    //输出
    private function outPut() {
        header('Content-type:image/png');
        imagepng($this->img);
        imagedestroy($this->img);
    }
    //对外生成
    public function doImg() {
        $this->fontsize=($this->width/$this->codelen)/2;
        $this->createBg();
        $this->createCode();
        $this->createLine();
        $this->createFont();
        $this->outPut();
    }
    //获取验证码
    public function getCode() {
        return strtolower($this->code);
    }
}