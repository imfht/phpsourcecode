<?php
/**
 * 路由解析类
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
use Yesf\Http\Response;

interface RouterInterface {
	public function parse(Request $request);
}