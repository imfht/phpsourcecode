{include file="header.tpl" jsload = "ajax" }

{include file="tabsmenue-project.tpl" timetab = "active"}
<div id="content-left">
<div id="content-left-in">
<div class="timetrack">


	<div class="infowin_left" style = "display:none;" id = "systemmsg">
		{if $mode == "added"}
		<span class="info_in_green"><img src="templates/standard/images/symbols/timetracker.png" alt=""/>{#timetracker#} {#was#} {#added#}</span>
		{elseif $mode == "edited"}
		<span class="info_in_yellow"><img src="templates/standard/images/symbols/timetracker.png" alt=""/>{#timetracker#} {#was#} {#edited#}</span>
		{elseif $mode == "deleted"}
		<span class="info_in_red"><img src="templates/standard/images/symbols/timetracker.png" alt=""/>{#timetracker#} {#was#} {#deleted#}</span>
		{/if}
	</div>
	{literal}
	<script type = "text/javascript">
	systemMsg('systemmsg');
	 </script>
	{/literal}


<h1>{$projectname}<span>/ {#timetracker#}</span></h1>

{*Timetracker*}
{if $userpermissions.timetracker.add}
<div class="timetrack">
    <div class="headline">
        <a href="javascript:void(0);" id="trackerhead_toggle" class="win_block" onclick = "toggleBlock('trackerhead');"></a>

        <!-- Export-block
        <div class="wintools">
            <div class="export-main">
                <a class="export"><span>{#export#}</span></a>
                <div class="export-in"  style="width:23px;left: -23px;"> {*at one item*}
                    <a class="ical" href="managetask.php?action=ical"><span>{#icalexport#}</span></a>
                </div>
            </div>
        </div>
        -->

        <h2>
            <a href="managetimetracker.php?action=showproject&amp;id={$project.ID}" title="{#timetracker#}"><img src="./templates/standard/images/symbols/timetracker.png" alt="" />{#timetracker#}</a>
        </h2>
    </div>

    <div class="block" id="trackerhead"{if $smarty.cookies.trackerhead!=""} style="display:{$smarty.cookies.trackerhead}"{/if}>
        <div id = "trackerform" class="addmenue">
            {include file="addtimetracker.tpl" }
        </div>
        <div class="tablemenue"></div>
    </div> {*block end*}
</div> {*timetrack end*}

<div class="content-spacer"></div>
{/if}
{*Timetracker End*}

<div class="timetrack">
			<div class="headline">
				<a href="javascript:void(0);" id="acctracker_toggle" class="win_block" onclick = "toggleBlock('acctracker');"></a>


				<div class="wintools">
					<div class="export-main">
						<a class="export"><span>{#export#}</span></a>
						<div class="export-in"  style="width:46px;left: -46px;"> {*at one item*}
							<a class="pdf" href="managetimetracker.php?action=projectpdf&amp;id={$project.ID}{if $start != "" and $end != ""}&amp;start={$start}&amp;end={$end}{/if}{if $usr > 0}&amp;usr={$usr}{/if}{if $task > 0}&amp;task={$task}{/if}{if $fproject > 0}&amp;project={$fproject}{/if}"><span>{#pdfexport#}</span></a>
							<a class="excel" href="managetimetracker.php?action=projectxls&amp;id={$project.ID}{if $start != "" and $end != ""}&amp;start={$start}&amp;end={$end}{/if}{if $usr > 0}&amp;usr={$usr}{/if}{if $task > 0}&amp;task={$task}{/if}{if $fproject > 0}&amp;project={$fproject}{/if}"><span>{#excelexport#}</span></a>
						</div>
					</div>

						<div class="toolwrapper">
							<a class="filter" href="javascript:blindtoggle('filter');" id="filter_report" onclick="toggleClass(this,'filter-active','filter');toggleClass('filter_butn','butn_link_active','butn_link');toggleClass('sm_report','smooth','nosmooth');"><span>{#filterreport#}</span></a>
						</div>

				</div>


				<h2>
					<img src="./templates/standard/images/symbols/timetracker.png" alt="" />{#report#}
				</h2>
			</div>

			<div class="block" id="acctracker"{if $smarty.cookies.acctracker!=""} style="display:{$smarty.cookies.acctracker}"{/if}>

			{*Filter Report*}
				<div id = "filter" class="addmenue" style = "display:none;">
					{include file="filtertracker.tpl" }
				</div>
			{*Filter End*}

				<div class="nosmooth" id="sm_report">
				<table cellpadding="0" cellspacing="0" border="0">


					<thead>
						<tr>
							<th class="a"></th>
							<th class="b">{#user#}</th>
							<th class="cf">{#day#}</th>
							<th class="cf">{#started#}</th>
							<th class="cf">{#ended#}</th>
							<th class="e">{#hours#}</th>
							<th class="tools"></th>
						</tr>
					</thead>

					<tfoot>
						<tr>
							<td colspan="6"></td>
						</tr>
					</tfoot>

					{section name = track loop=$tracker}

					{*Color-Mix*}
					{if $smarty.section.track.index % 2 == 0}
					<tbody class="color-a" id="track_{$tracker[track].ID}">
					{else}
					<tbody class="color-b" id="track_{$tracker[track].ID}">
					{/if}
						<tr>
							<td></td>
							<td>
								<div class="toggle-in">
								<span class="acc-toggle" onclick="javascript:accord_tracker.activate($$('#acc-tracker .accordion_toggle')[{$smarty.section.track.index}]);toggleAccordeon('acc-tracker',this);"></span>
									<a href = "manageuser.php?action=profile&amp;id={$tracker[track].user}" title="{$tracker[track].pname}">
										{$tracker[track].uname|truncate:16:"...":true}
									</a>
								</div>
							</td>
							<td>{$tracker[track].daystring|truncate:12:"...":true}</td>
							<td>{$tracker[track].startstring|truncate:12:"...":true}</td>
							<td>{$tracker[track].endstring|truncate:12:"...":true}</td>
							<td>{$tracker[track].hours|truncate:12:"...":true}</td>
							<td class="tools">
								{if $userpermissions.timetracker.edit}
								<a class="tool_edit" href="managetimetracker.php?action=editform&amp;tid={$tracker[track].ID}&amp;id={$project.ID}" title="{#edit#}"></a>
								{/if}
								{if $userpermissions.timetracker.del}
								<a class="tool_del" href="javascript:confirmfunction('{#confirmdel#}','deleteElement(\'track_{$tracker[track].ID}\',\'managetimetracker.php?action=del&amp;tid={$tracker[track].ID}&amp;id={$project.ID}\')');"  title="{#delete#}"></a>
								{/if}
							</td>
						</tr>

						<tr class="acc">
							<td colspan="7">
								<div class="accordion_toggle"></div>
								<div class="accordion_content">
									<div class="acc-in">

										{if $tracker[track].comment != ""}
											<strong>{#comment#}:</strong><br />{$tracker[track].comment}
										{/if}

										{if $tracker[track].task > 0}
											<p class="tags-miles">
											<strong>{#task#}:</strong><br />
											<a href = "managetask.php?action=showtask&amp;tid={$tracker[track].task}&amp;id={$project.ID}">{$tracker[track].tname}</a>
											</p>
										{/if}
									</div>
								</div>
							</td>
						</tr>
					</tbody>
					{/section}

					<tbody class="tableend">
						<tr>
							<td></td>
							<td colspan="4"><strong>{#totalhours#}:</strong></td>
							<td><strong>{$totaltime}</strong></td>
							<td class="tools"></td>
						</tr>
					</tbody>
					<tbody class="paging">
						<tr>
							<td></td>
							<td colspan="5">
								<div id="paging">
									{paginate_prev} {paginate_middle} {paginate_next}
								</div>
							</td>
							<td class="tools"></td>
						</tr>
					</tbody>
				</table>
			</div> {*smooth End*}

			<div class="tablemenue">
					<div class="tablemenue-in">
						<a class="butn_link" href="javascript:blindtoggle('filter');" id="filter_butn" onclick="toggleClass('filter_report','filter-active','filter');toggleClass(this,'butn_link_active','butn_link');toggleClass('sm_report','smooth','nosmooth');">{#filterreport#}</a>
					</div>
			</div>


		</div> {*block END*}
</div> {*timetrack END*}

<div class="content-spacer"></div>
		{literal}
		<script type = "text/javascript">
			var accord_tracker = new accordion('acc-tracker');
		</script>
		{/literal}


</div> {*Timetracking END*}
</div> {*content-left-in END*}
</div> {*content-left END*}

{include file="sidebar-a.tpl" showcloud="1"}
{include file="footer.tpl"}