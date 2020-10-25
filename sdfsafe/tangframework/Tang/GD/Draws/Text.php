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
namespace Tang\GD\Draws;
use Tang\GD\Color;
use Tang\GD\IDraw;
use Tang\GD\Image;
use Tang\Services\ConfigService;

/**
 * 绘制文字
 * Class Text
 * @package Tang\GD\Draws
 */
class Text extends Base implements IDraw
{
    protected $shadow;
    protected $text;
    protected $fontSize;
    protected $fontPath;
    protected $angle;
    public function __construct($x,$y,$text,Color $color,$width=0,$fontSize=12,$fontPath='',$angle=0)
    {
        $this->setXY($x,$y)->setColor($color)->setWidth($width)->setFontSize($fontSize)->setFontPath($fontPath)->setAngle($angle);
    }
    public function setFontSize($size)
    {
        $this->fontSize = $this->getIntValue($size,10);
        return $this;
    }
    public function setFontPath($fontPath = '')
    {
        if(!$fontPath || !file_exists($fontPath))
        {
            $fontPath =  stream_resolve_include_path(__DIR__.'/../Font1.ttf');
        }
        $this->fontPath = $fontPath;
        return $this;
}
public function setAngle($angle)
{
    $this->angle = $angle;
    return $this;
}
public function setShadow($px=2,Color $color = null)
{
    $this->shadow = $px == true;
    if($color)
    {
        $this->backgroundColor = $color;
    }
    $this->height = $this->getIntValue($px);
    return $this;
}
public function setText($text)
{
    $this->text = $text;
    return $this;
}
public function draw(Image $image)
{
    $newText = '';
    $charset = ConfigService::getService()->get('charset');
    $resource = $image->getResource()->resource;
    if($this->width > 0)
    {
        $textLength = mb_strlen($this->text,$charset);
        $stringLength = 0;
        $string = $char = '';
        for($i = 0;$i < $textLength;$i++)
        {
            $string .= $char = mb_substr($this->text, $i,1,$charset);
            $box = imagettfbbox($this->fontSize,$this->angle,$this->fontPath,$string);
            $stringLength = $box[2] - $box[0];
            $newText .= $char;
            if($stringLength >= $this->width)
            {
                $newText .= "\n";
                $string = '';
            }
        }
    } else
    {
        $newText = $this->text;
    }
    $box = imagettfbbox($this->fontSize,$this->angle,$this->fontPath,$newText);
    if($this->shadow)
    {
        imagettftext($resource,$this->fontSize,$this->angle,$this->x + $this->height,$this->y + $this->height,$this->backgroundColor->getColorAllocate($resource),$this->fontPath,$newText);
    }
    imagettftext($resource,$this->fontSize,$this->angle,$this->x,$this->y,$this->color->getColorAllocate($resource),$this->fontPath,$newText);
    $this->y = $this->y + $box[3] - $box[5];
    return $box;
}
}