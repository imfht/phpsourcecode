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

/**
 * 绘制矩形
 * Class Rectangle
 * @package Tang\GD\Draws
 */
class Rectangle extends Base implements IDraw
{
    /**
     * @param $x 起点 X
     * @param $y 起点Y
     * @param $width 宽度
     * @param $height 高度
     * @param Color $color 颜色
     */
    public function __construct($x,$y,$width,$height,Color $color)
    {
        $this->setColor($color)->setXY($x,$y)->setWidth($width)->setHeight($height);
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource()->resource;
        $x2 = $this->x + $this->width;
        $y2 = $this->y + $this->height;
        if($this->fillColor)
        {
            imagefilledrectangle($resource,$this->x,$this->y,$x2,$y2,$this->fillColor->getColorAllocate($resource));
            return;
        }
        imagerectangle($resource,$this->x,$this->y,$x2,$y2,$this->color->getColorAllocate($resource));
    }
}