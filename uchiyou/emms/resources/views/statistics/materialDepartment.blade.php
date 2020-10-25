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

var option = {
	    title : {
	        text: '部门-物资数量(件)',
	        subtext: '当前管理员管理的直接部门',
	        x:'center'
	    },
	    tooltip : {
	        trigger: 'item',
	        formatter: "{a} <br/>{b} : {c} ({d}%)"
	    },
	    legend: {
	        orient: 'vertical',
	        left: 'left',
	        data: [
		        @foreach ($datas as $data)
	        	'{!!$data['name']!!}',
	        	@endforeach]
	    },
	    series : [
	        {
	            name: '物资数量',
	            type: 'pie',
	            radius : '55%',
	            center: ['50%', '60%'],
	            data:[
	            	@foreach ($datas as $data)
	            	{value:{!!$data['value']!!},name:'{!!$data['name']!!}'},
	            	@endforeach
	            ],
	            itemStyle: {
	                emphasis: {
	                    shadowBlur: 10,
	                    shadowOffsetX: 0,
	                    shadowColor: 'rgba(0, 0, 0, 0.5)'
	                }
	            }
	        }
	    ]
	};



// 使用刚指定的配置项和数据显示图表。
chart.setOption(option);

</script>
</body>
</html>