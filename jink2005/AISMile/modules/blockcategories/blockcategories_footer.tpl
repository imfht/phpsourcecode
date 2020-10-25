{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block categories module -->
<div class="blockcategories_footer">
	<h4>{l s='Categories' mod='blockcategories'}</h4>
<div class="category_footer" style="float:left;clear:none;width:{$widthColumn}%">
	<div style="float:left" class="list">
		<ul class="tree {if $isDhtml}dhtml{/if}">
	
		{foreach from=$blockCategTree.children item=child name=blockCategTree}
			{if $smarty.foreach.blockCategTree.last}
				{include file="$branche_tpl_path" node=$child last='true'}
			{else}
				{include file="$branche_tpl_path" node=$child}
			{/if}
		
			{if ($smarty.foreach.blockCategTree.iteration mod $numberColumn) == 0 AND !$smarty.foreach.blockCategTree.last}
		</ul>
	</div>
</div>

<div class="category_footer" style="float:left;clear:none;width:{$widthColumn}%">
		<div style="float:left" class="list">
		<ul class="tree {if $isDhtml}dhtml{/if}">
			{/if}
			{/foreach}
		</ul>
	</div>
</div>
<br class="clear"/>
</div>
<!-- /Block categories module -->
