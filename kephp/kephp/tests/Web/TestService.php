<?php


namespace tests\Web;


use Ke\Utils\StatusImpl;
use Ke\Utils\Success;
use Ke\Web\Service\WebServiceTrait;

class TestService
{

	use WebServiceTrait;

	protected function beforeServe(string $name, ...$args)
	{
		// TODO: Implement beforeServe() method.
	}

	protected function afterServer(string $name, StatusImpl $status, ...$args)
	{
		// TODO: Implement afterServer() method.
	}

	protected function onException(string $name, StatusImpl $status, \Throwable $error)
	{
		$status->addData('error', $error);
	}

	public function no_return_serve()
	{

	}

	public function throw_error_serve()
	{
		throw new \Exception('这是一个异常');
	}

	public function another_serve()
	{
		return new Success('success', [
			'key' => 'value',
		]);
	}
}