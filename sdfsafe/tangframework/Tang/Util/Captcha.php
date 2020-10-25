<?php
// +-----------------------------------------------------------------------------------
// | TangFrameWork 致力于WEB快速解决方案
// +-----------------------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.tangframework.com All rights reserved.
// +-----------------------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +-----------------------------------------------------------------------------------
// | HomePage ( http://www.tangframework.com/ )
// +-----------------------------------------------------------------------------------
// | Author: wujibing<283109896@qq.com>
// +-----------------------------------------------------------------------------------
// | Version: 1.0
// +-----------------------------------------------------------------------------------
namespace Tang\Util;
use Tang\GD\Color;
use Tang\GD\Draws\Line;
use Tang\GD\Draws\Text;
use Tang\GD\Effects\Thumb;
use Tang\GD\Effects\Wave;
use Tang\GD\Fills\Background;
use Tang\GD\Fills\Gradient;
use Tang\GD\Image;
use Tang\GD\Resource;
use Tang\Web\Session\SessionService;

/**
 * 验证码类
 * @package Tang\Util
 */
class Captcha
{
    private static $enChars = 'QWERTYUIOPASDFGHJKLZXCVBNM';
    /**
     * 宽度
     * @var int
     */
    protected $width = 100;
    /**
     * 高度
     * @var int
     */
    protected $height = 40;
    /**
     * 设置验证码的字符数量
     * @var int
     */
    protected $codeLength = 4;
    protected $fontPath = '';
    protected $isGradientBackground = true;
    protected $backgroundColor = null;
    public function __construct($width=100,$height=40,$codeLength=4,$fontPath='')
    {
        $this->setWidth($width);
        $this->setHeight($height);
        $this->setCodeLength($codeLength);
        $this->setFontPath($fontPath);
    }
    public function setBackgroundColor(Color $color)
    {
        $this->backgroundColor = $color;
    }
    /**
     * 设置宽度
     * @param int $width
     */
    public function setWidth($width=100)
    {
        $this->width = $this->getInt($width,100);
    }
    public function setIsGradientBackground($bool)
    {
        $this->isGradientBackground = $bool;
    }
    /**
     * 设置高度
     * @param int $height
     */
    public function setHeight($height=40)
    {
        $this->height = $this->getInt($height,40);
    }

    /**
     * 设置字符数量
     * @param int $length
     */
    public function setCodeLength($length = 4)
    {
        $this->codeLength = $this->getInt($length,4);
    }
    public function create($id)
    {
        $scale = 4;
        //创建图片资源
        $width = $this->width*$scale;
        $height = $this->height*$scale;
        $imageResource = Resource::createByTrueColor($width,$height,'png');
        $image = new Image($imageResource);
        $textColor = null;
        if($this->isGradientBackground)
        {
            $startColor = $this->makeRandColor(255);
            $textColor = $startColor->getInverseColor();
            $image->draw(new Gradient(mt_rand(1, 9),2,$startColor,$this->makeRandColor(100)));
        } else
        {
            $bgColor = $this->backgroundColor?$this->backgroundColor:$this->makeRandColor();
            $image->draw(new Background($bgColor));//创建随机颜色的背景
            $textColor = $bgColor->getInverseColor();
        }

        $captchaCode = '';
        $charLength = strlen(static::$enChars) - 1;
        $char = '';
        $length = $this->codeLength + 1;
        $fontSize = $this->width / $length * $scale + 4;
        $x = mt_rand(1,$fontSize);
        $y = round(($this->height*32/40)*$scale);
        $x1 = $x -5;
        $y1= mt_rand($this->height/2, $this->height);
        $shadowColor = $this->makeRandColor();
        $text = new Text(0,0,'',$textColor,0,$fontSize,$this->fontPath);
        $text->setShadow(1,$shadowColor);
        $lineY = $y-mt_rand(0,$fontSize);
        $lineSize = $fontSize / 10;
        for ($i=0;$i<$this->codeLength;$i++)
        {
            $captchaCode .= $char = static::$enChars[mt_rand(0, $charLength)];
            $text->setText($char)->setXY($x, $y);
            $box = $image->draw($text);
            //$image->draw(new Line($x-mt_rand(1,20),$lineY,$x+$fontSize,$lineY,$lineColor,$lineSize));
            //$image->draw(new Line($x-mt_rand(1,20),$box[4],$x+$fontSize,$y,$lineColor,$lineSize));
            $x += $box[2];
        }
        SessionService::getService()->set('Captcha'.$id,$captchaCode);
        $image->draw(new Wave(mt_rand(20,40),mt_rand(20,50)));
        $image->draw(new Thumb($this->width,$this->height));
        $image->browseImage();
    }

    /**
     * 检验验证码
     * @param $id
     * @param $code
     * @return bool
     */
    public static function checkCode($id,$code)
    {
        $sessionCode = SessionService::getService()->get('Captcha'.$id);
        if(!$sessionCode || $sessionCode != strtoupper($code))
        {
            return false;
        } else
        {
            return true;
        }
    }
    /**
     * 创建随机颜色
     * @param int $max
     * @return Color
     */
    private function makeRandColor($max = 255)
    {
        $max = (int)$max;
        if($max < 1 || $max > 255)
        {
            $max = 255;
        }
        return Color::createByRgb(mt_rand(0,$max),mt_rand(0,$max),mt_rand(0,$max));
    }
    /**
     * 设置字体路径
     * @param $fontPath
     */
    public function setFontPath($fontPath)
    {
        $this->fontPath = $fontPath;
    }
    protected function getInt($value,$default)
    {
        $value = (int)$value;
        return $value > 0 ? $value : $default;
    }
}