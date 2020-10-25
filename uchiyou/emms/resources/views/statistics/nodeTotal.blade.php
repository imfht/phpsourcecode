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
<div id="main" style="width: 800px;height:800px;"></div>
</body>

<script type="text/javascript">
// 基于准备好的dom，初始化echarts实例
var myChart = echarts.init(document.getElementById('main'));

// 指定图表的配置项和数据
option = {
    title: {
        text: '部门信息'
    },
    tooltip: {},
    legend: {
        data: ['基本信息', '实际开销']
    },
    radar: {
        // shape: 'circle',
        indicator: [
           { name: '物资总数', max: 650},
           { name: '物资总价值', max: 50000},
           { name: '员工总数', max: 80},
           { name: '故障总数', max: 100},
           { name: '子部门数量', max: 40}
        ]
    },
    series: [{
        name: '部门 & 统计',
        type: 'radar',
        // areaStyle: {normal: {}},
        data : [
             {
                value : [{!! implode(',',$datas) !!}],
                name : '基本信息',
            }
        ]
    }]
};
// 使用刚指定的配置项和数据显示图表。
myChart.setOption(option);
</script>
</html>