<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

abstract class InstallAbstractModel
{
	/**
	 * @var InstallLanguages
	 */
	public $language;

	/**
	 * @var array List of errors
	 */
	protected $errors = array();

	public function __construct()
	{
		$this->language = InstallLanguages::getInstance();
	}

	public function setError($errors)
	{
		if (!is_array($errors))
			$errors = array($errors);

		$this->errors[] = $errors;
	}

	public function getErrors()
	{
		return $this->errors;
	}
}
