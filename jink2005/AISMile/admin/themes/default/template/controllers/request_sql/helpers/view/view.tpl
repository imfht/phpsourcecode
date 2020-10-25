{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/view/view.tpl"}

{block name="override_tpl"}
	{if isset($view['error'])}
		<p>{l s='This query has no result.'}</p>
	{else}
		<h2>{$view['name']}</h2>
		<table cellpadding="0" cellspacing="0" class="table" id="viewRequestSql">
			<thead>
				<tr>
					{foreach $view['key'] AS $key}
						<th align="center">{$key}</th>
					{/foreach}
				</tr>
			</thead>
			<tbody>
			{foreach $view['results'] AS $result}
				<tr>
					{foreach $view['key'] AS $name}
						{if isset($view['attributes'][$name])}
							<td>{$view['attributes'][$name]}</td>
						{else}
							<td>{$result[$name]}</td>
						{/if}
					{/foreach}
				</tr>
			{/foreach}
			</tbody>
		</table>
	
		<script type="text/javascript">
			$(function(){
				var width = $('#viewRequestSql').width();
				if (width > 990){
					$('#viewRequestSql').css('display','block').css('overflow-x', 'scroll');
				}
			});
		</script>
	{/if}
{/block}

