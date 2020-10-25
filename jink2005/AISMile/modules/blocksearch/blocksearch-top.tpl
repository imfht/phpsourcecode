{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<!-- block seach mobile -->
{if isset($hook_mobile)}
<div class="input_search" data-role="fieldcontain">
	<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
		<input type="hidden" name="controller" value="search" />
		<input type="hidden" name="orderby" value="position" />
		<input type="hidden" name="orderway" value="desc" />
		<input class="search_query" type="search" id="search_query_top" name="search_query" placeholder="{l s='Search' mod='blocksearch'}" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
	</form>
</div>
{else}
<!-- Block search module TOP -->
<div id="search_block_top">

	<form method="get" action="{$link->getPageLink('search')}" id="searchbox">
		<p>
			<label for="search_query_top"><!-- image on background --></label>
			<input type="hidden" name="controller" value="search" />
			<input type="hidden" name="orderby" value="position" />
			<input type="hidden" name="orderway" value="desc" />
			<input class="search_query" type="text" id="search_query_top" name="search_query" value="{if isset($smarty.get.search_query)}{$smarty.get.search_query|htmlentities:$ENT_QUOTES:'utf-8'|stripslashes}{/if}" />
			<input type="submit" name="submit_search" value="{l s='Search' mod='blocksearch'}" class="button" />
	</p>
	</form>
</div>
{include file="$self/blocksearch-instantsearch.tpl"}
{/if}
<!-- /Block search module TOP -->
