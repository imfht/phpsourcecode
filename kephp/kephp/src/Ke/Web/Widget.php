<?php
/**
 * KePHP, Keep PHP easy!
 *
 * @license   https://opensource.org/licenses/MIT
 * @copyright Copyright 2015 - 2020 KePHP Authors All Rights Reserved
 * @link      http://kephp.com ( https://git.oschina.net/kephp/kephp-core )
 * @author    曾建凯 <janpoem@163.com>
 */


namespace Ke\Web;

/**
 * @package Ke\Web
 */
class Widget
{

	protected $isRender = false;

	protected $publicOptions = [];

	public function setOption(string $key, $option)
	{
		// !(false, 0, null, '')
		if (!empty($this->publicOptions[$key])) {
			$handle = $this->publicOptions[$key];
			if (is_string($handle) && is_callable([$this, $handle])) {
				$this->{$handle}($option);
			}
			else {
				$this->{$key} = $option;
			}
		}
		return $this;
	}

	public function setOptions($options)
	{
		if (is_array($options) || is_object($options)) {
			foreach ($options as $key => $value) {
				$this->setOption($key, $value);
			}
		}
		return $this;
	}

//	public function getRenderContent(): string
//	{
//		return '';
//	}
//
//	protected function onRender()
//	{
//	}
//
//	/**
//	 * @return Widget|static
//	 */
//	public function render()
//	{
//		$this->onRender();
//		print $this->getRenderContent();
//		$this->isRender = true;
//		return $this;
//	}
}