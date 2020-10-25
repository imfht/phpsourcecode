{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<li {if $node.children|@count > 0}data-icon="more"{/if}>
	{if $node.children|@count > 0}
		{$node.name|escape:'htmlall':'UTF-8'}
		<ul data-inset="true">
			<li>
				<a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.desc|escape:'htmlall':'UTF-8'}" data-ajax="false">
					{l s='See products'}
				</a>
			</li>
		{foreach from=$node.children item=child name=categoryTreeBranch}
			{include file="$tpl_dir./category-tree-branch.tpl" node=$child}
		{/foreach}
		</ul>
	{else}
		<a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.desc|escape:'htmlall':'UTF-8'}" data-ajax="false">
			{$node.name|escape:'htmlall':'UTF-8'}
		</a>
	{/if}
</li>
