{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}


<li {if isset($node.children) && $node.children|@count > 0 || isset($node.cms) && $node.cms|@count > 0}data-icon="more"{/if}>
	<a href="{$node.link|escape:'htmlall':'UTF-8'}" title="{$node.name|escape:'htmlall':'UTF-8'}" data-ajax="false">{$node.name|escape:'htmlall':'UTF-8'}</a>
	{if isset($node.children) && $node.children|@count > 0}
		<ul data-inset="true">
		{foreach from=$node.children item=child name=categoryCmsTreeBranch}
			{if isset($child.children) && $child.children|@count > 0 || isset($child.cms) && $child.cms|@count > 0}
				{include file="./category-cms-tree-branch.tpl" node=$child}
			{/if}
		{/foreach}
		{if isset($node.cms) && $node.cms|@count > 0}
			{foreach from=$node.cms item=cms name=cmsTreeBranch}
				<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}" data-ajax="false">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
			{/foreach}
		{/if}
		</ul>
	{elseif isset($node.cms) && $node.cms|@count > 0}
		<ul data-inset="true">
		{foreach from=$node.cms item=cms name=cmsTreeBranch}
			<li><a href="{$cms.link|escape:'htmlall':'UTF-8'}" title="{$cms.meta_title|escape:'htmlall':'UTF-8'}" data-ajax="false">{$cms.meta_title|escape:'htmlall':'UTF-8'}</a></li>
		{/foreach}
		</ul>
	{/if}
</li>
