<?php
/**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 */

define('_PS_ADMIN_DIR_', getcwd());

include(_PS_ADMIN_DIR_.'/../config/config.inc.php');

if (!isset(Context::getContext()->employee) || !Context::getContext()->employee->isLoggedBack())
	die;

if (isset($_FILES['virtual_product_file']) AND is_uploaded_file($_FILES['virtual_product_file']['tmp_name']) AND 
(isset($_FILES['virtual_product_file']['error']) AND !$_FILES['virtual_product_file']['error'])	OR 
(!empty($_FILES['virtual_product_file']['tmp_name']) AND $_FILES['virtual_product_file']['tmp_name'] != 'none'))
{
	$filename = $_FILES['virtual_product_file']['name'];
	$file = $_FILES['virtual_product_file']['tmp_name'];
	$newfilename = ProductDownload::getNewFilename();

	if (!copy($file, _PS_DOWNLOAD_DIR_.$newfilename))
	{
		header('HTTP/1.1 500 Error');
		echo '<return result="error" msg="No permissions to write in the download folder" filename="'.Tools::safeOutput($filename).'" />';
	}
	@unlink($file);

	header('HTTP/1.1 200 OK');
	echo '<return result="success" msg="'.Tools::safeOutput($newfilename).'" filename="'.Tools::safeOutput($filename).'" />';
}
else
{
	header('HTTP/1.1 500 Error');
	echo '<return result="error" msg="Could not upload file" filename="'.Tools::safeOutput(ProductDownload::getNewFilename()).'" />';
}
