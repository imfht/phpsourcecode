<?php

/**
 * Created by PhpStorm.
 * Author: William
 * Date: 2016/9/15
 * Time: 21:40
 */
class Config
{
	private $config = array();


	public function get($key)
	{
		if (!empty($this->config[$key])) {
			return $this->config[$key];
		} else {
			return NULL;
		}
	}

	public function set($key, $value)
	{
		$this->config[$key] = $value;
	}

	public function setArray(array $value)
	{
		$this->config = array_merge($this->config, $value);
	}
}