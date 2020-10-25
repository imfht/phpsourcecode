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

class Line extends Base implements IDraw
{
    protected $x2;
    protected $y2;
    protected $thick;

    /**
     * @param $x 起点X
     * @param $y 起点Y
     * @param $x2 终点X
     * @param $y2 终点Y
     * @param Color $color 颜色
     * @param int $thick 线宽
     */
    public function __construct($x, $y, $x2, $y2,Color $color, $thick = 1)
    {
        $this->setColor($color)->setXY($x,$y)->setXY2($x2,$y2)->setThick($thick);
    }

    /**
     * 设置终点XY
     * @param $x2
     * @param $y2
     * @return $this
     */
    public function setXY2($x2,$y2)
    {
        $this->x2 = $this->GetIntValue($x2);
        $this->y2 = $this->GetIntValue($y2);
        return $this;
    }

    /**
     * 设置线宽
     * @param $thick
     * @return $this
     */
    public function setThick($thick)
    {
        $this->thick = $this->getIntValue($thick,1);
        return $this;
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource()->resource;
        $color = $this->color->getColorAllocate($resource);
        if ($this->thick == 1)
        {
            imageline($resource,$this->x, $this->y,$this->x2,$this->y2,$color);
        } else
        {
            $thick = $this->thick / 2 - 0.5;
            if ($this->x == $this->x2  || $this->y == $this->y2)
            {
                imagefilledrectangle($resource, round(min($this->x, $this->x2) - $thick), round(min($this->y, $this->y2) - $thick), round(max($this->x, $this->x2) + $thick), round(max($this->y, $this->y2) + $thick),$color);
            } else
            {
                $k = ($this->y2 - $this->y) / ($this->x2 - $this->x);
                $a = $thick / sqrt(1 + pow($k, 2));
                $points = array(
                    round($this->x - (1+$k)*$a), round($this->y + (1-$k)*$a),
                    round($this->x - (1-$k)*$a), round($this->y - (1+$k)*$a),
                    round($this->x2 + (1+$k)*$a), round($this->y2 - (1-$k)*$a),
                    round($this->x2 + (1-$k)*$a), round($this->y2 + (1+$k)*$a),
                );
                imagefilledpolygon($resource, $points, 4,$color);
                imagepolygon($resource, $points, 4, $color);
            }
        }
        //更新新的坐标
        $this->x = $this->x2;
        $this->y = $this->y2;
    }
}