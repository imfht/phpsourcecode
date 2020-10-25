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
use \sy\base\SYException;

//数据库相关异常
class SYDException extends SYException {
	protected $dbtype;
	protected $dbname;
	protected $execute;
	public function __construct($message, $dbtype, $execute = '') {
		$this->message = $message;
		$this->dbtype = $dbtype;
		$this->execute = $execute;
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
		$r = 'SY Framework:';
		$r .= '[' . $this->dbtype . ']';
		$r .= 'Message:' . $this->getMessage() . ' | ';
		$r .= 'Execute:' . $this->execute;
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
		$r .= '<p>Something wrong with ' . $this->dbtype . '...</p>';
		$r .= '<p>Error info: ' . $this->getMessage() . '</p>';
		$r .= '<p>Execute: ' . $this->execute . '</p>';
		$r .= '<p>in ' . $this->getFile() . ' on line ' . $this->getLine() . '</p>';
		return $r;
	}
}