<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Adm;

/**
 * 缓存数据模型
 *
 * @package Ke\Adm
 */
abstract class CacheModel implements CacheModelImpl
{

	use CacheModelTrait;

	protected static $cacheSource = null;

	protected static $cacheTTL = 60 * 60 * 12;

	public static function getCacheSource()
	{
		return static::$cacheSource;
	}

	public static function getCacheDefaultTTL(): int
	{
		return static::$cacheTTL;
	}

}