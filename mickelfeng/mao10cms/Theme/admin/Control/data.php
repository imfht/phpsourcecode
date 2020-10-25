<?php mc_template_part('header'); ?>
<script src="<?php echo mc_theme_url(); ?>/js/Chart.js"></script>
	<div class="container-admin">
		<?php mc_template_part('head-control-nav'); ?>
		<div class="row">
			<div class="col-lg-12">
				<div id="single">
					<style>
						h5 span {display: inline-block; padding: 0 10px; margin: 0 4px 0 10px;}
						#canvas-red {background-color:#e7343a;}
						#canvas-yellow {background-color:#fdd322;}
					</style>
					<h3 class="text-center">10日内流量/销量对比图表</h3>
					<h5 class="text-center"><span id="canvas-red">&nbsp;</span>当天产品访问量<span id="canvas-yellow">&nbsp;</span>当天销售额</h5>
					<canvas id="canvas" height="450" width="940"></canvas>
				</div>
			</div>
		</div>
	</div>
	<?php $day_views_args = M('action')->where("page_id='1' AND user_id='1' AND action_key='day_views'")->order('id desc')->limit(0,10)->select(); $day_views_args = array_reverse($day_views_args); ?>
	<script>
		var barChartData = {
			labels : [<?php foreach($day_views_args as $day_views) : ?>"<?php echo $day_views['date']; ?>",<?php endforeach; ?>],
			datasets : [
				{
					fillColor : "#e7343a",
					strokeColor : "#a74230",
					data : [<?php foreach($day_views_args as $day_views) : ?><?php echo $day_views['action_value']; ?>,<?php endforeach; ?>]
				},
				{
					fillColor : "#fdd322",
					strokeColor : "#b89c3c",
					data : [<?php foreach($day_views_args as $day_views) : ?><?php $day_total = M('action')->where("page_id='1' AND user_id='1' AND action_key='day_total' AND date='".$day_views['date']."'")->getField('action_value'); if($day_total>0) : echo $day_total; else : echo 0; endif; ?>,<?php endforeach; ?>]
				}
			]
			
		}
	var myLine = new Chart(document.getElementById("canvas").getContext("2d")).Bar(barChartData);
	</script>
<?php mc_template_part('footer'); ?>