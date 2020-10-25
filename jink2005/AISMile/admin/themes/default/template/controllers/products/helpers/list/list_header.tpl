{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/list/list_header.tpl"}

{block name=leadin}
	{if isset($category_tree)}
		<script type="text/javascript">
			$(document).ready(function(){
				var base_url = '{$base_url}';
				// Load category products page when category is clicked
				$('#categories-treeview :input').live('click', function(){
					if (this.value !== "")
						location.href = base_url + '&id_category=' + parseInt(this.value);
					else
						location.href = base_url;
				});

				// Make sure the checkbox is checked/unchecked when the link is clicked
				$('#toggle_category_tree').bind('click', function(event){
					event.preventDefault();
					$('#block_category_tree').toggle();
					if ($('#block_category_tree').is(':visible'))
						$(this).find('input').attr('checked', true);
					else
					{
						$(this).find('input').removeAttr('checked');
						location.href = base_url;
					}
				});
			});

		</script>
		<div class="bloc-leadin">
			<div id="container_category_tree">
				<a href="#" id="toggle_category_tree">
					<form>
						<input type="checkbox" {if $is_category_filter}checked="checked"{/if} />{l s='Filter by category'}
					</form>
				</a>
				<div id="block_category_tree" {if !$is_category_filter}style="display:none"{/if}>
					<br />
					{$category_tree}
				</div>
			</div>
		</div>
	{/if}
{/block}
