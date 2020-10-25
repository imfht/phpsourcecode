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
 * 绘制椭圆弧
 * Class EllipticArc
 * @package Tang\GD\Draws
 */
class EllipticArc extends Ellipse implements IDraw
{
    protected $start = 0;
    protected $end = 0;
    protected $style = IMG_ARC_PIE;

    /**
     * @param $x 圆的中心X坐标
     * @param $y 圆的中心Y坐标
     * @param $width 圆宽
     * @param $height 圆高
     * @param Color $color 颜色
     * @param int $start 开始画的开始角度。默认为0度。0度默认从3点顺时针方向开始画起
     * @param int $end 结束角度
     */
    public function __construct($x,$y,$width,$height,Color $color,$start=0,$end=0)
    {
        parent::__construct($x,$y,$width,$height,$color);
        $this->setStart($start)->setEnd($end);
    }

    /**
     * 设置开始画的开始角度
     * @param $start
     * @return $this
     */
    public function setStart($start)
    {
        $this->start = $this->getIntValue($start);
        return $this;
    }

    /**
     * 设置结束角度
     * @param $end
     * @return $this
     */
    public function setEnd($end)
    {
        $this->end = $this->getIntValue($end);
        return $this;
    }

    /**
     * 设置填充颜色
     * @param Color $color
     * @param int $Style
     * @return $this
     */
    public function setFillColor(Color $color = null,$Style=IMG_ARC_PIE)
    {
        $this->fillColor = $color;
        $this->style = $Style;
        return $this;
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource()->resource;
        if($this->fillColor)
        {
            imagefilledarc($resource,$this->x,$this->y,$this->width,$this->height,$this->start,$this->end,$this->fillColor->getColorAllocate($resource),$this->style);
            return;
        }
        imagearc($resource,$this->x,$this->y,$this->width,$this->height,$this->start,$this->end,$this->color->getColorAllocate($resource));
    }
}