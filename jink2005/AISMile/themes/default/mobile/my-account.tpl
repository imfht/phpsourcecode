{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{l s='My account'}{/capture}
{include file='./page-title.tpl'}

<div data-role="content" id="content">
		<p>{l s='Welcome to your account. Here, you can manage your addresses and orders.'}</p>
		
		<ul data-role="listview" data-inset="true" id="list_myaccount">
			{if $has_customer_an_address}
			<li>
				<a href="{$link->getPageLink('address', true)}" title="{l s='Add my first address'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/addrbook.png" alt="{l s='Addresses'}" class="ui-li-icon ui-li-thumb" />
					{l s='Add my first address'}
				</a>
			</li>
			{/if}
			<li>
				<a href="{$link->getPageLink('history', true)}" title="{l s='Orders'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/order.png" alt="{l s='Orders'}" class="ui-li-icon ui-li-thumb" />
					{l s='History and details of my orders'}
				</a>
			</li>
			{if $returnAllowed}
			<li>
				<a href="{$link->getPageLink('order-follow', true)}" title="{l s='Merchandise returns'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/return.png" alt="{l s='Merchandise returns'}" class="ui-li-icon ui-li-thumb" />
					{l s='My merchandise returns'}
				</a>
			</li>
			{/if}
			<li>
				<a href="{$link->getPageLink('order-slip', true)}" title="{l s='Credit slips'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/slip.png" alt="{l s='Credit slips'}" class="ui-li-icon ui-li-thumb" />
					{l s='My credit slips'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('addresses', true)}" title="{l s='Addresses'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/addrbook.png" alt="{l s='Addresses'}" class="ui-li-icon ui-li-thumb" />
					{l s='My addresses'}
				</a>
			</li>
			<li>
				<a href="{$link->getPageLink('identity', true)}" title="{l s='Information'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/userinfos.png" alt="{l s='Information'}" class="ui-li-icon ui-li-thumb" />
					{l s='My personal information'}
				</a>
			</li>
			{if $voucherAllowed}
			<li>
				<a href="{$link->getPageLink('discount', true)}" title="{l s='Vouchers'}" data-ajax="false">
					<img src="{$img_mobile_dir}icon/voucher.png" alt="{l s='Vouchers'}" class="ui-li-icon ui-li-thumb" />
					{l s='My vouchers'}
				</a>
			</li>
			{/if}
			<li data-icon="delete" data-theme="a">
				<a href="{$link->getPageLink('index', true)}?mylogout" title="{l s='Sign out'}" data-ajax="false">
					{l s='Sign out'}
				</a>
			</li>
			{* Un hook est dans la liste (pour favoris et wishlist) *}
			{* ===================================== *}
			{hook h="mobileCustomerAccount"}
			{* ===================================== *}
		</ul>

		<a href="{$base_dir}" class="lnk_my-account_home" title="{l s='Home'}" data-ajax="false">
			<img class="" alt="{l s='Home'}" src="{$img_mobile_dir}icon/home.png">
			{l s='Home'}
		</a>
		{include file='./sitemap.tpl'}
	</div><!-- /content -->
