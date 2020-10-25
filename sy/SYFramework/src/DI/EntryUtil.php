<?php
/**
 * 部分特殊内容转换
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category DI
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2015-2019 ShuangYa
 * @license https://syframework.sylibs.com/license.html
 */
namespace Sy\DI;

use Sy\App;
use Sy\DB\DBInterface;
use Sy\DB\Mysql;
use Sy\DB\Postgre;
use Sy\DB\Sqlite;
use Sy\Cache\Yac;
use Sy\Cache\File;
use Sy\Cache\Redis;
use Sy\Cache\Memcached;
use Sy\Http\Template;
use Sy\Http\TemplateInterface;
use Psr\SimpleCache\CacheInterface;

class EntryUtil {
	public static function controller($module, $controller) {
		return App::$cfgNamespace . 'Module\\' . $module . '\\Controller\\' . ucfirst($controller);
	}
	public static function initAlias() {
		$database = App::$config->get('database');
		$db_class = null;
		switch ($database) {
			case 'mysql':
				$db_class = Mysql::class;
				break;
			case 'postgre':
				$db_class = Postgre::class;
				break;
			case 'sqlite':
				$db_class = Sqlite::class;
				break;
		}
		if ($db_class) {
			Container::getInstance()->set(DBInterface::class, $db_class);
		}
		$cache = App::$config->get('cache.type');
		$cache_class = null;
		switch ($cache) {
			case 'yac':
				$cache_class = Yac::class;
				break;
			case 'file':
				$cache_class = File::class;
				break;
			case 'redis':
				$cache_class = Redis::class;
				break;
			case 'memcached':
				$cache_class = Memcached::class;
				break;
		}
		if ($cache_class) {
			Container::getInstance()->set(CacheInterface::class, $cache_class);
		}
		$template = App::$config->get('template.engine', Template::class);
		Container::getInstance()->set(TemplateInterface::class, $template);
		Container::getInstance()->setMulti($template, Container::MULTI_CLONE);
	}
}