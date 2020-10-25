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
namespace Tang\Util;
use Tang\Services\ConfigService;

/**
 * 验证类
 * Class Validate
 * @package Tang\Util
 */
class Validate
{
    /**
     * 验证字符串的最小长度
     * @param $value
     * @param $minSize
     * @return bool
     */
    public static function minStringLength($value,$minSize)
    {
        return static::getStringLength($value) > $minSize;
    }
    /**
     * 验证字符串的最大长度
     * @param $value
     * @param $maxSize
     * @return bool
     */
    public static function maxStringLength($value,$maxSize)
    {
        return static::getStringLength($value) < $maxSize;
    }

    /**
     * 验证字符串的长度
     * @param $value 字符串
     * @param $minSize 最小长度
     * @param $maxSize 最大长度
     * @return bool
     */
    public static function stringLength($value,$minSize,$maxSize)
    {
        $stringLength = static::getStringLength($value);
        return $stringLength >= $minSize && $stringLength <= $maxSize;
    }

	/**
	 * 比较$value的大小是否在$min与$max之间
	 * @param $value
	 * @param float $min
	 * @param float $max
	 * @return bool
	 */
	public static function compareWithFloat(&$value,$min = 0.0,$max = 0.0,$isEqual = false)
	{
		$value = (float)$value;
		$min = (float)$min;
		$max = (float)$max;
		return $isEqual ? $min >= $value && $value <= $max:$min > $value && $value < $max;
	}

	/**
	 * 比较$value的大小是否在$min与$max之间 包含了$min与$max
	 * @param $value
	 * @param float $min
	 * @param float $max
	 * @return bool
	 */
	public static function compareEqualWithFloat(&$value,$min = 0.0,$max = 0.0)
	{
		return static::compareWithFloat($value,$min,$max,true);
	}
    /**
     * 比较$value 比 $float大
     * @param $value
     * @param float $float
     * @return bool
     */
    public static function greaterThanFloat(&$value,$float = 0.0,$isGreater = true)
    {
		$value = (float)$value;
		$float = (float)$float;
        return $isGreater ? $value > $float : $value < $float;
    }

	/**
	 * 比较$value 比$float小
	 * @param $value
	 * @param float $float
	 * @return bool
	 */
	public static function lessThanFloat(&$value,$float = 0.0)
	{
		return static::greaterThanFloat($value,$float,false);
	}

	/**
	 * 比较$value的大小是否在$min与$max之间
	 * @param $value
	 * @param float $min
	 * @param float $max
	 * @return bool
	 */
	public static function compareWithInt(&$value,$min = 0,$max = 0,$isEqual = false)
	{
		$value = (int)$value;
		$min = (int)$min;
		$max = (int)$max;
		return $isEqual ? $value >= $min && $value <= $max:$value > $min && $value < $max;
	}

	/**
	 * 比较$value的大小是否在$min与$max之间 包含了$min与$max
	 * @param $value
	 * @param float $min
	 * @param float $max
	 * @return bool
	 */
	public static function compareEqualWithInt(&$value,$min = 0.0,$max = 0.0)
	{
		return static::compareWithFloat($value,$min,$max,true);
	}

    /**
     * 比较$value 比$number大
     * @param $value
     * @param int $number
     * @return bool
     */
    public static function greaterThanInt(&$value,$number = 0,$isGreater = true)
    {
		$value = (int)$value;
		$number = (int)$number;
        return $isGreater ? $value > $number : $value < $number;
    }

	/**
	 * 比较$value 比$number小
	 * @param $value
	 * @param int $number
	 * @return bool
	 */
	public static function lessThanInt(&$value,$number = 0)
	{
		return static::greaterThanInt($value,$number,false);
	}

    /**
     * 循环$arrays 率先出比$min大的数
     * @param $array
     * @param int $min
     * @return bool
     */
    public static function compareArrays(&$array, $min = 0)
    {
        $min = (int)$min;
        if ($array && is_array($array))
        {
            $newArray = array();
            foreach ($array as $value)
            {
                $value = (int) $value;
                if ($value > $min)
                {
                    $newArray[] = $value;
                }
            }
            if ($newArray)
            {
                $array = $newArray;
                return true;
            }
        }
        return false;
    }

