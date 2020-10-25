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
				<div class="col-md-3">
					<ul class="nav nav-pills nav-stacked menu_one">
					<?php 
						if($code == 0 && $monitor_data['module_list'] && is_array($monitor_data['module_list'])) 
						{
							foreach($monitor_data['module_list'] as $k => $v)
							{
								$addClass = '';
								if($module_name == $k) $addClass = 'class="active"';
					?>
						<li <?php echo $addClass; ?> role="presentation">
							<a href="/admin/monitor/countCallStatistics?server_name=<?php echo $server_name; ?>&module_name=<?php echo $k; ?>&ip=<?php echo $_GET['ip']; ?>"><?php echo $k; ?></a>
							<ul class="nav nav-pills nav-stacked menu_second">
							<?php if($v){ foreach($v as $v1) { $addClass = ''; if($interface_name == $v1) $addClass = 'class="active"'; ?>
								<li <?php echo $addClass; ?> role="presentation"><a href='/admin/monitor/countCallStatistics?server_name=<?php echo $server_name;?>&module_name=<?php echo $k; ?>&interface_name=<?php echo $v1; ?>&ip=<?php echo $_GET['ip']; ?>'><?php echo $v1; ?></a></li>
							<?php } } ?>
							</ul>
						</li>
					<?php } } ?>
					</ul>
				</div>
				<div class="col-md-9">
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="id_date_time">							
						<?php
							if($code == 0 && $interface_name)
							{
								$current_date_time	= isset($_GET['date_time']) ? $_GET['date_time'] : date('Ymd') ;
								$ip	= $_GET['ip'];
								for($i=13;$i>=0;$i--)
								{
									$the_time = strtotime("-$i day");
									$the_date = date('Ymd',$the_time);
									$date_str = $current_date_time == $the_date ? "<b>$the_date</b>" : $the_date ;
									$date_html	= "<a href='/admin/monitor/countCallStatistics?server_name={$server_name}&module_name={$module_name}&interface_name={$interface_name}&date_time={$the_date}&ip={$ip}' class='btn' type='button'>{$date_str}</a>";
									if($i == 7) $date_html .= "<br/>";
									echo $date_html;
								}
							}
						?>
						</div>
					</div>
					<?php 
						if($code == 0 && $monitor_data['monitor_count'])
						{
					?>
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="main">
						</div>
					</div>
					<?php }else if($code == 0 && $interface_name){ ?>
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="id_alert" style="padding-top:100px;">
							<center>
								<h1>暂无数据</h1>
							</center>
						</div>
					</div>
					<?php }else{ ?>
					<div class="row clearfix">
						<div class="col-md-12 column text-center" id="id_alert" style="padding-top:100px;">
							<center>
								<h1>请选择左边相应栏目，查看数据</h1>
							</center>
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>

		<script>
		<?php 
		if($code == 0 && $monitor_data['monitor_count'])
		{
		?>
			var tdata = {
				'timename':'2019-03-29',
				'sone': <?php  echo json_encode($monitor_data['monitor_count']); ?>
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
		<?php } ?>
		</script>
	</body>
</html>
