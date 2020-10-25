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
use Tang\GD\Resource;

/**
 * 缩略图效果
 * Class Thumb
 * @package Tang\Gd\Effects
 */
class Thumb extends Base implements IDraw
{
    public function __construct($width,$height)
    {
        $this->setWidth($width)->setHeight($height);
    }
    public function draw(Image $image)
    {
        if($this->width == 0 || $this->height == 0)
        {
            return;
        }
        $resource = $image->getResource();
        $imageWidth = $resource->getWidth();
        $imageHeight = $resource->getHeight();
        $ratio = $imageWidth / $imageHeight;  //原图比例
        $thumbRatio = $this->width / $this->height; //缩略后比例
        $thumbResource = Resource::CreateByTrueColor($this->width, $this->height, $resource->getType());
        if($ratio >= $thumbRatio)
        {
            $imageWidth = $imageHeight * $thumbRatio;
        } else
        {
            $imageHeight = $imageWidth / $thumbRatio;
        }
        imagecopyresampled($thumbResource->resource, $resource->resource, 0, 0, 0, 0, $this->width,$this->height,$imageWidth, $imageHeight);
        $image->setResource($thumbResource);
    }
}