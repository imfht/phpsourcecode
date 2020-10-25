{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<div id="productBox">

	{include file='controllers/modules/header.tpl'}
	{include file='controllers/modules/filters.tpl'}

	<ul class="view-modules">
		<li class="button normal-view-disabled"><img src="themes/default/img/modules_view_layout_sidebar.png" alt="{l s='Normal view'}" border="0" /><span>{l s='Normal view'}</span></li>
		<li class="button favorites-view"><a  href="index.php?controller={$smarty.get.controller|htmlentities}&token={$smarty.get.token|htmlentities}&select=favorites"><img src="themes/default/img/modules_view_table_select_row.png" alt="{l s='Favorites view'}" border="0" /><span>{l s='Favorites view'}</span></a></li>
	
	</ul>

	<div id="container">
		<!--start sidebar module-->
		<div class="sidebar">
			<div class="categorieTitle">
				<h3>{l s='Categories'}</h3>
				<div class="subHeadline">&nbsp;</div>
				<ul class="categorieList">
					<li {if isset($categoryFiltered.favorites)}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&filterCategory=favorites"><span><b>{l s='Favorites'}</b></span></a></div>
							<div class="count"><b>{$nb_modules_favorites}</b></div>
					</li>
					<li {if count($categoryFiltered) lte 0}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&unfilterCategory=yes"><span><b>{l s='Total'}</b></span></a></div>
							<div class="count"><b>{$nb_modules}</b></div>
					</li>
					{foreach from=$list_modules_categories item=module_category key=module_category_key}
						<li {if isset($categoryFiltered[$module_category_key])}style="background-color:#EBEDF4"{/if} class="categoryModuleFilterLink">
							<div class="categorieWidth"><a href="{$currentIndex}&token={$token}&{if isset($categoryFiltered[$module_category_key])}un{/if}filterCategory={$module_category_key}"><span>{$module_category.name}</span></a></div>
							<div class="count">{$module_category.nb}</div>
						</li>
					{/foreach}
				</ul>
			</div>
		</div>

		<div id="moduleContainer">
			{include file='controllers/modules/list.tpl'}
		</div>
	</div>

</div>
