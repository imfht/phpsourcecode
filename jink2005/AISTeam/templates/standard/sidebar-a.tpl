<div id="content-right">


	{*Search*}
	<div class="content-right-in">
			<h2><a id = "searchtoggle" class="win-up" href="javascript:blindtoggle('search');toggleClass('searchtoggle','win-up','win-down');">{#search#}</a></h2>
			
			<form id = "search" method = "get" action = "managesearch.php" {literal} onsubmit="return validateStandard(this,'input_error');"{/literal}>
			<fieldset>
				<div class = "row">
					<input type="text" class = "text" id="query" name="query" />
				</div>
			
				<div id="choices"></div>
				<input type = "hidden" name = "action" value = "search" />
				
				<div id="indicator1" style="display:none;"><img src="templates/standard/images/symbols/indicator_arrows.gif" alt="{#searching#}" /></div>
				
				<button type="submit" title="{#gosearch#}"></button>
			</fieldset>

			</form>
	</div>
	{*Search End*}
	
	
	{*Calendar*}
	{* theCal.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
	<div class="content-right-in">	
		<h2><a id="mycaltoggle" class="win-up" href="javascript:blindtoggle('mycal_mini');toggleClass('mycaltoggle','win-up','win-down');">{#calendar#}</a></h2>
		<div id = "mycal_mini"></div>
		<script type = "text/javascript">
		theCal = new calendar({$theM},{$theY});
		theCal.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
		theCal.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
		theCal.getCal('mycal_mini');
		</script>
	</div>		
	Calendar End*}
	

	{*Tag Cloud*}	
	{if $showcloud == "1"}
		{if $cloud != ""}
		<div class="content-right-in">
			<h2><a id="tagcloudtoggle" class="win-up" href="javascript:blindtoggle('tagcloud');toggleClass('tagcloudtoggle','win-up','win-down');">{#tags#}</a></h2>
			<div id = "tagcloud" class="cloud">
				{$cloud}
			</div>
		</div>
		{/if}
	{/if}
	{*Tag Cloud End*}
<script type = "text/javascript">{literal}
	new Ajax.Autocompleter('query', 'choices', 'managesearch.php?action=ajaxsearch', {paramName:'query',minChars: 2,indicator: 'indicator1'});
{/literal}</script>
</div>