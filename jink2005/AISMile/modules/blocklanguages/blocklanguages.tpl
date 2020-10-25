{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block languages module -->
{if count($languages) > 1}
<div id="languages_block_top">
	<div id="countries">
	{* @todo fix display current languages, removing the first foreach loop *}
{foreach from=$languages key=k item=language name="languages"}
	{if $language.iso_code == $lang_iso}
		<p class="selected_language">
			<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />
		</p>
	{/if}
{/foreach}
		<ul id="first-languages" class="countries_ul">
		{foreach from=$languages key=k item=language name="languages"}
			<li {if $language.iso_code == $lang_iso}class="selected_language"{/if}>
			{if $language.iso_code != $lang_iso}
				{assign var=indice_lang value=$language.id_lang}
				{if isset($lang_rewrite_urls.$indice_lang)}
					<a href="{$lang_rewrite_urls.$indice_lang|escape:htmlall}" title="{$language.name}">
				{else}
					<a href="{$link->getLanguageLink($language.id_lang)|escape:htmlall}" title="{$language.name}">

				{/if}
			{/if}
					<img src="{$img_lang_dir}{$language.id_lang}.jpg" alt="{$language.iso_code}" width="16" height="11" />
			{if $language.iso_code != $lang_iso}
				</a>
			{/if}
			</li>
		{/foreach}
		</ul>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function () {
	$("#countries").mouseover(function(){
		$(this).addClass("countries_hover");
		$(".countries_ul").addClass("countries_ul_hover");
	});
	$("#countries").mouseout(function(){
		$(this).removeClass("countries_hover");
		$(".countries_ul").removeClass("countries_ul_hover");
	});

});
</script>
{/if}
<!-- /Block languages module -->
