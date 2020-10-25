<?php
error_reporting(0);
// 初始化
include('function.php');
?>
<!DOCTYPE html>
<html style="height: 100%">
<head>
    <meta charset="utf-8">
    <title>肺炎感染演示效果图</title>
    <!-- 引入 echarts.js -->
    <script src="https://cdn.bootcss.com/echarts/4.4.0-rc.1/echarts.js"></script>
    <script src="https://cdn.bootcss.com/jquery/2.2.4/jquery.js"></script>
</head>
<body style="height: 100%; margin: 0">
    <div id="main" style="height: 100%;width:100%"> </div>
    <script type="text/javascript">
        var myChart = echarts.init(document.getElementById('main'),'dark');
    myChart.hideLoading();

    var option = {
    color:['#008000','#FF8C00','#DC143C','#FFF'],
    title: {
        text: '病毒感染传播演示效果 |【第<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['day'] ?>天】',
        subtext: '本样本数据由程序随机生成，请勿类比到真实情况当中！\n\n当前：\n健康人数：<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['people']; ?>人\n疑似人数：<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['yisi']; ?>人\n确诊人数：<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['quezhen']; ?>人\n医疗队伍：<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['h_num']; ?>人\n\n开发作者：clark',
    },
    grid: {
        left: '10%',
        right: '5%',
        bottom: '3%',
        containLabel: true
    },
    tooltip: {
        // trigger: 'axis',
        showDelay: 0,
        formatter: function (params) {
            if (params.value.length > 1) {
                return params.seriesName;
            }
            else {
                return params.seriesName + ' :<br/>'
                + params.name + ' : '
                + params.value + '活动范围 ';
            }
        },
    },
    toolbox: {
        itemSize: 30,
        itemGap: 20,
        feature: {
            // dataZoom: {},
            // brush: {
            //     type: ['rect', 'polygon', 'clear']
            // },
            myTools1:{  
                show: true,  
                title:'初始化',
                textAlign:'left',                       
                text: '初始化',
                icon:'image://img/home.png', 
                onclick:function(o){
                    window.location.href="index.php";
                }    
            },
            myTools2:{  
                show: true,  
                title:'下一日',
                textAlign:'left',                       
                text: '下一日',
                icon:'image://img/next.png', 
                onclick:function(o){
                    window.location.href="index.php?day=<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['day']+1?>&yisi=<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['yisi']?>&quezhen=<?php echo startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['quezhen']?>";
                }    
            },
            myTools3:{  
                show: true,  
                title:'刷新数据',
                textAlign:'left',                       
                text: '刷新数据',
                icon:'image://img/refresh.png', 
                onclick:function(o){
                    location.reload();
                }    
            },
            myTools4:{  
                show: true,  
                title:'源码及介绍',
                textAlign:'left',                       
                text: '源码及介绍',
                icon:'image://img/code.png', 
                onclick:function(o){
                    window.location.href="https://gitee.com/dongyao/virus";
                }    
            },
        }
    },
    // brush: {
    // },
    legend: {
        data: ['健康', '疑似','确诊','医生'],
        left: 'center'
    },
    xAxis: [
        {
            type: 'value',
            scale: true,
            axisLabel: {
                formatter: '{value} km'
            },
            splitLine: {
                show: false
            }
        }
    ],
    yAxis: [
        {
            type: 'value',
            scale: true,
            axisLabel: {
                formatter: '{value} km'
            },
            splitLine: {
                show: false
            }
        }
    ],
    series: [
        {
            name: '健康',
            type: 'scatter',
            symbolSize: 5,
            data: <?php echo get_data(startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['people']); ?>,
        },
        {
            name: '疑似',
            type: 'scatter',
            symbolSize: 5,
            data: <?php echo get_data(startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['yisi']); ?>,
        },
        {
            name: '确诊',
            type: 'scatter',
            symbolSize: 10,
            data: <?php echo get_data(startSet($_GET['day'],$_GET['yisi'],$_GET['quezhen'])['quezhen']); ?>,
        }
        ,
        {
            name: '医生',
            type: 'scatter',
            symbolSize: 10,
            data: <?php echo get_data(startSet($_GET['day'])['h_num']); ?>,
        }
    ]
};

        // 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option);
    </script>
</body >
</html>