{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

		<hr width="99%" align="center" size="2" class=""/>
		<h2 class="site_map">{l s='Sitemap'}</h2>
		<ul data-role="listview" data-inset="true" id="category">
			{if $controller_name != 'index'}<li><a href="{$link->getPageLink('index', true)}" data-ajax="false">Accueil</a></li>{/if}
			
			{* need to set a Hook : hookMobilePagesList *}
			{* ===================================== *}
			<li><a href="{$link->getCategoryLink(3, false)}" data-ajax="false">IPod</a></li>
			<li><a href="{$link->getCategoryLink(4, false)}" data-ajax="false">Accessoires</a></li>
			{* ===================================== *}
			
			{if $controller_name != 'my-account'}<li><a href="{$link->getPageLink('my-account', true)}" data-ajax="false">{l s='My account'}</a></li>{/if}
			{if $controller_name != 'contact'}<li><a href="{$link->getPageLink('contact', true)}" data-ajax="false">{l s='Contact'}</a></li>{/if}
		</ul>
