{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav"><a href="index.php"><img alt="home" src="templates/{$settings.template}/images/home.png" /></a><a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:32:"...":true}</a></div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<script type="text/javascript">
{literal}
etcrequired = {};
{/literal}
	
{section name = task loop=$ptasks}
	etcrequired[ {$ptasks[task].ID} ] = {$ptasks[task].optionaletc}; 
{/section}
	
{literal}
function setRequiredEtc(value) {
	if (value > 0) {
		var e = document.getElementById('efforttocomplete');
		var required = etcrequired[value];
		e.setAttribute('required', required);
	}
}
function checkEtc(obj) {
	var e = document.getElementById('efforttocomplete');
	if(parseInt(e.value) == 0) {
		if(confirm("Do you want to close the task?")) {
				return validateCompleteForm(obj,'input_error')
		} else {
			e.value = '';
		}
	}
	return validateCompleteForm(obj,'input_error')
}
{/literal}
</script>
<div id="content">
	<form id="trackeradd" method="post" action="managetimetracker.php?action=add" onsubmit="return checkEtc(this)">
	<input type="hidden" name="redirect_tracker" value="" />
	<input type="hidden" name="project" value="{$project.ID}" />
		<ul class="pageitem">
			<li class="smallfield"><span class="name">{#day#}</span><input placeholder="dd.MM.YYYY" type="text" id="ttday" name="ttday" realname="{#date#}" value="{"d.m.Y"|date}" /></li>
			<li class="smallfield"><span class="name">{#started#}</span><input placeholder="HH:mm" type="text" id="started" name="started" required="1" regexp="^\d\d:\d\d$" realname="{#started#} (Format: hh:mm)" /></li>
			<li class="smallfield"><span class="name">{#ended#}</span><input placeholder="HH:mm" type="text" id="ended" name="ended" required="1" regexp="^\d\d:\d\d$" realname="{#ended#} (Format: hh:mm)" /></li>
			<li class="textbox"><span class="header">{#comment#}</span><textarea name="comment" rows="4"></textarea></li>
			<li class="select">
			<select name="ttask" id="ttask" onchange="setRequiredEtc(this.value);">
				<option value="0">{#chooseone#} {#task#}</option>
			{section name=task loop=$ptasks}
				{if $ptasks[task].title != ""}
				<option value="{$ptasks[task].ID}">{$ptasks[task].title}</option>
				{else}
				<option value="{$ptasks[task].ID}">{$ptasks[task].text|truncate:30:"...":true}</option>
				{/if}
			{/section}
	  	</select>
			<span class="arrow"></span>
			</li>
			<li class="smallfield"><span class="name">{#efforttocomplete#}</span><input type="text" id="efforttocomplete" name="efforttocomplete" required="1" realname="{#efforttocomplete#}" regex="{literal}\d+(\.\d+)?{/literal}" /></li>
			<li class="button"><input type="submit" value="{#addbutton#}" /></li>
		</ul>
		{if $loginerror == 1}<ul class="pageitem"><li class="textbox error">{#loginerror#}</li></ul>{/if}
	</form>
</div>
{include file="footer.tpl"}