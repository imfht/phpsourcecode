<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

interface WebserviceSpecificManagementInterface
{
	public function setObjectOutput(WebserviceOutputBuilderCore $obj);
	public function getObjectOutput();
	public function setWsObject(WebserviceRequestCore $obj);
	public function getWsObject();

	public function manage();

	/**
	 * This must be return an array with specific values as WebserviceRequest expects.
	 *
	 * @return array
	 */
	public function getContent();
}