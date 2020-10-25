<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */

namespace topic\library;

use tfc\ap\Ap;
use tfc\util\Language;

/**
 * Lang class file
 * 当前业务的语言国际化管理类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Lang.php 1 2013-04-05 01:38:06Z huan.song $
 * @package topic.library
 * @since 1.0
 */
class Lang
{
	/**
	 * 通过键名获取语言内容
	 * @param string $string
	 * @param boolean $jsSafe
	 * @param boolean $interpretBackSlashes
	 * @return string
	 */
	public static function _($string, $jsSafe = false, $interpretBackSlashes = true)
	{
		static $language = null;

		if ($language === null) {
			$type = Ap::getLanguageType();
			$baseDir = substr(dirname(__FILE__), 0, -8) . DS . 'languages';
			$language = Language::getInstance($type, $baseDir);
		}

		return $language->_($string, $jsSafe, $interpretBackSlashes);
	}
}
