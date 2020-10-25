{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
{if $infos|@count > 0}
<!-- MODULE Block reinsurance -->
<div id="reinsurance_block" class="clearfix">
	<ul class="width{$nbblocks}">	
		{foreach from=$infos item=info}
			<li><img src="{$module_dir}img/{$info.file_name}" {if $info.text!= ''}alt="{$info.text|escape:html:'UTF-8'}"{/if} /> {if $info.text!= ''}<span>{$info.text|escape:html:'UTF-8'}</span>{/if}</li>
		{/foreach}
	</ul>
</div>
<!-- /MODULE Block reinsurance -->
{/if}