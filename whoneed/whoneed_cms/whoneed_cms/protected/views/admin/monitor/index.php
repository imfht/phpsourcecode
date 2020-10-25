<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<title></title>
		<link rel="stylesheet" href="/monitor/css/bootstrap.min.css" />
		<script type="text/javascript" src="/monitor/js/jquery.min.js" ></script>
		<script type="text/javascript" src="/monitor/js/bootstrap.min.js" ></script>
		<script type="text/javascript" src="/monitor/js/highchars.js" ></script>
		<style>
			.menu_one>li>a{
				font-size: 16px;
			    display: block;
			}
			.menu_one>.active>a{
				padding: 10px 15px;
				color: #fff;
				background-color: #428bca;
				border-radius: 4px;
			}
			.menu_second>li{
				padding-left: 20px;
			}
		</style>
	</head>
	<body>
		<div class="container">
		  	<div class="row">
				<div class="col-md-2">
					<ul class="nav nav-pills nav-stacked menu_one">
					<?php 
						if($module_list) 
						{
							foreach($module_list as $k => $v)
							{
					?>
						<li role="presentation">
							<a href="javascript:showAndHide('#<?php echo $k; ?>');"><?php echo $k; ?></a>
							<ul class="nav nav-pills nav-stacked menu_second" id="<?php echo $k; ?>" style="display:none;">
							<?php if($v){ foreach($v as $v1) {?>
								<li role="presentation"><a href='javascript:changeSet("/admin/monitor/countCallStatistics?server_name=<?php echo $_GET['server_name'];?>&module_name=<?php echo $k; ?>&interface_name=<?php echo $v1; ?>", 1);'><?php echo $v1; ?></a></li>
							<?php } } ?>
							</ul>
						</li>
					<?php } } ?>
					</ul>
				</div>
				<div class="col-md-10">
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="id_date_time" style="display:none">							
						<?php
							$current_date_time	= isset($_GET['date_time']) ? $_GET['date_time'] : date('Y-m-d') ;
							for($i=13;$i>=0;$i--)
							{
								$the_time = strtotime("-$i day");
								$the_date = date('Y-m-d',$the_time);
								$date_str = $current_date_time == $the_date ? "<b>$the_date</b>" : $the_date ;
								$date_html	= "<a href='javascript:changeDate(\"{$the_date}\");' class='btn' type='button'>{$date_str}</a>";
								if($i == 7) $date_html .= "<br/>";
								echo $date_html;
						?>
						<?php } ?>
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="main" style="display:none">
						</div>
					</div>
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="id_alert" style="padding-top:100px;">
							<center>
								<h1>请选择左边相应栏目，查看数据</h1>
							</center>
						</div>
					</div>
				</div>
			</div>
		</div>

		<script>
			var tdata = {
				'timename':'2019-03-29',
				'sone': [[0,0]]
			};
			var onmenu = $('.menu_one .active>a').html()
			console.log( tdata.sone,onmenu)
			//初始化
			Highcharts.setOptions({
				global: {
					useUTC: false
				}
			})
			var chart = Highcharts.chart('main',{
				chart: {
					type: 'spline'
				},
				title: {
					text: '上报量(次/5分钟)'
				},
				subtitle: {
					text: ''
				},
				xAxis: {
					type: 'datetime',
					dateTimeLabelFormats: { 
						hour: '%H:%M'
					}
				},
				yAxis: {
					title: {
						text: '上报量(次/5分钟)'
					},
					min: 0
				},
				tooltip: {   
					formatter: function() {
						return '<p style="color:'+this.series.color+';font-weight:bold;">'
						+ this.series.name + 
						'</p><br /><p style="color:'+this.series.color+';font-weight:bold;">时间：' + Highcharts.dateFormat('%m月%d日 %H:%M', this.x) + 
						'</p><br /><p style="color:'+this.series.color+';font-weight:bold;">数量：'+ this.y + '</p>';
					}
				},
				credits: {
					enabled: false,
				},
				series: [
					{
					    name: '上报曲线',
						data: tdata.sone,
						lineWidth: 2,
						marker:{
							radius: 1
						},
						pointInterval: 300*1000
					}
				]
			});
		</script>

		<script>
		function showAndHide(id)
		{
			console.log(id);
			$(id).toggle();
		}

		$(function()
		{
			//一级菜单
			$('.menu_one>li>a').click(function(){
				var index = $(this).parent('li').index()
				$('.menu_one .active').removeClass('active')
				$('.menu_one>li').eq(index).addClass('active')
			});
			//二级菜单
			$('.menu_second>li').click(function(){
				$('.menu_one .active').removeClass('active')
				$(this).addClass('active')
			});
		})

		//更新数据
		var strApiUrl	= '';

		function changeDate(date_time)
		{
			changeSet(strApiUrl + '&date_time=' + date_time, 0);
		}

		function changeSet(val, ope)
		{
			console.log(val + ', ' + ope);

			// 显示数据
			$("#id_date_time").show();
			$("#main").show();
			$("#id_alert").hide();
			
			// save last click
			if(ope == 1)
				strApiUrl	= val;

			$.ajax({ 
				url: val, 
				data: {},
				dataType: "json",
				success: function(data){
					console.log(data);

					chart.setTitle({ text: data.date_time + ' ' + data.module_name + ' ' + data.interface_name }, null);

					if(data.code == 0)
					{
						chart.series[0].update({
							data: data.sone
						});
					}else{
						alert('暂无数据');
					}
				},
				error: function(error){
					console.log(error);
				}
			})
		}
		</script>
	</body>
</html>
