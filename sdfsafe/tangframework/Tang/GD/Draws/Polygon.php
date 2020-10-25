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
 * 绘制多边形
 * Class Polygon
 * @package Tang\GD\Draws
 */
class Polygon extends Base implements IDraw
{
    protected $points;
    protected $pointsNum;
    public function __construct(array $points,$pointsNum,Color $color)
    {
        $this->setColor($color)->setPoints($points, $pointsNum);
    }
    public function setPoints(array $points,$pointsNum)
    {
        $this->points = $points;
        $this->pointsNum = (int)$pointsNum;
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource()->resource;
        if($this->fillColor)
        {
            imagefilledpolygon($resource,$this->points,$this->pointsNum,$this->fillColor->getColorAllocate($resource));
            return;
        }
        imagepolygon($resource,$this->points,$this->pointsNum,$this->color->getColorAllocate($resource));
    }
}