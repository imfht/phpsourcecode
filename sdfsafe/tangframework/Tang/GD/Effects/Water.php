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
use Tang\GD\IDraw;
use Tang\GD\Image;
use Tang\GD\Resource as ImageResource;

/**
 * 添加图片水印
 * Class Water
 * @package Tang\Gd\Effects
 */
class Water extends AppendImage implements IDraw
{
    const TOP_LEFT = 1; //左上
    const TOP_MIDDLE = 2;//中上
    const TOP_RIGHT = 3;//右中
    const MIDDLE_LEFT = 4;//中左
    const MIDDLE_MIDDLE = 5;//中中
    const MIDDLE_RIGHT = 6;//中右
    const BOTTOM_LEFT = 7;//下左
    const BOTTOM_MIDDLE = 8;//下中
    const BOTTOM_RIGHT = 9;//下右
    protected $postion = 0;

    public function __construct(ImageResource $resource,$postion,$opacity = 100)
    {
        $this->setResource($resource)->setOpacity($opacity);
    }
    public function setPostion($postion)
    {
        $this->postion = $this->getIntValue($postion);
        return $this;
    }
    public function draw(Image $image)
    {
        $resource = $image->getResource();
        $waterX = $waterY = 0;
        $imageHeight = $resource->getHeight();
        $imageWidth = $resource->getWidth();
        $waterHeight = $this->resource->getHeight();
        $waterWidth = $this->resource->getWidth();
        switch ($this->postion)
        {
            case Water::TOP_LEFT:
                break;
            case Water::MIDDLE_LEFT:
                $waterY = ($imageHeight - $waterHeight) / 2;
                break;
            case Water::BOTTOM_LEFT:
                $waterY = $imageHeight - $waterHeight;
                break;
            case Water::TOP_MIDDLE:
                $waterX = ($imageWidth - $waterWidth) / 2;
                break;
            case Water::MIDDLE_MIDDLE:
                $waterX = ($imageWidth - $waterWidth) / 2;
                $waterY = ($imageHeight - $waterHeight) / 2;
                break;
            case WAter::BOTTOM_MIDDLE:
                $waterX = ($imageWidth - $waterWidth) / 2;
                $waterY = $imageHeight - $waterHeight;
                break;
            case Water::TOP_RIGHT:
                $waterX = $imageWidth - $waterWidth;
                break;
            case Water::MIDDLE_RIGHT:
                $waterX = $imageWidth - $waterWidth;
                $waterY = ($imageHeight - $waterHeight) / 2;
                break;
            case Water::BOTTOM_RIGHT:
                $waterX = $imageWidth - $waterWidth;
                $waterY = $imageHeight - $waterHeight;
                break;
            default:
                $waterX = rand(0,($imageWidth - $waterWidth));
                $waterY = rand(0,($imageHeight - $waterHeight));
        }
        $this->setXY($waterX,$waterY);
        parent::draw($image);
    }
}