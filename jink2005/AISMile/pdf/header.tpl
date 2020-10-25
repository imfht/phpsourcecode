{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}
<table style="width: 100%">
<tr>
	<td style="width: 50%">
		{if $logo_path}
			<img src="{$logo_path}" style="width:{$width_logo}px; height:{$height_logo}px;" />
		{/if}
	</td>
	<td style="width: 50%; text-align: right;">
		<table style="width: 100%">
			<tr>
				<td style="font-weight: bold; font-size: 14pt; color: #444; width: 100%">{$shop_name|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$date|escape:'htmlall':'UTF-8'}</td>
			</tr>
			<tr>
				<td style="font-size: 14pt; color: #9E9F9E">{$title|escape:'htmlall':'UTF-8'}</td>
			</tr>
		</table>
	</td>
</tr>
</table>

