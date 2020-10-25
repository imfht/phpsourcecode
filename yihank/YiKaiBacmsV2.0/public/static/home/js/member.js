/*
 * @Author: Paco
 * @Date:   2017-02-22
 * +----------------------------------------------------------------------
 * | jqadmin [ jq酷打造的一款懒人后台模板 ]
 * | Copyright (c) 2017 http://jqadmin.jqcool.net All rights reserved.
 * | Licensed ( http://jqadmin.jqcool.net/licenses/ )
 * | Author: Paco <admin@jqcool.net>
 * +----------------------------------------------------------------------
 */

layui.define('echarts', function(exports) {
    var echarts = layui.echarts,
        $ = layui.jquery,
        option = {
            title: {
                text: '某站点用户男女比例',
                subtext: '纯属虚构',
                x: 'center'
            },
            tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b} : {c} ({d}%)"
            },
            legend: {
                orient: 'vertical',
                left: 'left',
                data: ['男', '女']
            },
            series: [{
                name: '访问来源',
                type: 'pie',
                radius: '55%',
                center: ['50%', '60%'],
                data: [
                    { value: 1335, name: '男' },
                    { value: 310, name: '女' }
                ],
                itemStyle: {
                    emphasis: {
                        shadowBlur: 10,
                        shadowOffsetX: 0,
                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                    }
                }
            }]
        };
    var myecharts = echarts.init(document.getElementById('afbces'));
    myecharts.setOption(option);


    var myChart = echarts.init(document.getElementById('area'));

    // 指定图表的配置项和数据
    var option = {
        title: {
            text: '地区会员统计',
            subtext: '纯属虚构',
            x: 'center'
        },
        tooltip: {
            trigger: 'item',
            formatter: "{a} <br/>{b} : {c} ({d}%)"
        },
        legend: {
            orient: 'vertical',
            left: 'left',
            data: ['广州', '佛山', '东莞', '中山', '深圳']
        },
        series: [{
            name: '地区会员数量',
            type: 'pie',
            radius: '55%',
            center: ['50%', '60%'],
            data: [
                { value: 6335, name: '广州' },
                { value: 4310, name: '佛山' },
                { value: 2310, name: '东莞' },
                { value: 3310, name: '中山' },
                { value: 9310, name: '深圳' }
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }]
    };

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);

    var actived = echarts.init(document.getElementById('actived'));

    var option = {
        title: {
            text: '周活跃度',
            subtext: '纯属虚构',
            x: 'center'
        },
        color: ['#3398DB'],
        tooltip: {
            trigger: 'axis',
            axisPointer: { // 坐标轴指示器，坐标轴触发有效
                type: 'shadow' // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis: [{
            type: 'category',
            data: ['周一', '周二', '周三', '周四', '周五', '周六', '周日'],
            axisTick: {
                alignWithLabel: true
            }
        }],
        yAxis: [{
            type: 'value'
        }],
        series: [{
            name: '活跃度',
            type: 'bar',
            barWidth: '60%',
            data: [10, 52, 200, 334, 390, 330, 220]
        }]
    };

    actived.setOption(option);

    exports('member', {});
});