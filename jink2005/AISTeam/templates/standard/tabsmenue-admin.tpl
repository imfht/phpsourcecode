<div class="tabswrapper">
	<ul class="tabs">
		<li class="desk"><a {if $desktab == "active" }class="active"{/if} href="index.php"><span>{#desktop#}</span></a></li>
		{if $userpermissions.admin.add}
		<li class="projects"><a {if $projecttab == "active" }class="active"{/if} href="admin.php?action=projects"><span>{#projectadministration#}</span></a></li>
		{/if}
		<li class="user"><a {if $usertab == "active" }class="active"{/if} href="admin.php?action=users"><span>{#useradministration#}</span></a></li>
		{if $userpermissions.admin.add}
		<li class="system-settings"><a {if $settingstab == "active" }class="active"{/if} href="admin.php?action=system"><span>{#systemadministration#}</span></a></li>
		{/if}
	</ul>
</div>