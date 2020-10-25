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
 * 背景填充
 * Class Background
 * @package Tang\GD\Fills
 */
class Background implements IDraw
{
    protected $color;
    public function __construct(Color $color)
    {
        $this->setColor($color);
    }
    /**
     * 设置颜色
     * @param Color $color
     * @return Background
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
        return $this;
    }
    public function draw(Image $image)
    {
        if(!$this->color)
        {
            $this->color = Color::createByHex('#fff');
        }
        $resource = $image->getResource();
        imagefilledrectangle($resource->resource,0,0,$resource->getWidth(),$resource->getHeight(),$this->color->getColorAllocate($resource->resource));
    }
}