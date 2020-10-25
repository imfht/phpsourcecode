<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>物资管家</title>
<!-- 引入 echarts.js -->
<script src="{{ asset('js/plugins/charts/echarts.js') }}"></script>
</head>
<body>
<!-- 为ECharts准备一个具备大小（宽高）的Dom -->
<div id="topAppointment" style="width: 600px;height:400px;"></div>
<div id="topRent" style="width: 600px;height:400px;"></div>
<script type="text/javascript">
//----------- 预约次数的 top柱状图
var topAppointmentChart = echarts.init(document.getElementById('topAppointment'));

// 指定图表的配置项和数据
var option = {
	title: {
		text: '预约 top10 的物资'
	},
	tooltip: {},
	legend: {
		data:['预约次数']
	},
	xAxis: {
		data: {!! $appointmentNames !!}
	},
	yAxis: {},
	series: [{
		name: '预约次数',
		type: 'bar',
		data: {!! $appointmentCounts !!}
	}]
};

// 使用刚指定的配置项和数据显示图表。
topAppointmentChart.setOption(option);

//------------ 使用次数的 top柱状图
var topRentChart = echarts.init(document.getElementById('topRent'));

// 指定图表的配置项和数据
var option = {
	title: {
		text: '租用频率 top10 的物资'
	},
	tooltip: {},
	legend: {
		data:['租用次数']
	},
	xAxis: {
		data: {!! $rentNames !!}
	},
	yAxis: {},
	series: [{
		name: '租用次数',
		type: 'bar',
		data: {!! $rentCounts !!}
	}]
};

// 使用刚指定的配置项和数据显示图表。
topRentChart.setOption(option);
</script>
</body>
</html>