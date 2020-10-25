<?php
/**
 * HTTP事件回调
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Swoole
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Event;

use Yesf\Yesf;
use Yesf\DI\Container;
use Yesf\Http\Request;
use Yesf\Http\Response;
use Yesf\Http\Dispatcher;

class HttpServer {
	/**
	 * HTTP事件：收到请求
	 * 
	 * @access public
	 * @param Swoole\Http\Request $request
	 * @param Swoole\Http\Response $response
	 */
	public static function onRequest($request, $response) {
		$request = new Request($request);
		$request->setCookieHandler([$response, 'cookie']);
		$response = new Response($response);
		Container::getInstance()
			->get(Dispatcher::class)
			->handleRequest($request, $response);
	}
}