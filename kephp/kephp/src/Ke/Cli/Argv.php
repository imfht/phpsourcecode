<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Cli;

use ArrayObject;

class Argv extends ArrayObject
{

	private static $current = null;

	private static $parsedRawArgv = [];

	public static function current()
	{
		if (!isset(self::$current)) {
			$argv = isset($_SERVER['argv']) ? $_SERVER['argv'] : [KE_SCRIPT_FILE];
			array_shift($argv);
			self::$current = new static(static::parse($argv));
		}
		return self::$current;
	}

	/**
	 * @param $rawArgv
	 * @return Argv
	 */
	public static function fromRaw($rawArgv)
	{
		return new static(static::parse($rawArgv));
	}

	/**
	 * 解析原始的命令行参数，转化为一个key->value的数据结构
	 *
	 * 初始的参数列表，应该只是数组和字符格式，
	 * 字符格式：'command --arg1=value1 --arg2=value2'
	 * 数组格式：['command', '--arg1=value1', '--arg2=value2']
	 *
	 * 在以`$_SERVER['argv']`为参数传入时，应该手动移除 0 位的文件。
	 *
	 * @param string|array $rawArgv
	 * @return array
	 */
	public static function parse($rawArgv)
	{
		$argv = null;
		$data = [];
		if (empty($rawArgv))
			return $data;
		$type = gettype($rawArgv);
		if ($type === KE_STR) {
			if (isset(self::$parsedRawArgv[$rawArgv]))
				return self::$parsedRawArgv[$rawArgv];
			// command --args1=value1
			$argv = explode(' ', $rawArgv);
		} elseif ($type === KE_OBJ) {
			// 一个对象不是做有效的RawArgv
			return $data;
		} elseif ($type === KE_ARY) {
			$key = implode(' ', $rawArgv);
			if (isset(self::$parsedRawArgv[$key]))
				return self::$parsedRawArgv[$key];
			$argv = $rawArgv;
			$rawArgv = $key;
		}
		if (empty($argv))
			return $data;
		$inPair = false;
		$naturalIndex = -1; // 自然计数索引
		foreach ($argv as $index => $item) {
			$item = trim($item);
			if (preg_match('#^(\-+)([^\=]+)(?:\=(.*))?#', $item, $matches)) {
				if ($inPair !== false) {
					$inPair = false;
				}
				if ($inPair === false) {
//				    $prefix = $matches[1];
//				    if (strlen($prefix) > 2)
//					    $prefix = '--'; // limit -- should in 2 chars
					$prefix = $matches[2];
					if (isset($matches[3])) {
						$data[$prefix] = $matches[3];
					} else {
						$data[$prefix] = '';
						$inPair = $prefix;
					}
				}
				continue;
			}
			if ($inPair !== false) {
				$data[$inPair] = $item;
				$inPair = false;
				continue;
			}
			$naturalIndex++;
			$data[] = $item;
		}
		ksort($data, SORT_NATURAL);
		// 将这个结果记录下来
		self::$parsedRawArgv[$rawArgv] = $data;
		return $data;
	}

	public static function new($argv)
	{
		$data = static::parse($argv);
		return new static($data);
	}

	public function __construct(array $input = [])
	{
		parent::__construct($input, ArrayObject::ARRAY_AS_PROPS);
	}

	/**
	 * 批量绑定数据的操作
	 *
	 * Argv只作为命令行参数的存储容器，而不具有数据逻辑，高级的数据过滤，在Command上实现：
	 *
	 * Argv => Command => Command->properties (这里存放的是经过过滤后的数据，并且是当前命令所需的参数)
	 *
	 * @param string|array|object $input
	 * @return $this
	 */
	public function setData($input)
	{
		$type = gettype($input);
		if ($type === KE_STR) {
			// arg1=value2&argv2=value2
			$type = KE_ARY;
			parse_str($input, $input);
		} elseif ($type === KE_OBJ) {
			// 一个对象无法做有效的转换
			$type = KE_ARY;
			$input = get_object_vars($input);
		}
		if (empty($input) || $type !== KE_ARY)
			return $this;
		foreach ($input as $field => $value) {
			$this[$field] = $value;
		}
		return $this;
	}

	public function getData()
	{
		return (array)$this;
	}

	public function isEmpty()
	{
		return empty((array)$this);
	}
}