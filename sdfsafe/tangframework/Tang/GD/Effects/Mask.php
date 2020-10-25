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
namespace Tang\Gd\Effects;
use Tang\GD\Draws\Base;
use Tang\GD\IDraw;
use Tang\GD\Image;

/**
 * 马赛克效果
 * Class Mask
 * @package Tang\Gd\Effects
 */
class Mask extends Base implements IDraw
{
    protected $step = 2;
    /**
     *
     * @param int $x 开始X
     * @param int $y 开始Y
     * @param int $width 宽度
     * @param int $height 长度
     * @param int $step 马赛克一格大小
     */
    public function __construct($x,$y,$width,$height,$step = 2)
    {
        $this->setXY($x,$y)->setWidth($width)->setHeight($height)->setStep($step);
    }

    /**
     * 设置马赛克一格大小
     * @param $step
     * @return $this
     */
    public function setStep($step)
    {
        $this->step = $this->getIntValue($step,2);
        return $this;
    }
    public function draw(Image $image)
    {
        if($this->width == 0 || $this->height == 0)
        {
            return;
        }
        $x = $this->x;
        $y = $this->y;
        $x2 = $x + $this->width;
        $y2 = $y + $this->height;
        $resource = $image->getResource()->resource;
        for(;$x < $x2;$x += $this->step)
        {
            for($y1=$y;$y1 < $y2;$y1 += $this->step)
            {
                $color = ImageColorAt($resource,$x + round($this->step / 2),$y1 + round($this->step / 2));
                imagefilledrectangle($resource,$x,$y1,$x + $this->step,$y1 + $this->step, $color);
            }
        }
    }
}