<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>统计-物资管家</title>
<link href="{{ asset('css/styles/userModify.css') }}"
	rel="stylesheet">
<!-- 引入 echarts.js -->
<script src="{{ asset('js/plugins/charts/echarts.js') }}"></script>

</head>
<body>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div class="center" id="main" style="width: 1000px;height:500px;"></div>
<script type="text/javascript">
var chart = echarts.init(document.getElementById('main'));

// 指定图表的配置项和数据
var option = {
	title: {
		text: '{{$title}}'
	},
	tooltip: {},
	legend: {
		data:['次数']
	},
	xAxis: {
		data: {!! $names !!}
	},
	yAxis: {},
	series: [{
		name: '次数',
		type: 'bar',
		data: {!! $counts !!}
	}]
};

// 使用刚指定的配置项和数据显示图表。
chart.setOption(option);

</script>
</body>
</html>