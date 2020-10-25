<?php
/**
 * HTTP其他参数
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http;

use Yesf\Yesf;

class Vars {
	protected static $mimeTypes = null;
	public static function mimeType($extension, $includeCharset = true) {
		if (self::$mimeTypes === null) {
			self::$mimeTypes = require(YESF_PATH . 'Data/mimeTypes.php');
		}
		$extension = strtolower($extension);
		if (!isset(self::$mimeTypes[$extension])) {
			return 'application/octet-stream';
		}
		$mimeType = self::$mimeTypes[$extension];
		if ($includeCharset && in_array($extension, ['js', 'json', 'atom', 'rss', 'xhtml'], true) || substr($mimeType, 0, 5) === 'text/') {
			$mimeType .= '; charset=' . Yesf::app()->getConfig('charset', Yesf::CONF_PROJECT);
		}
		return $mimeType;
	}
}