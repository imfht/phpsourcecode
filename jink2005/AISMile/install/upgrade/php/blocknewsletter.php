<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

function blocknewsletter()
{
	// No one will know if the table does not exist :]
	DB::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'newsletter ADD `http_referer` VARCHAR(255) NULL');
}

