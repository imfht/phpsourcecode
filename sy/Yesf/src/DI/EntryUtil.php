<?php
/**
 * 部分特殊内容转换
 * 
 * @author ShuangYa
 * @package Yesf
 * @category DI
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\DI;

use Yesf\Yesf;

class EntryUtil {
	public static function controller($module, $controller) {
		return Yesf::app()->getConfig('namespace', Yesf::CONF_PROJECT) . 'Module\\' . $module . '\\Controller\\' . ucfirst($controller);
	}
}