{include file="header.tpl" title="Login"}
<body>
<div id="topbar">
	<div id="leftnav">
		<a href="index.php"><img alt="home" src="templates/{$settings.template2}/images/home.png" /></a>
		<a href="manageproject.php?action=showproject&id={$project.ID}">{$projectname|truncate:16:"...":true}</a>
		<a href="managetask.php?action=showproject&id={$project.ID}">{#tasklists#}</a>
	</div>
	<div id="rightnav"><a href="manageuser.php?action=logout">{#logout#}</a></div>
</div>

<div id="content">
	<ul class="pageitem">
		<li class="textbox"><span class="header">{$tasklist.name}</span>{$tasklist.desc}</li>
	</ul>
	<ul class="pageitem">
		<li class="smallfield"><span class="name">{#startdate#}</span><span class="info">{$tasklist.startdate}</span></li>
		<li class="smallfield"><span class="name">{#finishdate#}</span><span class="info">{$tasklist.finishdate}</span></li>
		<li class="smallfield"><span class="name">{#planeffort#}</span><span class="info">{$tasklist.planeffort} {#hours#}</span></li>
		<li class="smallfield"><span class="name">{#milestone#}</span><span class="info">{$tasklist.milestone_name}</span></li>
		<li class="smallfield"><span class="name">{#responsible#}</span><span class="info">{$tasklist.responsible}</span></li>
		<li class="smallfield"><span class="name">{#tasks#}</span><span class="info">{if $tasks}{$tasks|@count}{else}0{/if}</span></li>
		<li class="smallfield"><span class="name">{#donetasks#}</span><span class="info">{if $donetasks}{$donetasks|@count}{else}0{/if}</span></li>
		<li class="smallfield"><span class="name">{#done#}</span><span class="info">{$tasklist.done}%</span></li>
	</ul>
{if $settings.template2=="iphone"}
	<ul class="pageitem">
		<li class="textbox">
		<div id="chartdiv" style="width:100%;height:300px;"></div>
		</li>
	</ul>
	<ul class="pageitem">
		<li class="textbox">
		<div id="chartdiv2" style="width:100%;height:300px;"></div>
		</li>
	</ul>
{/if}
</div>
{if $settings.template2=="iphone"}
<link type="text/css" rel="stylesheet" href="include/js/dhtmlxchart/dhtmlxchart.css" />
<script type="text/javascript" language="javascript" src="include/js/dhtmlxchart/dhtmlxchart.js"></script>
<script type="text/javascript" language="javascript">
var data = [{$chart_data}];
var data2 = [{$chart_data2}];
{literal}
window.onload = function(){
	var chart =  new dhtmlXChart({
		view:"line",
		container:"chartdiv",
		value:"#real#",
		item:{borderColor: "#3399ff", radius: 1},
		line:{color:"#3399ff", width:3},
		xAxis:{template:"#date#",show:"side"},
		yAxis:{title:"Hours",hide_null:true},
		padding:{left:45,bottom:20,right:30},
		legend:{
			layout:"x",
			align:"center",
			marker:{type:"round", width:15},
			values:[
				{text:"Remaining Effort",color:"#3399ff"},
				{text:"Ideal",color:"#ff0000"}
			]
		}
	})
	chart.addSeries({
		value:"#ideal#",
		item:{borderColor: "#ff0000", radius: 0},
		line:{color:"#ff0000", width:3 }
	})
	chart.parse(data,"json");
	
	var chart =  new dhtmlXChart({
		view:"line",
		container:"chartdiv2",
		value:"#real#",
		item:{borderColor: "#3399ff", radius:1},
		line:{color:"#3399ff", width:3},
		xAxis:{template:"#date#",show:"side"},
		yAxis:{title:"Hours",hide_null:true},
		padding:{left:45,bottom:20,right:30},
		legend:{
			layout:"x",
			align:"center",
			marker:{type:"round", width:15},
			values:[
				{text:"Actual",color:"#3399ff"},
				{text:"Planned",color:"#ff0000"}
			]
		}
	})
	chart.addSeries({
		value:"#ideal#",
		item:{borderColor: "#ff0000", radius:0},
		line:{color:"#ff0000", width:3 }
	})
	chart.parse(data2,"json");
}
{/literal}
</script>
{/if}
{include file="footer.tpl"}