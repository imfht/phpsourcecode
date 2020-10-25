{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block permanent links module -->
<div id="permanent_links">
	<!-- Sitemap -->
	<div class="sitemap">
		<a href="{$link->getPageLink('sitemap')}">{l s='sitemap' mod='blockpermanentlinks'}</a>
	</div>
	<!-- Contact -->
	<div class="contact">
		<a href="{$link->getPageLink('contact', true)}">{l s='contact' mod='blockpermanentlinks'}</a>
	</div>
	<!-- Bookmark -->
	<div class="add_bookmark" style="height:30px;">
		<script type="text/javascript">
		writeBookmarkLink('{$come_from}', '{$shop_name|addslashes|addslashes}', '{l s='bookmark this page' mod='blockpermanentlinks'}');</script>&nbsp;
	</div>
</div>
<!-- /Block permanent links module -->