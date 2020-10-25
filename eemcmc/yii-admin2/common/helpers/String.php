<?php

namespace common\helpers;

/**
 * 字符串处理类
 *
 * @author ken <vb2005xu@qq.com>
 */
class String extends \yii\helpers\BaseStringHelper
{

	/**
	 * 获取uuid
	 * @staticvar int $being_timestamp
	 * @param type $suffix_len
	 * @return type
	 */
	public static function uuid($suffix_len = 2)
	{
		//! 计算种子数的开始时间
		static $being_timestamp = 1396681180; // 2012-5-10

		$time = explode(' ', microtime());
		$id = ($time[1] - $being_timestamp) . sprintf('%06u', substr($time[0], 2, 6));
		if ($suffix_len > 0)
		{
			$id .= substr(sprintf('%010u', mt_rand()), 0, $suffix_len);
		}
		return $id;
	}

	/**
	 * 返回6位的标识串 
	 * 
	 * @param string $x 
	 * @return string 
	 */
	public static function identify($x)
	{
		static $mask = '0123456789abcdefghijklmnopqrstuvwxyz';
		$x = sprintf("%u", crc32($x));

		$show = '';
		while ($x > 0)
		{
			$s = $x % 36;
			$show .= $mask[$s];
			$x = floor($x / 36);
		}
		return $show;
	}

	/**
	 * 取时间转换成的字符串
	 * @param int $mode
	 * @return string
	 */
	public static function datetimeToString($mode = 'y')
	{
		$str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$strs = str_split($str); //打散到数组
		$count = count($strs); //统计数组
		$dates = getdate(); //取日期时间
		switch ($mode)
		{
			case 'y': //年
				$key = substr($dates['year'], 2);
				$key = $key - 1;
				$size = 62;
				break;
			case 'm': //月
				$key = $dates['mon'] - 1;
				$size = 12;
				break;
			case 'd': //天
				$key = $dates['mday'] - 1;
				$size = 31;
				break;
			case 'h': //时
				$key = $dates['hours'];
				$size = 24;
				break;
			case 'i': //分
				$key = $dates['minutes'];
				$size = 60;
				break;
			case 's': //秒
				$key = $dates['seconds'];
				$size = 60;
				break;
		}

		$num = intval($count / $size); //取分页数量
		$mod = $count % $size; //取余
		//如果当前页
		if ($key >= $mod)
		{
			$offset = $num * $key + $mod;
			$length = $num;
		}
		else
		{
			$offset = $num * $key + $key;
			$length = $num + 1;
		}
		$strs = array_slice($strs, $offset, $length);
		$rand_key = array_rand($strs);
		$result = $strs[$rand_key];
		return $result;
	}

	/**
	 * 获取一个随机用户名
	 * @param string $prefix 前缀
	 * @return string
	 */
	public static function randGenUsername($prefix = '玩家')
	{
		$year = self::getDateStr('y');
		$mon = self::getDateStr('m');
		$day = self::getDateStr('d');
		$hour = self::getDateStr('h');
		$min = self::getDateStr('i');
		$second = self::getDateStr('s');
		$rand_str = self::getRandStr(2);
		$username = $prefix . $year . $mon . $day . $hour . $min . $second . $rand_str;
		return $username;
	}

	/**
	 * 随机生成一个字符串
	 * @param int $length 长度
	 * @param int $mode 生成模式: 1纯数字, 2纯小写字母, 3纯大写字母, 0数字大小字母混合
	 * @return string
	 */
	public static function randGenString($length = 32, $mode = 0)
	{
		switch ($mode)
		{
			case '1':
				$str = '1234567890';
				break;
			case '2':
				$str = 'abcdefghijklmnopqrstuvwxyz';
				break;
			case '3':
				$str = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
			default:
				$str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				break;
		}

		$result = '';
		$l = strlen($str) - 1;
		$num = 0;

		for ($i = 0; $i < $length; $i++)
		{
			$num = rand(0, $l);
			$a = $str[$num];
			$result = $result . $a;
		}
		return $result;
	}

	/**
	 * 统计字符串长度
	 * @param string $string 需要统计的字符串
	 * @param int $num 中文按几个字符来计算，默认为2
	 * @return int 字符长度
	 */
	public static function strlen($str, $num = 2)
	{
		$count = 0;
		$len = strlen($str);
		$num = $num >= 1 ? $num : 0;
		for ($i = 0; $i < $len; $i++)
		{
			if (ord($str[$i]) >= 128)
			{
				$i += 2;
				$count += $num;
			}
			else
			{
				$count++;
			}
		}
		return $count;
	}

	/**
	 * 判断字符串是否包含（大小写字母、数字、中文）以外的特殊字符
	 * @param string $str 需要判断的字符串
	 * @return bool 有特殊字符返回true, 否则返回false
	 */
	public static function isSpecial($str)
	{
//		//GBK编码环境下的正则
//		$chinese = chr(0xa1) . "-" . chr(0xff);
//		$pattern = "/^[a-zA-Z0-9($chinese)]{1,}$/";

		$pattern = "/^[a-zA-Z0-9(\x{4e00}-\x{9fa5})]{1,}$/u"; //utf-8编码环境下的正则
		if (preg_match($pattern, $str))
		{
			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * gbk编码转uft-8
	 * @param type $string
	 * @return type
	 */
	public static function gbkToUtf8($string)
	{
		$ret = mb_convert_encoding($string, 'utf-8', 'gbk');
		return $ret;
	}

	/**
	 * gbk编码转uft-8
	 * @param type $string
	 * @return type
	 */
	public static function utf8ToGbk($string)
	{
		$ret = mb_convert_encoding($string, 'gbk', 'utf-8');
		return $ret;
	}

	/**
	 * 将驼峰风格转为下划线风格
	 *
	 * @param  string  $value
	 * @param  string  $delimiter
	 * @return string
	 */
	public static function snakeCase($value, $delimiter = '_')
	{
		$replace = '$1' . $delimiter . '$2';

		return ctype_lower($value) ? $value : strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
	}

	/**
	 * Convert a value to studly caps case.
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function studlyCase($value)
	{
		$value = ucwords(str_replace(array('-', '_'), ' ', $value));

		return str_replace(' ', '', $value);
	}

	/**
	 * 将一个字符串转为驼峰风格
	 *
	 * @param  string  $value
	 * @return string
	 */
	public static function camelCase($value)
	{
		return lcfirst(static::studlyCase($value));
	}

}
