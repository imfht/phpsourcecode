<?php

/**
 * 异常类
 * 
 * @author ShuangYa
 * @package SYFramework
 * @category Base
 * @link http://www.sylingd.com/
 * @copyright Copyright (c) 2015 ShuangYa
 * @license http://lab.sylingd.com/go.php?name=framework&type=license
 */

namespace sy\base;
use \Sy;

//普通异常类
class SYException extends \Exception {
	public function __construct($message, $code, $file = NULL) {
		$this->message = $message;
		$this->code = $code;
		if ($file !== NULL) {
			$this->file = $file[0];
			$this->line = $file[1];
		}
	}
	public function __toString() {
		return Sy::$isCli ? $this->getText() : $this->getHtml();
	}
	/**
	 * 获取文本错误信息
	 * @access public
	 * @return string
	 */
	public function getText() {
		if (!isset(Sy::$debug) || !Sy::$debug) {
			return $this->getTextNotDebug();
		}
		$r = '[' . $this->getCode() . ']' . $this->getMessage() . '[File: ' . $this->file . '. Line: ' .$this->line;
		return $r;
	}
	/**
	 * 获取HTML错误信息
	 * @access public
	 * @return string
	 */
	public function getHtml() {
		if (!isset(Sy::$debug) || !Sy::$debug) {
			return $this->getHtmlNotDebug();
		}
		$r = '<p><strong>SY Framework</strong></p>';
		$r .= '<p>[' . $this->getCode() . ']' . $this->getMessage();
		$r .= '</p><p>in ' . $this->getFile() . ' on line ' . $this->getLine() . '</p>';
		return $r;
	}
	/**
	 * 非调试模式提示
	 * @access protected
	 * @return string
	 */
	protected function getHtmlNotDebug() {
		$r = '<p><strong>SY Framework</strong></p>';
		$r .= '<p>Please contact to the Admin for helps</p>';
		$r .= '<p>If you are Admin,please enable Debug Mode in config.php</p>';
		return $r;
	}
	protected function getTextNotDebug() {
		$r = 'SY Framework:Please enable Debug Mode in config.php';
		return $r;
	}
}
