{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{capture assign='page_title'}{l s='Page not available'}{/capture}
{include file='./page-title.tpl'}

	{* Submit 脿 tester sur t茅l茅phone *}
	{* ===================================== *}
	<div data-role="content" id="content">
		<div id="not_found">
			<p>{l s='We\'re sorry, but the Web address you entered is no longer available'}</p>
			<p>{l s='To find a product, please type its name in the field below'}</p>
			<div data-role="fieldcontain" class="input_search_404">
				<form action="{$link->getPageLink('search')}" method="post" class="std">
				<input type="search" name="search_query" id="search_query" value="{l s='Search'}" />
				</form>
			</div>
			<p>
				<a href="{$base_dir}" class="lnk_my-account_home" title="{l s='Home'}" data-ajax="false">
					<img class="" alt="{l s='Home'}" src="{$img_mobile_dir}icon/home.png">
					{l s='Home'}
				</a>
			</p>
		</div>
	{* ===================================== *}
	</div><!-- /content -->
