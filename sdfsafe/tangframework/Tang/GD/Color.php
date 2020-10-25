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
namespace Tang\GD;
/**
 * 色彩类
 * Class Color
 * @package Tang\GD
 */
class Color
{
    /**
     * 红色
     * @var int
     */
    protected $r;
    /**
     * 绿色
     * @var int
     */
    protected $g;
    /**
     * 蓝色
     * @var int
     */
    protected $b;
    private function __construct($r,$g,$b)
    {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    /**
     * 获取红色
     * @return int
     */
    public function getRed()
    {
        return $this->r;
    }

    /**
     * 获取蓝色
     * @return int
     */
    public function getBlue()
    {
        return $this->b;
    }

    /**
     * 获取绿色
     * @return int
     */
    public function getGreen()
    {
        return $this->g;
    }

    /**
     * 根据图片资源获取色彩
     * @param $resource
     * @return int
     */
    public function getColorAllocate($resource)
    {
        return imagecolorallocate($resource,$this->r,$this->g,$this->b);
    }

    /**
     * 获取反色
     * @return Color
     */
    public function getInverseColor()
    {
        return static::createByRgb(255-$this->r,255-$this->g,255-$this->b);
    }
    /**
     * 根据色值创建颜色
     * @param $r
     * @param $g
     * @param $b
     * @return Color
     */
    public static function createByRgb($r,$g,$b)
    {
        return new self($r,$g,$b);
    }

    /**
     * 根据16进制创建颜色
     * @param $value
     * @return Color
     */
    public static function createByHex($value)
    {
        if ($value[0] == '#')
        {
            $value = substr($value,1);
        }
        $length = strlen($value);
        $r = $g = $b = 0;
        if ($length == 6)
        {
            list ($r,$g,$b) = array($value[0] . $value[1], $value[2] . $value[3], $value[4] . $value[5]);
        }elseif ($length == 3)
        {
            list ($r,$g,$b) = array($value[0] . $value[0], $value[1] . $value[1], $value[2] . $value[2] );
        } else
        {
            $r = $g = $b = '00';
        }
        $r = hexdec($r);
        $g = hexdec($g);
        $b = hexdec($b);
        return static::createByRgb($r,$g,$b);
    }
}