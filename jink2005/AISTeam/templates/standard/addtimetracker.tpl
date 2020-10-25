<div class="block_in_wrapper">

	<form class="main" id = "trackeradd" method = "post" action = "managetimetracker.php?action=add" onsubmit="{literal}return checkEtc(this);{/literal}">
	<fieldset>    
        {if $tracker}
        <input type = "hidden" name = "redirect_tracker" value = "" />
        {/if}
        <input type = "hidden" name = "project" value = "{$project.ID}" />

	 	<div class = "row">
	  		<label for = "ttday">{#day#}:</label>
	  		<input type = "text" class="text" style="width:80px;margin:0 6px 0 0;" id = "ttday" name = "ttday" realname = "{#date#}" />
		</div>

		<div class="datepick">
				<div id = "datepicker_addtt" class="picker" style = "display:none;"></div>
		</div>
		
		<script type="text/javascript">
		theCal = new calendar({$theM},{$theY});
		theCal.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
		theCal.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
		theCal.relateTo = "ttday";
		theCal.keepEmpty = false;
		theCal.dateFormat = "{$settings.dateformat}";
		theCal.getDatepicker("datepicker_addtt");
        </script>
        
	  	<div class = "row">
	  		<label for = "started">{#started#}:</label>
	  		<input type = "text" class="text" style="width:80px;margin:0 6px 0 0;" id = "started" name = "started" required = "1" regexp="^\d\d:\d\d$" realname = "{#started#} (Format: hh:mm)" />

	  		<button onclick="getnow('started');return false;" onfocus="this.blur();" title = "{#inserttime#}">hh:mm</button>
		</div>

	  	<div class = "row">
	  		<label for = "ended">{#ended#}:</label>
			<input  type = "text" class="text" style="width:80px;margin:0 6px 0 0;" id = "ended" name = "ended"  required = "1" regexp="^\d\d:\d\d$" realname = "{#ended#} (Format: hh:mm)" / >

			<button onclick="getnow('ended');return false;" onfocus="this.blur();" title = "{#inserttime#}">hh:mm</button>
	   </div>
	   
	   <div class = "row">
            <label for = "ended_hours">{#addhours#}:</label>
            {literal}
            <script type="text/javascript">
            function addHours(hours) {
                var start_time = document.getElementById('started').value;

                if (/\d\d\:\d\d/.test(start_time)) {
                
                    var time = start_time.split(':');
                    var hours_to_add = Math.floor(parseFloat(hours));
                    var mins_to_add = (parseFloat(hours) - hours_to_add) * 60;

                    //alert(hours_to_add);
                    if(time[1]>=30 && mins_to_add==30)
                        hours_to_add++;
                    if(time[0]<10)
                        time[0] = time[0].replace("0","");
                    if(time[1]<10)
                        time[1] = time[1].replace("0","");
                    time[1] = parseInt(time[1]) + mins_to_add;
                    time[0] = parseInt(time[0]) + parseInt(hours_to_add);

                    // normalize the time
                    if (time[0] > 23) {
                        time[0] = time[0] - 24;
                    }
                    if (time[1] > 59) {
                        time[1] = time[1] - 60;
                    }
                    time = time.map(function(t) {
                        return t < 10 ? '0'+t : t;
                    });
                    document.getElementById('ended').value = time.join(':');
                }
            }
            </script>
            {/literal}
			<select id="ended_hours" name="ended_hours" onchange="addHours(this.value)">
                <option value="0" selected="selected">0</option>
                <option value="0.5">0.5</option>
                <option value="1">1</option>
                <option value="1.5">1.5</option>
                <option value="2">2</option>
                <option value="2.5">2.5</option>
                <option value="3">3</option>
                <option value="3.5">3.5</option>
                <option value="4">4</option>
                <option value="4.5">4.5</option>
                <option value="5">5</option>
                <option value="5.5">5.5</option>
                <option value="6">6</option>
                <option value="6.5">6.5</option>
                <option value="7">7</option>
                <option value="7.5">7.5</option>
                <option value="8">8</option>
                <option value="8.5">8.5</option>
                <option value="9">9</option>
			</select>
		</div>

	  	<div class = "row">
	  		<label for ="trackcomm">{#comment#}:</label>
	  		<textarea name = "comment" id = "trackcomm" ></textarea>
	  	</div>

	  	<div class = "row">
			<label for="ttask">{#task#}:</label>
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
			{/literal}
			</script>
			<select name = "ttask" id = "ttask" onchange="setRequiredEtc(this.value);">
                <option value = "0" >{#chooseone#}</option>
		  	{section name = task loop=$ptasks}
		  		{if $ptasks[task].title != ""}
		  		<option value="{$ptasks[task].ID}">{$ptasks[task].title}</option>
		  		{else}
		  		<option value="{$ptasks[task].ID}">{$ptasks[task].text|truncate:30:"...":true}</option>
				{/if}
			{/section}
	  	</select>
	  	</div>

        <div class="row">
            <label for="efforttocomplete">{#efforttocomplete#}:</label>
            <input type="text" class="text" id="efforttocomplete" name="efforttocomplete" required="1" realname="{#efforttocomplete#}" regex="{literal}\d+(\.\d+)?{/literal}" />
        </div>

		<div class="row-butn-bottom">
			<label>&nbsp;</label>
			<button type="submit" onfocus="this.blur();">{#addbutton#}</button>
		</div>

<script type="text/javascript" src="include/js/modaldbox/modaldbox.js"></script>
<link rel="stylesheet" href="include/js/modaldbox/modaldbox.css" type="text/css" />
<div id="modalbox" class="dialog">
	<div id="txt">Do you want to close the task?</div>
	<button onclick="hm('modalbox');yesSelected()">Yes</button>
	<button onclick="hm('modalbox');noSelected()">No</button>
</div>
<script type="text/javascript">
{literal}
form_is_checked = false;
function checkEtc(obj) {
	if(form_is_checked)
		return true;
	if(validateCompleteForm(obj,'input_error')) {
		if(parseInt(document.getElementById('efforttocomplete').value)==0) {
			sm('modalbox',200,50);
			return false;
		}
		return true;
	} else {
		return false;
	}
}
function yesSelected() {
	form_is_checked = true;
	document.getElementById('trackeradd').submit();
}
function noSelected() {
	form_is_checked = true;
	document.getElementById('efforttocomplete').value = '';
	document.getElementById('trackeradd').submit();
}
{/literal}
</script>

	</fieldset>
</form>

</div> {*block_in_wrapper end*}