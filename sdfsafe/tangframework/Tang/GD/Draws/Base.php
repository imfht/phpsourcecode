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

/**
 * 绘图基类
 * Class Base
 * @package Tang\GD\Draws
 */
abstract class Base
{
    /**
     * x坐标
     * @var int
     */
    protected $x;
    /**
     * y坐标
     * @var int
     */
    protected $y;
    /**
     * 颜色
     * @var Color
     */
    protected $color;
    /**
     * 背景色
     * @var Color
     */
    protected $backgroundColor;
    /**
     * 宽度
     * @var int
     */
    protected $width;
    /**
     * 高度
     * @var int
     */
    protected $height;
    /**
     * 填充色
     * @var Color
     */
    protected $fillColor;

    /**
     * 设置颜色
     * @param Color $color
     * @return $this
     */
    public function setColor(Color $color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * 设置背景色
     * @param Color $color
     * @return $this
     */
    public function setBackgroundColor(Color $color)
    {
        $this->backgroundColor = $color;
        return $this;
    }

    /**
     * 设置XY坐标
     * @param $x
     * @param $y
     * @return $this
     */
    public function setXY($x,$y)
    {
        $this->x = $this->getIntValue($x);
        $this->y = $this->getIntValue($y);
        return $this;
    }

    /**
     * 设置宽度
     * @param $width
     * @return $this
     */
    public function setWidth($width)
    {
        $this->width = $this->getIntValue($width);
        return $this;
    }

    /**
     * 设置高度
     * @param $height
     * @return $this
     */
    public function setHeight($height)
    {
        $this->height = $this->getIntValue($height);
        return $this;
    }

    /**
     * 设置填充颜色 如果不想填充则设置null
     * @param Color $color
     * @return $this
     */
    public function setFillColor(Color $color= null)
    {
        $this->fillColor = $color;
        return $this;
    }

    /**
     * 获取整数值。如果小于1则返回$default
     * @param $value
     * @param int $default
     * @return int
     */
    protected function getIntValue($value,$default=0)
    {
        $value = (int)$value;
        $value < 1 && $value=$default;
        return $value;
    }
}