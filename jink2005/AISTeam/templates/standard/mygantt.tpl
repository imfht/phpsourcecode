{include file="header.tpl" jsload = "ajax"  jsload1 = "tinymce"}

{if $project.ID>0}
{include file="tabsmenue-project.tpl" gantttab = "active"}
{else}
{include file="tabsmenue-desk.tpl" gantttab = "active"}
{/if}

<div id="content-left">
<div id="content-left-in">
<div class="tasks">

	{literal}
	<script type = "text/javascript">
	apperar = new Effect.Appear('systemmsg', { duration: 2.0 })
	 </script>
	{/literal}


	<h1>{$title}</h1>
	<div style="width:700px;height:400px;position:relative" id="ganttdiv"></div>
	<div class="content-spacer"></div>
	
<link type="text/css" rel="stylesheet" href="include/js/dhtmlxgantt/dhtmlxgantt.css" />
<script type="text/javascript" language="javascript" src="include/js/dhtmlxgantt/dhtmlxcommon.js"></script>
<script type="text/javascript" language="javascript" src="include/js/dhtmlxgantt/dhtmlxgantt.js"></script>
{literal}
<script type="text/javascript" language="JavaScript">
/*<![CDATA[*/
function createChartControl(htmlDiv){
	// Create Gantt control
	var ganttChartControl = new GanttChart();
	// Setup paths and behavior
	ganttChartControl.setImagePath("include/js/dhtmlxgantt/imgs/");
	ganttChartControl.setEditable(false);
	ganttChartControl.showTreePanel(true);
	ganttChartControl.showContextMenu(true);
	ganttChartControl.showDescTask(false,'d,s-f');
	ganttChartControl.showDescProject(true,'n,d');
	// Initialize Gantt data structures
{/literal}{$gantt_data}{literal}
	// Build control on the page
	ganttChartControl.create(htmlDiv);
}
createChartControl("ganttdiv");
/*]]>*/
</script>
{/literal}
 
</div> {*tasks END*}
</div> {*content-left-in END*}
</div> {*content-left END*}


{include file="sidebar-a.tpl" showcloud="1"}
{include file="footer.tpl"}
