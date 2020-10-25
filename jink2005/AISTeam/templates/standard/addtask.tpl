{if isset($workpackage)}
	{assign var=end_date value=$workpackage.finishdate}
{else}
	{assign var=end_date value=$lists[list].finishdate}
{/if}
<div class="block_in_wrapper">

	<h2>{#addtask#}</h2>
	<form name = "addtaskform{$lists[list].ID}" id = "addtaskform{$lists[list].ID}" class="main" method="post" action="managetask.php?action=add&amp;id={$project.ID}" onsubmit=" if(validateCompleteForm(this,'input_error')) return verifyTaskDates('end{$lists[list].ID}'); else return false;">
	<fieldset>

	<div class="row"><label for="title">{#title#}:</label><input type="text" class="text" name="title" id="title"  realname = "{#title#}" required = "1"  /></div>
	<div class="row"><label for="text">{#text#}:</label><div class="editor"><textarea name="text" id="text" rows="3" cols="1"></textarea></div></div>
	<div class="row"><label for="end{$lists[list].ID}">{#due#}:</label><input type="text" value="{$tasklist.finishdate}" class="text" name="end" realname="{#due#}" id="end{$lists[list].ID}" required = "1" date="{"Ymd"|date:$end_date}" /></div>
    
    <div class="row"><label for="efforttocomplete">{#efforttocomplete#}:</label>
        <input type="text" style="width:70px;" class="text" name="efforttocomplete" id="efforttocomplete" realname = "{#efforttocomplete#}" realname="{#planeffort#}" regexp="{literal}\d+{/literal}" />
        <input type="checkbox" class="checkbox" style="margin:7px 4px 0 45px;" name="optionaletc" id="optionaletc" realname = "{#optionaletc#}" checked="checked" />
        <label for="optionaletc" style="width:225px;padding-left:5px;">{#optionaletc#}</label>
        <br/><br/>
    </div>

	<div class="datepick">
		<div id = "datepicker_task{$lists[list].ID}" class="picker" style = "display:none;"></div>
	</div>

<script type="text/javascript">
theCal{$lists[list].ID} = new calendar({$theM},{$theY});
theCal{$lists[list].ID}.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
theCal{$lists[list].ID}.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
theCal{$lists[list].ID}.relateTo = "end{$lists[list].ID}";
theCal{$lists[list].ID}.dateFormat = "{$settings.dateformat}";
theCal{$lists[list].ID}.getDatepicker("datepicker_task{$lists[list].ID}");
//document.getElementById("end{$lists[list].ID}").value = "{$tasklist.finishdate}";
</script> 

    <div class="row"><label for="priority">{#priority#}</label>
        <select name="priority" id="priority">
	        <option value="0">Low</option>
	        <option value="1" selected="selected">Medium</option>
	        <option value="2">High</option>
        </select>
		<!-- script>
		
		window.dhx_globalImgPath = "include/slider/imgs/";
		
		</script>
		<script  src="include/slider/dhtmlxcommon.js"></script>
		<script  src="include/slider/dhtmlxslider.js"></script>
		<script  src="include/slider/ext/dhtmlxslider_start.js"></script>
		<link rel="stylesheet" type="text/css" href="include/slider/dhtmlxslider.css">    
		 
		<div id="sliderBox"></div-->
		{literal}
		<script>
		/*
		var slider = new dhtmlxSlider("sliderBox", {
		    skin: "dhx_skyblue",
		    min: 0,
		    max: 100,
		    step: 50,
		    size: 260,
		    vertical: false
		});
		//slider.attachEvent("onChange",function(nv){document.getElementById("priority").value=nv;
		slider.init();
		
		var slider = new dhtmlxSlider("sliderBox", 260, "dhx_skyblue");
		slider.setImagePath("codebase/imgs/");
		slider.setStep(50);
		slider.attachEvent("onChange",function(nv){document.getElementById("qual").value=nv;});
		slider.init();*/
		</script> 
		{/literal}
    </div>
    
	<div class="row">
		<label for="assigned" >{#assignto#}:</label>
		<select name="assigned[]" multiple="multiple" style = "height:80px;" id="assigned" required = "1" exclude = "-1" realname = "{#assignto#}" >
			<option value="-1" selected="selected">{#chooseone#}</option>
			{section name=user loop=$assignable_users}
				<option value="{$assignable_users[user].ID}">{$assignable_users[user].name}</option>
			{/section}
		</select>
	</div>

	{if $lists[list].ID != ""}
	<input type="hidden" value="{$lists[list].ID }" name="tasklist" />
	{else}
	<input type="hidden" value="{$tasklist.ID }" name="tasklist" />
	{/if}

	<div class="row-butn-bottom">
		<label>&nbsp;</label>
		<button type = "submit" onfocus="this.blur();">{#addbutton#}</button>
		<button onclick="blindtoggle('form_{$lists[list].ID}');toggleClass('add_{$lists[list].ID}','add-active','add');toggleClass('add_butn_{$lists[list].ID}','butn_link_active','butn_link');toggleClass('sm_{$lists[list].ID}','smooth','nosmooth');return false;" onfocus="this.blur();">{#cancel#}</button>
	</div>

	</fieldset>
	</form>

</div> {*block_in_wrapper end*}