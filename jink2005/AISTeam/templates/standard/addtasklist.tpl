<div class = "block_in_wrapper">
	<h2>{#addtasklist#}</h2>
	<form class="main" method="post" action="managetasklist.php?action=add&amp;id={$project.ID}" {literal} onsubmit="if(validateCompleteForm(this)) return verifyWorkpackageDates(); else return false;"{/literal} >
	<fieldset>
			<div class="row"><label for="name">{#name#}:</label><input type="text" class="text" name="name" id="name" required="1" realname="{#name#}" /></div>
			<div class="row"><label for="desc">{#description#}:</label><div class="editor"><textarea name="desc" id="desc"  rows="3" cols="1" ></textarea></div></div>
	        <div class="row"><label for="planeffort">{#planeffort#}:</label><input type="text" class="text" name="planeffort" id="planeffort" realname="{#planeffort#}" regexp="{literal}\d+(\.\d+)?{/literal}" /></div>
	        
            <div class="row"><label for="startdate">{#startdate#}:</label><input type="text" class="text" name="startdate" id="startdate" required="1" realname="{#startdate#}" date="{"Ymd"|date:$pro.start}" value="{"d.m.Y"|date:$pro.start}" /></div>
            <div class="datepick">
                <div id = "add_project_start" class="picker" style = "display:none;"></div>
            </div>
            
            <div class="row"><label for="finishdate">{#finishdate#}:</label><input type="text" class="text" name="finishdate" id="finishdate" required="1" realname="{#finishdate#}" date="{"Ymd"|date:$pro.end}" value="{"d.m.Y"|date:$pro.end}" /></div>
	        
	        <div class="datepick">
	            <div id = "add_project_finish" class="picker" style = "display:none;"></div>
	        </div>
	
<script type="text/javascript">
theCal = new calendar({$theM},{$theY});
theCal.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
theCal.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
theCal.relateTo = "finishdate";
theCal.dateFormat = "{$settings.dateformat}";
theCal.getDatepicker("add_project_finish");

theCal2 = new calendar({$theM},{$theY});
theCal2.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
theCal2.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
theCal2.relateTo = "startdate";
theCal2.dateFormat = "{$settings.dateformat}";
theCal2.getDatepicker("add_project_start");
</script>
	        
			<div class="row">
				<label for="milestone">{#milestone#}: </label>
				<select name="milestone" id="milestone" >
				<option value="0" selected="selected">{#chooseone#}</option>
				{section name=stone loop=$milestones}
					<option value="{$milestones[stone].ID}">{$milestones[stone].name}</option>
				{/section}
				</select>
			</div>
	
            <div class="row"><label for="responsible">{#responsible#}:</label><input type="text" class="text" name="responsible" id="responsible" required="1" realname="{#responsible#}" /></div>
	
			<div class="row-butn-bottom">
				<label>&nbsp;</label>
				<button type="submit" onfocus="this.blur();">{#addbutton#}</button>
				<button onclick="blindtoggle('addlist');toggleClass('addtasklists','add','add-active');return false;" onfocus="this.blur();">{#cancel#}</button>
			</div>

	</fieldset>
</form>
</div>
<div class="content-spacer"></div>