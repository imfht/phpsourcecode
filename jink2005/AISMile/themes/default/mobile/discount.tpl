{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{l s='My Vouchers'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
	<a data-role="button" data-icon="arrow-l" data-theme="a" data-mini="true" data-inline="true" href="{$link->getPageLink('my-account', true)}" data-ajax="false">{l s='My account'}</a>
	
	{if isset($discount) && count($discount) && $nbDiscounts}
	<table class="discount std table_block">
		<thead>
			<tr>
				<th class="discount_code first_item">{l s='Code'}</th>
				<th class="discount_description item">{l s='Description'}</th>
				<th class="discount_quantity item">{l s='Quantity'}</th>
				<th class="discount_value item">{l s='Value'}*</th>
				<th class="discount_minimum item">{l s='Minimum'}</th>
				<th class="discount_cumulative item">{l s='Cumulative'}</th>
				<th class="discount_expiration_date last_item">{l s='Expiration date'}</th>
			</tr>
		</thead>
		<tbody>
		{foreach from=$discount item=discountDetail name=myLoop}
			<tr class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
				<td class="discount_code">{$discountDetail.name}</td>
				<td class="discount_description">{$discountDetail.description}</td>
				<td class="discount_quantity">{$discountDetail.quantity_for_user}</td>
				<td class="discount_value">
					{if $discountDetail.id_discount_type == 1}
						{$discountDetail.value|escape:'htmlall':'UTF-8'}%
					{elseif $discountDetail.id_discount_type == 2}
						{convertPrice price=$discountDetail.value}
					{else}
						{l s='Free shipping'}
					{/if}
				</td>
				<td class="discount_minimum">
					{if $discountDetail.minimal == 0}
						{l s='none'}
					{else}
						{convertPrice price=$discountDetail.minimal}
					{/if}
				</td>
				<td class="discount_cumulative">
					{if $discountDetail.cumulable == 1}
						<img src="{$img_dir}icon/yes.gif" alt="{l s='Yes'}" class="icon" /> {l s='Yes'}
					{else}
						<img src="{$img_dir}icon/no.gif" alt="{l s='No'}" class="icon" valign="middle" /> {l s='No'}
					{/if}
				</td>
				<td class="discount_expiration_date">{dateFormat date=$discountDetail.date_to}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
	<p>
		*{l s='Tax included'}
	</p>
	{else}
		<p class="warning">{l s='You do not possess any vouchers.'}</p>
	{/if}
	
</div><!-- /content -->
