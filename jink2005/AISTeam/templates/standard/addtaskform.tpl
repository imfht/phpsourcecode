{if $showhtml != "no"}
{include file="header.tpl" jsload = "ajax" jsload1 = "tinymce" }

{include file="tabsmenue-project.tpl" taskstab = "active"}
<div id="content-left">
<div id="content-left-in">
<div class="tasks">

<div class="breadcrumb">
<a href="manageproject.php?action=showproject&amp;id={$project.ID}" title="{$projectname}"><img src="./templates/standard/images/symbols/projects.png" alt="" />{$projectname|truncate:25:"...":true}</a>
<a href="managetask.php?action=showproject&amp;id={$project.ID}"><img src="./templates/standard/images/symbols/tasklist.png" alt="" />{#tasklists#}</a>
</div>

{/if}
				<div class="block_in_wrapper">

	<h2>{#addtask#}</h2>
	<form class="main" method="post" action="managetask.php?action=add&amp;id={$project.ID}" onsubmit="if(validateCompleteForm(this,'input_error')) return verifyTaskDates('end'); else return false;">
	<fieldset>

	<div class="row"><label for="title">{#title#}:</label><input type="text" class="text" name="title" id="title"  realname = "{#title#}" required = "1"  /></div>
	<div class="row"><label for="text">{#text#}:</label><div class="editor"><textarea name="text" id="text" rows="3" cols="1" ></textarea></div></div>
	<div class="row"><label for="end">{#finishdate#}:</label><input type="text" class="text" name="end" realname="{#finishdate#}"  id="end" required = "1" value = "{$day}.{$month}.{$year}" date="" /></div>
	
	<div class="row"><label for="efforttocomplete">{#efforttocomplete#}:</label>
		<input type="text" style="width:70px;" class="text" name="efforttocomplete" id="efforttocomplete" realname = "{#efforttocomplete#}" realname="{#planeffort#}" regexp="{literal}\d+{/literal}" />
		<input type="checkbox" class="checkbox" style="margin:7px 4px 0 45px;" name="optionaletc" id="optionaletc" realname = "{#optionaletc#}" checked="checked" />
		<label for="optionaletc" style="width:225px;padding-left:5px;">{#optionaletc#}</label>
		<br/><br/>
	</div>

	<div class="datepick">
		<div id = "datepicker_addtask" class="picker" style = "display:none;"></div>
	</div>
<input type="hidden" id="workpackage_date" name="workpackage_date" value="" />
<script type="text/javascript">
theCal{$lists[list].ID} = new calendar({$theM},{$theY});
theCal{$lists[list].ID}.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
theCal{$lists[list].ID}.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
theCal{$lists[list].ID}.relateTo = "end{$lists[list].ID}";
theCal{$lists[list].ID}.dateFormat = "{$settings.dateformat}";
theCal{$lists[list].ID}.getDatepicker("datepicker_addtask");

{literal}
function setWorkpackageDate(obj){
	document.getElementById("end").setAttribute('date', obj.options[obj.selectedIndex].getAttribute('date'));
}
{/literal}
</script>
  <div class="row"><label for="tasklist">{#tasklist#}:</label>
	    <select name="tasklist" id="tasklist" required = "1" exclude = "-1" realname = "{#tasklist#}" onchange="setWorkpackageDate(this)">
	    <option value="-1" selected="selected">{#chooseone#}</option>
	    {section name=tasklist loop=$tasklists}
	    <option value="{$tasklists[tasklist].ID}" date="{"Ymd"|date:$tasklists[tasklist].finishdate}">{$tasklists[tasklist].name}</option>
	    {/section}
	    </select>
    </div>
	<div class="row">
		<label for="assigned" >{#assignto#}:</label>
		<select name = "assigned[]" id="assigned" required = "1" exclude = "-1" realname = "{#assignto#}">
			<option value="-1" selected="selected">{#chooseone#}</option>
		    <option value="0">{#all#}</option>
			{section name=user loop=$assignable_users}
				<option value="{$assignable_users[user].ID}">{$assignable_users[user].name}</option>
			{/section}
		</select>
	</div>

	<div class="row-butn-bottom">
		<label>&nbsp;</label>
		<button type="submit" onfocus="this.blur();">{#addbutton#}</button>
		<button onclick="blindtoggle('form_{$lists[list].ID}');toggleClass('add_{$lists[list].ID}','add-active','add');toggleClass('add_butn_{$lists[list].ID}','butn_link_active','butn_link');toggleClass('sm_{$lists[list].ID}','smooth','nosmooth');return false;" onfocus="this.blur();">{#cancel#}</button>
	</div>

	</fieldset>
	</form>

</div> {*block_in_wrapper end*}



{if $showhtml != "no"}
<div class="content-spacer"></div>
</div> {*Tasks END*}
</div> {*content-left-in END*}
</div> {*content-left END*}

{include file="sidebar-a.tpl" showcloud="1"}
{include file="footer.tpl"}
{/if}