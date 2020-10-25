<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */

namespace Ke\Utils;

/**
 * 状态接口类
 *
 * @package Ke\Web\Service
 */
interface StatusImpl
{

	public function setStatus($status);

	public function getStatus();

	public function setMessage(string $message);

	public function getMessage();

	public function getData();

	public function setData(array $data);

	public function addData(string $field, $data);

	public function mergeData(array $data);

	public function isSuccess();

	public function isFailure();

	public function toJSON();
}