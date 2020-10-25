<?php
/**
 * VgotFaster PHP Framework
 *
 * @package VgotFaster
 * @author pader
 * @copyright Copyright (c) 2009-2015, VGOT.NET
 * @link http://www.vgot.net/ http://vgotfaster.googlecode.com
 * @filesource
 */

namespace VF;

/**
 * VgotFaster Base Abstract Class
 *
 * @package VgotFaster
 * @subpackage Interface
 * @author pader
 */
abstract class Base {

	private static $instance;

	protected function __setInstance()
	{
		self::$instance =& $this;
	}

	public static function &__getInstance()
	{
		return self::$instance;
	}

}

/**
 * Load Core Class
 *
 * @param string $class
 * @return object
 */
function &loadCore($class) {
	$namespace = '\\VF\\Core\\';
	$filename = ucfirst($class).'.php';
	$coreFile = SYSTEM_PATH.'/core/'.$filename;

	if (is_file($coreFile)) {
		include_once $coreFile;

		$coreFile = APPLICATION_PATH.'/core/'.$filename;
		if (is_file($coreFile)) {
			include_once $coreFile;
			$namespace = '\\Core\\';
		}
	} else {
		showError('No Found Core File: '.$filename);
	}

	$className = $namespace.ucfirst($class);
	_systemLog("Load Core Class '$className'");
	$object = new $className;

	return $object;
}
