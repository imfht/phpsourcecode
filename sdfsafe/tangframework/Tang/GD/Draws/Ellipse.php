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
 * 绘制椭圆
 * Class Ellipse
 * @package Tang\GD\Draws
 */
class Ellipse extends Base implements IDraw
{
    public function __construct($x,$y,$width,$height,Color $color)
    {
        $this->setXY($x, $y)->setColor($color)->setWidth($width)->setHeight($height);
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource()->resource;
        if($this->fillColor)
        {
            imagefilledellipse($resource,$this->x,$this->y,$this->width,$this->height,$this->fillColor->getColorAllocate($resource));
            return;
        }
        imageellipse($resource,$this->x,$this->y,$this->width,$this->height,$this->color->getColorAllocate($resource));
    }
}