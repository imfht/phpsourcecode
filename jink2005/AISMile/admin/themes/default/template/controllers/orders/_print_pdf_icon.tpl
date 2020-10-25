{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{* Generate HTML code for printing Invoice Icon with link *}
<span style="width:20px; margin-right:5px;">
{if ($order_state->invoice || $order->invoice_number)}
	<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateInvoicePDF&id_order={$order->id}"><img src="../img/admin/tab-invoice.gif" alt="invoice" /></a>
{else}
	-
{/if}
</span>

{* Generate HTML code for printing Delivery Icon with link *}
<span style="width:20px;">
{if ($order_state->delivery || $order->delivery_number)}
	<a target="_blank" href="{$link->getAdminLink('AdminPdf')|escape:'htmlall':'UTF-8'}&submitAction=generateDeliverySlipPDF&id_order={$order->id}"><img src="../img/admin/delivery.gif" alt="delivery" /></a>
{else}
	-
{/if}
</span>
