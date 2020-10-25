<?php
/**
 * Interceptor interface
 * 
 * @author ShuangYa
 * @package Yesf
 * @category Base
 * @link https://www.sylingd.com/
 * @copyright Copyright (c) 2017-2019 ShuangYa
 * @license https://yesf.sylibs.com/license.html
 */
namespace Yesf\Http\Interceptor;

use Yesf\Http\Request;
use Yesf\Http\Response;

interface AfterInterface extends BaseInterface {
	public function after(Request $request, Response $response);
}