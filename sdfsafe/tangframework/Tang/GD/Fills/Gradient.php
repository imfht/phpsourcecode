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
namespace Tang\GD\Fills;
use Tang\GD\Color;
use Tang\GD\IDraw;
use Tang\GD\Image;

/**
 * 渐变填充
 * Class Gradient
 * @package Tang\GD\Fills
 */
class Gradient implements  IDraw
{
    const HORIZONTAL = 1;
    const VERTICAL=2;
    const ELLIPSE=3;
    const ELLIPSE2=4;
    const CIRCLE=5;
    const CIRCLE2=6;
    const SQUARE=7;
    const RECTANGLE=8;
    const DIAMOND=9;
    protected $type;
    protected $startColor;
    protected $endColor;
    protected $step = 2;
    protected static function getColor1(Color $startColor,Color $endColor,Color $newStartColor,Color $newEndColor)
    {
        $newStartColor = clone $startColor;
        $newEndColor = clone $endColor;
    }
    protected static function getColor2(Color $startColor,Color $endColor,Color $newStartColor,Color $newEndColor)
    {
        $newStartColor = clone $endColor;
        $newEndColor = clone $startColor;
    }
    public function __construct($type,$step,Color $startColor,Color $endColor)
    {
        $this->setColor($startColor,$endColor)->setType($type)->setStep($step);
    }
    /**
     * 设置步长
     * @param $step
     * @return $this
     */
    public function setStep($step)
    {
        $step = (int)$step;
        if($step > 0)
        {
            $this->step = $step;
        }
        return $this;
    }

    /**
     * 设置渐变类型
     * @param $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * 设置渐变颜色
     * @param Color $startColor
     * @param Color $endColor
     * @return $this
     */
    public function setColor(Color $startColor,Color $endColor)
    {
        $this->startColor = $startColor;
        $this->endColor = $endColor;
        return $this;
    }
    public function draw(Image $image)
    {
        $step = $this->step;
        $resource = $image->getResource();
        $width = $resource->getWidth();
        $height = $resource->getHeight();
        $xCenter = $width / 2;
        $yCenter = $height / 2;
        $lineNumbers = 0;
        $lineWidth = 0;
        $rHeight = $rWidth = 0;
        $newStartColor = clone $this->startColor;
        $newEndColor = clone $this->endColor;
        switch($this->type)
        {
            case Gradient::VERTICAL:
                $lineNumbers = $height;
                $lineWidth = $width;
                static::getColor1($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                break;
            case Gradient::ELLIPSE:
                $rHeight = $height>$width?1:$width/$height;
                $rWidth = $width>$height?1:$height/$width;
                $lineNumbers = min($width,$height);
                static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                imagefill($resource->resource, 0, 0, imagecolorallocate($resource->resource, $newStartColor->getRed(), $newStartColor->getGreen(), $newStartColor->getBlue()));
                break;
            case Gradient::ELLIPSE2:
                $rHeight = $height>$width?1:$width/$height;
                $rWidth = $width>$height?1:$height/$width;
                $lineNumbers = sqrt(pow($width,2)+pow($height,2));
                static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                break;
            case Gradient::CIRCLE:
                $lineNumbers = sqrt(pow($width,2)+pow($height,2));
                $rHeight = $rWidth = 1;
                static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                break;
            case Gradient::CIRCLE2:
                $lineNumbers = min($width,$height);
                $rHeight = $rWidth = 1;
                static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                imagefill($resource->resource, 0, 0, imagecolorallocate($resource->resource,$newStartColor->getRed(), $newStartColor->getGreen(), $newStartColor->getBlue()));
                break;
            case Gradient::SQUARE:
            case Gradient::RECTANGLE:
                $lineNumbers = max($width,$height)/2;
            static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                break;
            case Gradient::DIAMOND:
                static::getColor2($this->startColor,$this->endColor,$newStartColor,$newEndColor);
                $rHeight = $height>$width?1:$width/$height;
                $rHeight = $width>$height?1:$height/$width;
                $lineNumbers = min($width,$height);
                break;
            default:
                $this->type = Gradient::HORIZONTAL;
                $lineNumbers = $width;
                $lineWidth = $height;
                static::getColor1($this->startColor,$this->endColor,$newStartColor,$newEndColor);
        }
        $drawCall = null;
        switch ($this->type)
        {
            case Gradient::VERTICAL:
                $drawCall = function($resource,$i,$fillColor) use ($step,$lineWidth)
                {
                    imagefilledrectangle($resource,0,$i, $lineWidth,$i+$step,$fillColor);
                };
                break;
            case Gradient::HORIZONTAL:
                $drawCall = function($resource,$i,$fillColor) use ($step,$lineWidth)
                {
                    imagefilledrectangle($resource, $i, 0, $i+$step,$lineWidth, $fillColor);
                };
                break;
            case Gradient::ELLIPSE:
            case Gradient::ELLIPSE2:
            case Gradient::CIRCLE:
            case Gradient::CIRCLE2:
            $drawCall = function($resource,$i,$fillColor) use ($step,$xCenter,$yCenter,$lineNumbers,$rWidth,$rHeight)
                {
                    $i = $lineNumbers-$i;
                    imagefilledellipse($resource,$xCenter, $yCenter, $i*$rHeight,$i*$rWidth,$fillColor);
                };
                break;
            case Gradient::SQUARE:
            case Gradient::RECTANGLE:
                 $drawCall = function($resource,$i,$fillColor) use ($width,$height)
                {
                    $x1 = $i*$width/$height;
                    $y1 = $i*$height/$width;
                    imagefilledrectangle($resource,$x1,$y1,$width-$x1, $height-$y1,$fillColor);
                };
                break;
            case Gradient::DIAMOND:
                $drawCall = function($resource,$i,$fillColor) use ($width,$height,$rWidth,$rHeight)
                {
                    imagefilledpolygon($resource,array(
                        $width/2, $i*$rWidth-0.5*$height,
                        $i*$rHeight-0.5*$width, $height/2,
                        $width/2,1.5*$height-$i*$rWidth,
                        1.5*$width-$i*$rHeight, $height/2), 4, $fillColor);
                };
                break;
        }
        $oldRed = $oldGreen = $oldBlue = $red = $green = $blue = 0;
        $fillColor = null;
        $red1 = $newStartColor->getRed();
        $red2 = $newEndColor->getRed();
        $green1 = $newStartColor->getRed();
        $green2 = $newEndColor->getRed();
        $blue1 = $newStartColor->getBlue();
        $blue2 = $newEndColor->getBlue();
        for ($i = 0; $i < $lineNumbers;$i=$i+1+$step)
        {
            $oldRed = $red;
            $oldGreen = $green;
            $oldBlue = $blue;
            $red = $red2 - $red1;
            $green = $green2 - $green1;
            $blue = $blue2 - $blue1;
            $i2 = $i / $lineNumbers;
            $red = ($red != 0) ? intval($red1 + $red * $i2): $red1;
            $green = ($green != 0) ? intval($green1 + $green * $i2):$green1;
            $blue = ($blue != 0) ? intval($blue1 + $blue * $i2): $blue1;
            if ("$oldRed,$oldGreen,$oldBlue" != "$red,$green,$blue")
            {
                $fillColor = imagecolorallocate($resource->resource, $red,$green,$blue);
            }
            $drawCall($resource->resource,$i,$fillColor);
        }
    }
}