{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

{extends file="helpers/list/list_header.tpl"}

{block name="override_header"}

	<fieldset>
		<legend>{l s='Severity levels'}</legend>
		<p>{l s='Meaning of severity levels:'}</p>
		<ol style="margin-left: 30px; list-style-type: decimal;">
			<li style="color: green;">{l s='Informative only'}</li>
			<li style="color: orange;">{l s='Warning'}</li>
			<li style="color: orange;">{l s='Error'}</li>
			<li style="color: red;">{l s='Major issue (crash)'}</li>
		</ol>
	</fieldset><br />

{/block}