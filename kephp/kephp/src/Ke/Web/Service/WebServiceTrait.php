<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


namespace Ke\Web\Service;

use Ke\Utils\Status;
use Ke\Utils\Failure;
use Ke\Utils\Success;
use Ke\Utils\StatusImpl;
use Ke\Web\Http;
use Ke\Web\Web;

/**
 * 网站服务特质类
 *
 * @package Ke\Web\Service
 */
trait WebServiceTrait
{

	/**
	 * @var Web
	 */
	protected $web = null;

	/**
	 * @var Http
	 */
	protected $http = null;

	public function getWeb()
	{
		if (!isset($this->web)) {
			return Web::getWeb();
		}
		return $this->web;
	}

	public function setWeb(Web $web)
	{
		$this->web = $web;
		return $this;
	}

	public function getHttp()
	{
		if (!isset($this->http)) {
			return $this->getWeb()->http;
		}
		return $this->http;
	}

	public function setHttp(Http $http)
	{
		$this->http = $http;
		return $this;
	}

	/**
	 * 生成一个新的失败状态实例
	 *
	 * @param string $message
	 * @param array  $data
	 *
	 * @return Status|Failure|Success|StatusImpl
	 */
	public function newFailure(string $message, array $data = []): StatusImpl
	{
		return new Failure($message, $data);
	}

	/**
	 * 执行服务前钩子
	 *
	 * @param string $name
	 * @param mixed  ...$args
	 *
	 * @return mixed
	 */
	abstract protected function beforeServe(string $name, ...$args);

	/**
	 * 执行服务后钩子
	 *
	 * @param string     $name
	 * @param StatusImpl $status
	 * @param mixed      ...$args
	 *
	 * @return mixed
	 */
	abstract protected function afterServe(string $name, StatusImpl $status, ...$args);

	abstract protected function onException(string $name, StatusImpl $status, \Throwable $error);

	/**
	 * 生成服务名
	 *
	 * 使用访问的服务名，强制转小写，加后缀 _serve
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	public function mkServeName(string $name)
	{
		$name = trim($name, ' -_./\\');
		if (empty($name)) $name = 'index';
		$name = mb_strtolower($name);
		return $name . '_serve';
	}

	/**
	 * 执行一个 Web Serve，必然返回一个 StatusImpl
	 *
	 * @param string $name
	 * @param mixed  ...$args
	 *
	 * @return Status|Failure|Success|StatusImpl
	 */
	public function serve(string $name, ...$args): StatusImpl
	{
		$serveName = $this->mkServeName($name);
		if (method_exists($this, $serveName) && is_callable([$this, $serveName])) {
			try {
				$this->beforeServe($name, ...$args);
				$status = $this->$serveName(...$args);
				if ((!$status instanceof StatusImpl)) {
					$status = $this->newFailure('Invalid serve return!');
				}
				$this->afterServe($name, $status, ...$args);
			} catch (\Throwable $throwable) {
				$status = $this->newFailure($throwable->getMessage());
				$this->onException($name, $status, $throwable);
			}
			return $status;
		}
		return $this->newFailure('Serve undefined!');
	}

}