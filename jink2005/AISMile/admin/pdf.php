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

/**
 * @deprecated 1.5.0
 * This file is deprecated, please use AdminPdfController instead
 */
Tools::displayFileAsDeprecated();

if (!Context::getContext()->employee->id)
	Tools::redirectAdmin('index.php?controller=AdminLogin');

$function_array = array(
	'pdf' => 'generateInvoicePDF',
	'id_order_slip' => 'generateOrderSlipPDF',
	'id_delivery' => 'generateDeliverySlipPDF',
	'delivery' => 'generateDeliverySlipPDF',
	'invoices' => 'generateInvoicesPDF',
	'invoices2' => 'generateInvoicesPDF2',
	'slips' => 'generateOrderSlipsPDF',
	'deliveryslips' => 'generateDeliverySlipsPDF',
	'id_supply_order' => 'generateSupplyOrderFormPDF'
);

$pdf_controller = new AdminPdfController();
foreach ($function_array as $var => $function)
	if (isset($_GET[$var]))
	{
		$pdf_controller->{'process'.$function}();
		exit;
	}

exit;