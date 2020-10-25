{include file="news/header.tpl"}

<!-- 头部// -->
{include file="news/top.tpl"}


{include file="news/nav.tpl"}
	<!-- start: Content -->
			<div id="content" class="span10">
			
						
			<div class="row-fluid">


				<div class="well" >
				<div class="box-content"><!-- Default panel contents -->
				
				<div class="form-inline suoding">
动作监控：
{foreach from=$flag item=l}
<a href="./index.php?log/chart/?event={$l.event}" class="label">{$l.event|cntruncate:6}</a>
{/foreach}
</div>
				
		<div id="container" style="min-width: 310px; height: 350px; margin: 20px auto;"></div>

</div></div></div>



</div>


<table id="datatable" style="display:none;">
	<thead>
		<tr>
			<th>日期</th>
			<th>人数</th>
		</tr>
	</thead>
	<tbody>
	{foreach from=$log item=l}
	
		<tr>
			<td>{$l.rq}</td>
			<td>{$l.num}</td>
		</tr>
		{/foreach}
	</tbody>
</table>

	<script type="text/javascript" src="./static/public/jquery-1.11.0.min.js"></script>

{literal}

	<style type="text/css">
${demo.css}
	</style>
	<script type="text/javascript">
	$(function () {
	    $('#container').highcharts({
	        data: {
	            table: document.getElementById('datatable')
	        },
	        chart: {
	            type: 'line'
	        },
	        plotOptions: {
                series: {
                    borderWidth: 0,
                    dataLabels: {
                        enabled: true,
                        format: '{point.y}'
                    }
                }
            },
            
	        title: {
	            text: '最近7天{/literal}{$event}{literal}情况'
	        },
	        yAxis: {
	            allowDecimals: false,
	            title: {
	                text: '人数'
	            }
	        },
	        tooltip: {
	            formatter: function() {
	                return '<b>'+ this.series.name +'</b><br/>'+
	                    this.point.y +' '+ this.point.name.toLowerCase();
	            }
	        }
	    });
	}); 
	
	</script>
	{/literal}
		<script src="./static/public/highchart/js/highcharts.js"></script>
<script src="./static/public/highchart/js/modules/data.js"></script>
<script src="./static/public/highchart/js/modules/exporting.js"></script>
{include file="news/footer.tpl"}

