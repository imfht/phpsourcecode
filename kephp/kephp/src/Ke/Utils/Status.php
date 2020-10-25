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


class Status implements StatusImpl
{

	protected $status;

	public $message;

	public $data = [];

	public function __construct($status, string $message = null, array $data = null)
	{
		$this->setStatus($status);
		if (!empty($message))
			$this->setMessage($message);
		if (!empty($data))
			$this->setData($data);
	}

	public function setStatus($status)
	{
		$this->status = $status;
		return $this;
	}

	public function getStatus()
	{
		return $this->status;
	}

	public function setMessage(string $message, $isPlus = false)
	{
		if ($isPlus)
			$this->message .= $message;
		else
			$this->message = $message;
		return $this;
	}

	public function getMessage()
	{
		return $this->message;
	}

	public function plusMessage(string $message)
	{
		$this->message .= $message;
		return $this;
	}

	public function addData(string $field, $data)
	{
		$this->data[$field] = $data;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	public function setData(array $data, bool $isMerge = true)
	{
		if (empty($this->data) || !$isMerge)
			$this->data = $data;
		else
			$this->data = array_merge($this->data, $data);
		return $this;
	}

	public function mergeData(array $data)
	{
		return $this->setData($data, true);
	}

	public function isSuccess()
	{
		return !!$this->status;
	}

	public function isFailure()
	{
		return !$this->status;
	}

	public function export()
	{
		return [
			'status'  => $this->status,
			'message' => $this->message,
			'data'    => $this->data,
		];
	}

	public function toJSON()
	{
		return json_encode($this->export());
	}
}
