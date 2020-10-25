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

use Ke\Utils\StatusImpl;

/**
 * 网站服务接口类
 *
 * @package Ke\Web\Service
 */
interface WebServiceImpl
{

	public function serve(string $name, ...$args): StatusImpl;
}