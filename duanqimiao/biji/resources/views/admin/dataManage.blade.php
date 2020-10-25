<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--移动或响应式web页面缩放设置--}}
    <meta name="viewport" content="width=device-width,initial-scale=0.4,maximum-scale=1.0,user-scalable=no">
    <title>笔友 | Be yourself</title>
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css" rel="stylesheet">
    <script language="JavaScript" src="{{ URL::asset('/') }}js/jquery.js"></script>

    {{--引入Chart.js文件--}}
    <script language="JavaScript" src="{{ URL::asset('/') }}js/Chart.js"></script>

    <script src="{{ URL::asset('/') }}js/echarts.js"></script>
    <script src="{{ URL::asset('/') }}js/macarons.js"></script>

</head>
<body>
<div>
    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    <div id="main" style="width: 850px;height:400px;"></div>
    <script type="text/javascript">
        $.ajax({
            type: "GET",
            url: "/admin/dataManage/chart",
            success:function(active){
                // 第二个参数可以指定前面引入的主题
                var chart = echarts.init(document.getElementById('main'), 'macarons');
                // 指定图表的配置项和数据
                chart.setOption({
                    title : {
                        text: '发表笔记活跃图',
                        subtext: '笔友圈',
                        x:'center'
                    },
                    tooltip : {
                        trigger: 'item',
                        formatter: "{a} <br/>{b} : {c} ({d}%)"
                    },
                    legend: {
                        orient : 'vertical',
                        x : 'left',
                        data:['第一名 '+active.counts[0][0],'第二名 '+active.counts[1][0],'第三名 '+active.counts[2][0]]
                    },
                    toolbox: {
                        show : true,
                        feature : {
                            mark : {show: true},
                            dataView : {show: true, readOnly: false},
                            magicType : {
                                show: true,
                                type: ['pie', 'funnel'],
                                option: {
                                    funnel: {
                                        x: '25%',
                                        width: '50%',
                                        funnelAlign: 'left',
                                        max: 1548
                                    }
                                }
                            },
                            restore : {show: true},
                            saveAsImage : {show: true}
                        }
                    },
                    calculable : true,
                    series : [
                        {
                            name:'发表笔记数',
                            type:'pie',
                            radius : '55%',
                            center: ['50%', '60%'],
                            data:[
                                {value:active.counts[0][1], name:'第一名 '+active.counts[0][0]},
                                {value:active.counts[1][1], name:'第二名 '+active.counts[1][0]},
                                {value:active.counts[2][1], name:'第三名 '+active.counts[2][0]}
                            ]
                        }
                    ]
                });

            }
        }) ;
    </script>
</div>
</body>
</html>