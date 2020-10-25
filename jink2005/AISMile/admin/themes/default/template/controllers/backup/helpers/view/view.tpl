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

	<div class="conf">{l s='Beginning download ...'}</div>

	<p>{l s='Backup file should automatically download.'}</p>

	<p>{l s='If not,'} <b><a href="{$url_backup}">{l s='please click here!'}</a></b></p>

	<iframe width="0" height="0" scrolling="no" frameborder="0" src="{$url_backup}"></iframe>

{/block}


