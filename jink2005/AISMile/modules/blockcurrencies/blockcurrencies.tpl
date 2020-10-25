{**
 * MILEBIZ 米乐商城
 * ============================================================================
 * 版权所有 2011-20__ 米乐网。
 * 网站地址: http://www.milebiz.com
 * ============================================================================
 * $Author: zhourh $
 *}

<!-- Block currencies module -->
<script type="text/javascript">
$(document).ready(function () {
	$("#setCurrency").mouseover(function(){
		$(this).addClass("countries_hover");
		$(".currencies_ul").addClass("currencies_ul_hover");
	});
	$("#setCurrency").mouseout(function(){
		$(this).removeClass("countries_hover");
		$(".currencies_ul").removeClass("currencies_ul_hover");
	});

	$('ul#first-currencies li:not(.selected)').css('opacity', 0.3);
	$('ul#first-currencies li:not(.selected)').hover(function(){
		$(this).css('opacity', 1);
	}, function(){
		$(this).css('opacity', 0.3);
	});
});
</script>

<div id="currencies_block_top">
	<form id="setCurrency" action="{$request_uri}" method="post">
		<p>
			<input type="hidden" name="id_currency" id="id_currency" value=""/>
			<input type="hidden" name="SubmitCurrency" value="" />
			{l s='Currency' mod='blockcurrencies'} : {$blockcurrencies_sign}
		</p>
		<ul id="first-currencies" class="currencies_ul">
			{foreach from=$currencies key=k item=f_currency}
				<li {if $cookie->id_currency == $f_currency.id_currency}class="selected"{/if}>
					<a href="javascript:setCurrency({$f_currency.id_currency});" title="{$f_currency.name}">{$f_currency.sign}</a>
				</li>
			{/foreach}
		</ul>
	</form>
</div>
<!-- /Block currencies module -->
