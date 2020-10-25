{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<table>
	<tr><td style="line-height: 6px">&nbsp;</td></tr>
</table>
	
<table style="width: 100%">
<tr>
	<td style="width: 50%">
        {if $logo_path}
            <img src="{$logo_path}" />
        {/if}
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%">{$shop_name|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #444; font-weight: bold;">{$date|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #444; font-weight: bold;">{$title|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #444; font-weight: bold;">{$reference|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

