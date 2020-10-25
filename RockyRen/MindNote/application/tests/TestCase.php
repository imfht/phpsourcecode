<?php
/**
 * Part of CI PHPUnit Test
 *
 * @author     Kenji Suzuki <https://github.com/kenjis>
 * @license    MIT License
 * @copyright  2015 Kenji Suzuki
 * @link       https://github.com/kenjis/ci-phpunit-test
 */

class TestCase extends PHPUnit_Framework_TestCase
{
	protected $_error_reporting = -1;

	/**
	 * Request to Controller
	 * 
	 * @param string   $method   HTTP method
	 * @param array    $argv     controller, method [, arg1, ...]
	 * @param array    $params   POST parameters/Query string
   * @param array    $params   GET parameters
	 * @param callable $callable
	 */
	public function request($method, $argv, $params = [], $get_params = [], $callable = null)
	{
		$_SERVER['REQUEST_METHOD'] = $method;
		
		$_SERVER['argv'] = array_merge(['index.php'], $argv);

    //POST、DELETE、PUT方法的名值对都放在$_POST超级变量上
		if ($method === 'POST' || $method === 'DELETE' || $method === 'PUT')
		{
      $_GET = $get_params;
			$_POST = $params;
		}
		else if ($method === 'GET')
		{
      $p = array_merge($params, $get_params);
			$_GET = $p;
		}


		$this->CI = get_new_instance();
		if (is_callable($callable))
		{
			$callable($this->CI);
		}

		array_shift($_SERVER['argv']);
		$controller = array_shift($_SERVER['argv']);
		$controller = ucfirst($controller);
		$method = array_shift($_SERVER['argv']);
		$this->obj = new $controller;


//		ob_start();
		$output = call_user_func_array([$this->obj, $method], $_SERVER['argv']);
//		$output = ob_get_clean();


		return $output;
	}
  
	public function warning_off()
	{
		$this->_error_reporting = error_reporting(E_ALL & ~E_WARNING);
	}

	public function warning_on()
	{
		error_reporting($this->_error_reporting);
	}

}
