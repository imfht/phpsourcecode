 <div class="box">
    <div class="heading">
     <h2><?php echo $heading_title; ?></h2>
     <div class="buttons">
      	<div class="btn-toolbar" style="margin-bottom: 9px">
	        <div class="btn-group">
	          <a class="btn" href="<?php echo $week_report;?>">周视图</a>
	          <a class="btn" href="<?php echo $month_report;?>">月视图</i></a>
	          <a class="btn" href="<?php echo $year_report;?>">年视图</a>
			</div>
        </div>
      </div>
    </div>
   <div class="content">
     <div id="sale-trend" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
   </div>
  </div>
<script type="text/javascript">
$(function () {
    var chart;
    $(document).ready(function() {
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'sale-trend',
                type: 'line'
            },
            title: {
                text: '报表趋势',
                x: -20 //center
            },
            subtitle: {
                text: 'Shopilex-统计趋势',
                x: -20
            },
            xAxis: {
                categories: [<?php echo $range;?>]
            },
            yAxis: {
                title: {
                    text: '数量'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }],
                min: 0
            },
            tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ this.y ;
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
            series: [{
                name: '销售额(百元)',
                data: [<?php echo $order_total;?>]
            }, {
                name: '订单数',
                data: [<?php echo $orders;?>]
            }, {
                name: '注册客户数',
                data: [<?php echo $customers?>]
            },  {
                name: '评价数',
                data: [<?php echo $review;?>]
            }]
        });
    });
    
});
</script>	
<script type="text/javascript" src="view/javascript/highcharts.js"></script>
<script type="text/javascript" src="view/javascript/exporting.js"></script>