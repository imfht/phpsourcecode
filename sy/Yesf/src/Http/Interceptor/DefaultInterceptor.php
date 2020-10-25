<?php
/**
 * Default Interceptor
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http\Interceptor;

use Yesf\Yesf;
use Yesf\Log\Logger;
use Yesf\Http\Request;
use Yesf\Http\Response;
use Yesf\Http\Template;

class DefaultInterceptor implements AfterInterface {
	public function after(Request $request, Response $response) {
		if ($response->result !== null) {
			if (is_array($response->result) || is_object($response->result)) {
				$result = json_encode($response->result);
			} else {
				$result = $response->result;
			}
			$response->write($result);
		}
		if ($request->status !== null) {
			$response->disableView();
			$response->setCurrentTemplateEngine(Template::class);
			if (is_int($request->status)) {
				$this->handleNotFound($request, $response);
			} else {
				$this->handleException($request, $response);
			}
		}
		return null;
	}
	private function handleNotFound(Request $request, Response $response) {
		$response->status(404);
		if (Yesf::app()->getEnvironment() === 'develop') {
			$response->assign('request', $request);
			$response->display(YESF_PATH . 'Data/error_404_debug.php', true);
		} else {
			$response->display(YESF_PATH . 'Data/error_404.php', true);
		}
	}
	private function handleException(Request $request, Response $response) {
		$exception = $request->status;
		//日志记录
		Logger::error('Uncaught exception: ' . $exception->getMessage() . '. Trace: ' . $exception->getTraceAsString());
		if (Yesf::app()->getEnvironment() === 'develop') {
			$response->assign('exception', $exception);
			$response->assign('request', $request);
			$response->display(YESF_PATH . 'Data/error_debug.php', true);
		} else {
			$response->display(YESF_PATH . 'Data/error.php', true);
		}
	}
}