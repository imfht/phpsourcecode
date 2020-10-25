<!DOCTYPE html>
<html lang="zh-CN">
<head>
    {include file="public/header"}
</head>
<body>
{// 引入顶部导航文件}
{include file="public/topbar"}

<div class="viewFramework-body viewFramework-sidebar-full">
    {// 引入左侧导航文件}
    {include file="public/sidebar"}
    <!-- 主体内容 开始 -->
    <div class="viewFramework-product viewFramework-product-col-1">
        <!-- 中间导航 开始 viewFramework-product-col-1-->
        {include file="public/product-navbar-finance"}
        <!-- 中间导航 结束 -->
        <div class="viewFramework-product-body">
            <div class="console-container">
                <!--内容开始-->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="console-title console-title-border clearfix">
                            <div class="pull-left">
                                <h5><span>{$title}</span></h5>
                            </div>
                            <div class="pull-right">
                                <a href="javascript:window.location.reload();" class="btn btn-default">
                                    <span class="glyphicon glyphicon-refresh"></span>
                                    <span>刷新</span></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="marginTop15"></div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet light-syc bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject bold uppercase font-green-haze">收款项目</span>
                                    <span class="caption-helper">统计当前六个月</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="shoukxiangmuBZX" style="min-height: 500px;margin: 0 auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="portlet light-syc bordered">
                            <div class="portlet-title">
                                <div class="caption">
                                    <span class="caption-subject bold uppercase font-green-haze">收款金额</span>
                                    <span class="caption-helper">{$dateD}年与{$dateL}年的月份对比</span>
                                </div>
                                <div class="tools">
                                    <span class="collapse">{$dateD}年 <b>{$amountD}</b> 万元</span>
                                    <span class="collapse">{$dateL}年 <b>{$amountL}</b> 万元</span>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div id="shoukuanyear" style="min-height: 500px;margin: 0 auto"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--内容结束-->
            </div>
        </div>
    </div>
</div>

{// 引入底部公共JS文件}
{include file="public/footer"}
<!--Highcharts-6.0.0插件-->
<script src="/assets/plugins/highcharts/code/highcharts.js"></script>
<script src="/assets/plugins/highcharts/code/modules/exporting.js"></script>
<script src="/assets/plugins/highcharts/code/themes/sunset.js"></script>
<script type="text/javascript">
    window.item = '';
    $(document).ready(function() {
        // 当前页面分类高亮
        $("#sidebar-finance").addClass("sidebar-nav-active"); // 大分类
        $("#finance-receivables").addClass("active"); // 小分类
        $("#product-finance-index").addClass("active"); // 小分类

        //饼状图展示收款项目
        Highcharts.chart('shoukxiangmuBZX', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false,
                type: 'pie'
            },
            title: {
                text: '{$qldate}-{$dqdate}',
            },
            //顶部的来源注释
            subtitle: {
                text: '{$aSum}万元'
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
            },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        //enabled: false
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    },
                    showInLegend: true
                }
            },
            series: [{
                //type: 'column',
                name: '占比',
                colorByPoint: true, //颜色变化
                data: {$dArr}

            }],
            credits: {
                enabled: false
            },
        });

        //收款额
        Highcharts.chart('shoukuanyear', {
            chart: {
                type: 'column'
            },
            title: {
                text: '{$dateL}年与{$dateD}年月收款金额对比'
            },
            subtitle: {
                text: false
            },
            xAxis: {
                categories: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '销售额 (万元)'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                '<td style="padding:0"><b>{point.y:.2f} 万元</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: [{
                name: '{$dateD}年',
                data: [{$dateDAm}]

            }, {
                name: '{$dateL}年',
                data: [{$dateLAm}]

            }],
            credits: {
                enabled: false
            },
        });
    });
</script>
</body>
</html>