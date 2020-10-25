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
namespace Tang\GD\Effects;
use Tang\GD\Draws\Base;
use Tang\GD\IDraw;
use Tang\GD\Image;

/**
 * 水波效果
 * Class Wave
 * @package Tang\Gd\Effects
 */
class Wave extends Base implements IDraw
{
    /**
     *
     * @param int $x X轴扭动频率 值越小 扭动的越扭曲
     * @param int $y Y轴扭动频率 值越小 扭动的越扭曲
     */
    public function __construct($x,$y)
    {
        $this->setXY($x, $y);
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource();
        $width = $resource->getWidth();
        $height = $resource->getHeight();

        if($this->x > 0)
        {
            for ($i = 0; $i < $width; $i++)
            {
                imagecopy($resource->resource, $resource->resource,$i-1,sin($i/$this->x) *6,$i, 0, 1, $height);
            }
        }
        if($this->y > 0)
        {
            for ($i = 0; $i < $height; $i++)
            {
                imagecopy($resource->resource, $resource->resource,sin($i/$this->y) *6, $i-1,0, $i, $width, 1);
            }
        }
    }
}