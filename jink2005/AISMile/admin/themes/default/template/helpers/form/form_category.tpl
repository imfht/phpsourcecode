{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if count($categories) && isset($categories)}
	<script type="text/javascript">
		var inputName = '{$categories.input_name}';
		var use_radio = {if $categories.use_radio}1{else}0{/if};
		var selectedCat = '{implode value=$categories.selected_cat}';
		var selectedLabel = '{$categories.trads.selected}';
		var home = '{$categories.trads.Root.name}';
		var use_radio = {if $categories.use_radio}1{else}0{/if};
		var use_context = {if isset($categories.use_context)}1{else}0{/if};
		$(document).ready(function(){
			buildTreeView(use_context);
		});
	</script>

	<div class="category-filter">
		<span><a href="#" id="collapse_all" >{$categories.trads['Collapse All']}</a>
		 |</span>
		 <span><a href="#" id="expand_all" >{$categories.trads['Expand All']}</a>
		{if !$categories.use_radio}
		 |</span>
		 <span></span><a href="#" id="check_all" >{$categories.trads['Check All']}</a>
		 |</span>
		 <span></span><a href="#" id="uncheck_all" >{$categories.trads['Uncheck All']}</a></span>
		 {/if}
		{if $categories.use_search}
			<span style="margin-left:20px">
				{$categories.trads.search} :
				<form method="post" id="filternameForm">
					<input type="text" name="search_cat" id="search_cat">
				</form>
			</span>
		{/if}
	</div>

	{assign var=home_is_selected value=false}

	{foreach $categories.selected_cat AS $cat}
		{if is_array($cat)}
			{if $cat.id_category != $categories.trads.Root.id_category}
				<input {if in_array($cat.id_category, $categories.disabled_categories)}disabled="disabled"{/if} type="hidden" name="{$categories.input_name}" value="{$cat.id_category}" >
			{else}
				{assign var=home_is_selected value=true}
			{/if}
		{else}
			{if $cat != $categories.trads.Root.id_category}
				<input {if in_array($cat, $categories.disabled_categories)}disabled="disabled"{/if} type="hidden" name="{$categories.input_name}" value="{$cat}" >
			{else}
				{assign var=home_is_selected value=true}
			{/if}
		{/if}
	{/foreach}
	<ul id="categories-treeview" class="filetree">
		<li id="{$categories.trads.Root.id_category}" class="hasChildren">
			<span class="folder">
				{if $categories.top_category->id != $categories.trads.Root.id_category}
					<input type="{if !$categories.use_radio}checkbox{else}radio{/if}"
							name="{$categories.input_name}"
							value="{$categories.trads.Root.id_category}"
							{if $home_is_selected}checked{/if}
							onclick="clickOnCategoryBox($(this));" />
						<span class="category_label">{$categories.trads.Root.name}</span>
				{else}
					&nbsp;
				{/if}
			</span>
			<ul>
				<li><span class="placeholder">&nbsp;</span></li>
		  	</ul>
		</li>
	</ul>
	{if $categories.use_radio}
	<script type="text/javascript">
		searchCategory();
	</script>
	{/if}
{/if}
