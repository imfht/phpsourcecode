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

class CodeStatus extends Status
{

	const SUCCESS_CODE = 200;

	const DEFAULT_CODE = 400;

	protected $code;

	public function convertCode($code)
	{
		$code = intval($code);
		if ($code === false)
			$code = self::DEFAULT_CODE;
		return $code;
	}

	public function isSuccessCode($code)
	{
		return $code === self::SUCCESS_CODE;
	}

	public function __construct($code, string $message = null, array $data = null)
	{
		$this->code = $this->convertCode($code);
		parent::__construct($this->isSuccessCode($this->code), $message, $data);
	}

	public function getCode()
	{
		return $this->code;
	}

	public function setCode($code)
	{
		$this->code = $this->convertCode($code);
		$this->setStatus($this->isSuccessCode($this->code));
		return $this;
	}

	public function export()
	{
		$data = ['code' => $this->code];
		$data += parent::export();
		return $data;
	}
}