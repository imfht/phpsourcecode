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

/**
 * Controller Parent Class
 *
 * @package VgotFaster
 * @subpackage Interface
 * @author pader
 *
 * @property \VF\Core\Loader $load
 * @property \VF\Core\Config $config
 * @property \VF\Core\Input $input
 * @property \VF\Database\Database $db
 */
abstract class Controller extends \VF\Base {

	public static $afterRuntimeFunctions = array();

	public function __construct()
	{
		parent::__setInstance();

		//Load Default Libraries
		$systemLoads = array(
			'config',
			'load' => 'loader',
			'input'
		);

		foreach($systemLoads as $instance => $class) {
			is_numeric($instance) && $instance = $class;
			$this->$instance =& \VF\loadCore($class);
		}

		//Autoload
		$this->load->autoload();
	}

	public function _redirect()
	{
		showError404();
	}

	/**
	 * To Run The Registered Functions
	 *
	 * @return void
	 */
	public function __ControllerAfterRuntime()
	{
		if (count(self::$afterRuntimeFunctions) == 0) return;

		foreach (self::$afterRuntimeFunctions as $library => $function) {
			if(is_numeric($library)) {
				call_user_func_array($function, array());
			} else {
				if (!is_array($function)) continue;

				$VF =& getInstance();

				//printr($VF);

				foreach($function as $method) {
					$VF->$library->$method();
				}
			}
		}
	}

}

/**
 * Model Parent Class
 *
 * @package VgotFaster
 * @subpackage Interface
 * @author pader
 *
 * @property \VF\Core\Loader $load
 * @property \VF\Core\Config $config
 * @property \VF\Core\Input $input
 * @property \VF\Database\Database $db
 */
abstract class Model {

	protected $_selfClassName = '';

	public function __construct()
	{
		$this->_selfClassName = get_class($this);
		$this->_assignLibraries();
	}

	/**
	 * Assign loaded library|libraries to this model
	 *
	 * @param string $instance Empty to assign all libraries
	 */
	public function _assignLibraries($instance='')
	{
		$VF =& getInstance();
		$objects = $instance == '' ? array_keys(get_object_vars($VF)) : array($instance);

		foreach ($objects as $key) {
			if (is_object($VF->$key) AND !($VF->$key instanceof $this->_selfClassName) AND empty($this->$key)) {
				$this->$key =& $VF->$key;
			}
		}
	}

	/**
	 * Inject this model to other loaded models
	 *
	 * @param string $instance
	 */
	public function _injectBroadcast($instance)
	{
		$VF =& getInstance();
		foreach (array_keys(get_object_vars($VF)) as $key) {
			if (is_object($VF->$key) AND ($VF->$key instanceof Model) AND !($VF->$key instanceof $this->_selfClassName)
				&& !isset($VF->$key->$instance)) {
				$VF->$key->$instance =& $this;
			}
		}
	}

}

/**
 * Get VgotFaster Instance Object
 *
 * @return Controller
 */
function &getInstance() {
	return \VF\Base::__getInstance();
}

/**
 * Register A Function After Controller Run End
 *
 * @param string $callback Function or object name
 * @param string $method
 * @return void
 */
function registerControllerAfterFunction($callback,$method='') {
	if($method == '') {  //Function
		$skey = array_search($callback,Controller::$afterRuntimeFunctions);
		if($skey === FALSE or !is_numeric($skey)) {
			Controller::$afterRuntimeFunctions[] = $callback;
		}
	} else {  //Object method
		$VF =& getInstance();

		if(!isset(Controller::$afterRuntimeFunctions[$callback])) {
			Controller::$afterRuntimeFunctions[$callback] = array();
		}

		if(!in_array($method,Controller::$afterRuntimeFunctions[$callback])) {
			Controller::$afterRuntimeFunctions[$callback][] = $method;
		}
	}
}
