{if $showhtml != "no"}
{include file="header.tpl"  jsload = "ajax" jsload1 = "tinymce"}



{include file="tabsmenue-project.tpl" projecttab = "active"}
<div id="content-left">
<div id="content-left-in">
<div class="projects">

<div class="breadcrumb">
<a href="manageproject.php?action=showproject&amp;id={$project.ID}" title="{$project.name}"><img src="./templates/standard/images/symbols/projects.png" alt="" />{$project.name|truncate:50:"...":true}</a>
<span>&nbsp;/...</span>
</div>

<h1 class="second"><img src="./templates/standard/images/symbols/projects.png" alt="" />{$project.name}</h1>

{/if}

<div class="block_in_wrapper">

	<h2>{#editproject#}</h2>

	<form class="main" method="post" action="manageproject.php?action=edit&amp;id={$project.ID}" {literal}onsubmit="return validateCompleteForm(this,'input_error');"{/literal}>
	<fieldset>

	<div class="row"><label for="name">{#name#}:</label><input type="text" class="text" name="name" id="name" required="1" realname="{#name#}" value = "{$project.name|htmlentities}" /></div>
	<div class="row"><label for="desc">{#description#}:</label><div class="editor"><textarea name="desc" id="desc"  rows="3" cols="1">{$project.desc}</textarea></div></div>

	<div class="row">
		<label for="end">{#startdate#}:</label><input type="text" class="text" value="{$project.startstring}" name="start"  id="start" realname="{#startdate#}" />
		<br /><br />
	</div>

	<div class="row">
		<label for="end">{#finishdate#}:</label><input type="text" class="text" value="{$project.endstring}" name="end"  id="end" realname="{#finishdate#}" />
		<br /><br />
	</div>

	<div class="datepick">
		<div id = "datepicker_project_start" class="picker" style = "display:none;"></div>
		<div id = "datepicker_project_end" class="picker" style = "display:none;"></div>
	</div>

	<script type="text/javascript">
		theCal{$lists[list].ID} = new calendar({$theM},{$theY});
		theCal{$lists[list].ID}.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
		theCal{$lists[list].ID}.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
		theCal{$lists[list].ID}.relateTo = "start";
		theCal{$lists[list].ID}.getDatepicker("datepicker_project_start");
		
		theCal{$lists[list].ID} = new calendar({$theM},{$theY});
		theCal{$lists[list].ID}.dayNames = ["{#monday#}","{#tuesday#}","{#wednesday#}","{#thursday#}","{#friday#}","{#saturday#}","{#sunday#}"];
		theCal{$lists[list].ID}.monthNames = ["{#january#}","{#february#}","{#march#}","{#april#}","{#may#}","{#june#}","{#july#}","{#august#}","{#september#}","{#october#}","{#november#}","{#december#}"];
		theCal{$lists[list].ID}.relateTo = "end";
		theCal{$lists[list].ID}.getDatepicker("datepicker_project_end");
	</script>

		<div class = "row">
		<label for = "budget">{#planeffort#}:</label>
		<input type = "text" class="text" name = "budget" id = "budget" value="{$project.planeffort}" />
		</div>

    <div class = "row">
        <label for = "accesskey">{#accesskey#}:</label>
        <input type = "text" class="text" name = "accesskey" id = "accesskey" value="{$project.accesskey}" readonly="readonly" style="background-color: #ddd;" />
    </div>

	<div class="row-butn-bottom">
		<label>&nbsp;</label>
		<button type="submit" onfocus="this.blur();">{#send#}</button>
		<button onclick="blindtoggle('form_edit');toggleClass('edit_butn','edit-active','edit');toggleClass('sm_project','smooth','nosmooth');toggleClass('sm_project_desc','smooth','nosmooth');return false;" onfocus="this.blur();" {if $showhtml != "no"} style="display:none;"{/if}>{#cancel#}</button>
	</div>

	</fieldset>
	</form>

</div> {*block_in_wrapper end*}



{if $showhtml != "no"}
	<div class="content-spacer"></div>
	</div> {*Projects END*}
	</div> {*content-left-in END*}
	</div> {*content-left END*}

	{include file="sidebar-a.tpl"}
	{include file="footer.tpl"}
{/if}