	/**
	 * 判断是否存在于数组
	 * @param $value
	 * @param array $array
	 * @return bool
	 */
	public static function inArray($value,array $array)
	{
		return in_array($value,$array);
	}

	/**
	 * $value大于某个值，并且存在于$array
	 * @param $value
	 * @param $maxValue
	 * @param array $array
	 * @return bool
	 */
	public static function greaterThanInArray($value,$maxValue,array $array)
	{
		return $value > $maxValue && isset($array[$value]);
	}

	/**
	 * $value小于某个值，并且存在于$array
	 * @param $value
	 * @param $minValue
	 * @param array $array
	 * @return bool
	 */
	public static function lessThanInArray($value,$minValue,array $array)
	{
		return $value > $minValue && in_array($value,$array);
	}

    /**
     * 用户名校验
     * @param $userName 用户名
     * @param $minSize 最小长度
     * @param $maxSize 最大长度
     * @return bool
     */
    public static function userName($userName,$minSize,$maxSize)
    {
        return static::stringLength($userName,$minSize,$maxSize) &&
                preg_match('/^[a-zA-Z0-9_ ]\w{3,}$/i',$userName) &&
                !preg_match('/[\\\<>\?\#\$\*\&;\/\[\]\{\}=\(\)\.\^%,]/', $userName);
    }

    /**
     * 判断字符串是否全部为英文 以及字符串的长度
     * @param $value 字符串
     * @param  $minSize 最小长度
     * @param  $maxSize 最大长度
     * @return boolean
     */
    public static function isEnglish($value,$minSize,$maxSize)
    {
        return preg_match('/^[A-Za-z]+$/', $value) && static::stringLength($value,$minSize,$maxSize);
    }

    /**
     * 判断字符串是否全部为中文 以及字符串的长度
     * @param $value 字符串
     * @param $minSize 最小长度
     * @param $maxSize 最大长度
     * @return boolean
     */
    public static function isChinese($value,$minSize,$maxSize)
    {
        return preg_match('/^[\x0391-\xFFE5]+$/',$value) && static::stringLength($value,$minSize,$maxSize);
    }

	/**
	 * 判断字符串是否为URL地址 以及字符串的长度
	 * @param $value 字符串
	 * @param $minSize 最小长度
	 * @param $maxSize 最大长度
	 * @return boolean
	 */
	public static function isUrl($value,$minSize,$maxSize)
	{
		return preg_match('/^(http:\/\/|https:\/\/)?((?:[A-Za-z0-9]+-[A-Za-z0-9]+|[A-Za-z0-9]+)\.)+([A-Za-z]+)[\/\?\:]?.*$/',$value) && static::stringLength($value,$minSize,$maxSize);
	}

    /**
     * 判断一个字符窜是否为email地址 以及字符串的长度
     * @param $value
     * @param $minSize 最小长度
     * @param $maxSize 最大长度
     * @return boolean
     */
    public static function isEmail($value,$minSize,$maxSize)
    {
        return static::stringLength($value,$minSize,$maxSize) && preg_match('/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/', $value);
    }

    /**
     * 判断是否是一个alipay账号
     * @param $id
     * @return bool
     */
    public static function isAlipay($id,$minSize,$maxSize)
    {
        return static::isEmail($id,$minSize,$maxSize) || static::isMobile($id);
    }

    /**
     * 判断一个字符窜是否为移动电话号码
     * @param $value
     * @return boolean
     */
    public static function isMobile($value)
    {
        return preg_match('/^13[0-9]{9}$|14[0-9]{9}|15[0-9]{9}$|18[0-9]{9}$/', $value);
    }

    /**
     * 判断一个字符窜是否为电话号码
     * @param $value
     * @return boolean
     */
    public static function isTelphone($value)
    {
        return preg_match('/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',$value);
    }

    /**
     * 获取字符串长度
     * @param $string
     * @return int
     */
    public static function getStringLength($string)
    {
        return mb_strlen($string,ConfigService::getService()->get('charset'));
    }
}