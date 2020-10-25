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
use Tang\GD\Resource as ImageResource;

/**
 * 添加一个图形资源到图形上
 * Class AppendImage
 * @package Tang\Gd\Effects
 */
class AppendImage extends Base implements IDraw
{
    /**
     * 添加的图形资源
     * @var ImageResource
     */
    protected $resource;
    protected $opacity;

    /**
     * @param ImageResource $resource 添加的资源
     * @param $x X
     * @param $y Y
     * @param int $opacity 透明度
     */
    public function __construct(ImageResource $resource,$x,$y,$opacity = 100)
    {
        $this->setXY($x,$y)->setResource($resource)->setOpacity($opacity);
    }

    /**
     * 设置添加的图片资源
     * @param ImageResource $resource
     * @return $this
     */
    public function setResource(ImageResource $resource)
    {
        $this->resource = $resource;
        return $this;
    }

    /**
     * 设置 透明度
     * @param int $opacity
     * @return $this
     */
    public function setOpacity($opacity = 100)
    {
        $this->opacity = $this->getIntValue($opacity,100);
        return $this;
    }
    public function draw(Image $image)
    {
        $dstResource = $image->getResource()->resource;
        $srcResource = $this->resource->resource;
        $srcWidth = $this->resource->getWidth();
        $srcHeight = $this->resource->getHeight();
        $cutResource = imagecreatetruecolor($srcWidth, $srcHeight);
        imagecopy($cutResource, $dstResource, 0, 0, $this->x, $this->y, $srcWidth, $srcHeight);
        imagecopy($cutResource, $srcResource, 0, 0, 0, 0, $srcWidth, $srcHeight);
        imagecopymerge($dstResource, $cutResource, $this->x, $this->y, 0, 0, $srcWidth, $srcHeight, $this->opacity);
        imagedestroy($cutResource);
    }
}