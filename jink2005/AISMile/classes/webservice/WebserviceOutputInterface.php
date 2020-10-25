<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

interface WebserviceOutputInterface
{
	public function __construct($languages = array());
	public function setWsUrl($url);
	public function getWsUrl();
	public function getContentType();
	public function setSchemaToDisplay($schema);
	public function getSchemaToDisplay();
	public function renderField($field);
	public function renderNodeHeader($obj, $params ,$more_attr = null);
	public function renderNodeFooter($obj, $params);
	public function renderAssociationHeader($obj, $params, $assoc_name);
	public function renderAssociationFooter($obj, $params, $assoc_name);
	public function overrideContent($content);
	public function renderErrorsHeader();
	public function renderErrorsFooter();
	public function renderErrors($message, $code = null);
}