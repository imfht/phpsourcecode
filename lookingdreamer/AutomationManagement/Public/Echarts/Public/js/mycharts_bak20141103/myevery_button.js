$(document).ready(function () {
    //定义图表对象
    var echart_title = "各个分区初审数量";     //标题
    var echart_legend = ["一区", "二区", "三区", "四区", "五区", "六区"];			//图例
    var echart_xAxis = ["周一", "周二", "周三", "周四", "周五", "周六", "周日"];			//X轴上的值
    var echart_series = [
        { name: "一", type: "line", stack: "总量", data: [120, 132, 101, 134, 90, 230, 210] },
        { name: "二", type: "line", stack: "总量", data: [220, 182, 191, 234, 290, 330, 310] },
        { name: "三", type: "line", stack: "总量", data: [150, 232, 201, 154, 190, 330, 410] },
        { name: "四", type: "line", stack: "总量", data: [320, 332, 301, 334, 390, 330, 320] },
        { name: "五", type: "line", stack: "总量", data: [820, 932, 901, 934, 1290, 1330, 1320]},
        { name: "六", type: "line", stack: "总量", data: [820, 932, 901, 934, 1290, 1330, 1320]}
    ];													//Y轴上的值

    //从客户端传入基本数据
    var myDate = encodeURI("2014-09-16 23:00:00");
    var myType = "Verfy";
    var myPart = "All";
    //每月的按钮逻辑处理
    $("#btn_loading_month").click(function () {
        //移除刷新和切换主题按钮 -start
        $('#flush').remove();
        $('#change_theme').remove();
        $('#theme-select').remove();
        document.getElementById("open_close").style.marginLeft = "0px";
        //移除刷新和切换主题按钮 -end

        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 1000);
        //初始化图表
        var myChart = echarts.init(document.getElementById('main'));
        myChart.showLoading({
            text: '正在努力的读取数据中...',    //loading话术
        });
        myChart.hideLoading();

        $.post(forwording_url,
            {
                query_type: "Month",
                query_date: myDate,
                type: myType,
                depart: myPart,
            },
            function (data, status) {
                if (status) {
                    var echart_title = data.title;
                    var echart_xAxis = data.xAxis;
                    var echart_series = data.series;
                    var option = { tooltip: { trigger: "axis" },
                        title: {text: echart_title, textAlign: 'left'},
                        legend: { data: echart_legend},
                        toolbox: { show: true, feature: { mark: {show: true}, dataView: {show: true, readOnly: false}, magicType: {show: true, type: ["line", "bar", "stack", "tiled"]}, restore: {show: true}, saveAsImage: {show: true} } },
                        calculable: true,
                        xAxis: [
                            { type: "category", boundaryGap: false, data: echart_xAxis }
                        ],
                        yAxis: [
                            { type: "value" }
                        ],
                        series: echart_series
                    };
                    myChart.setOption(option);
                } else {
                    alert("异步返回数据失败！");
                }
                ;
            });
    });

    //每周的按钮
    $("#btn_loading_week").click(function () {
        //移除刷新和切换主题按钮
        $('#flush').remove();
        $('#change_theme').remove();
        $('#theme-select').remove();
        document.getElementById("open_close").style.marginLeft = "0px";

        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 1000);
        //初始化图表
        var myChart = echarts.init(document.getElementById('main'));
        myChart.showLoading({
            text: '正在努力的读取数据中...',    //loading话术
        });
        myChart.hideLoading();
        myChart.setOption(option);
        $.post(forwording_url, {
                query_type: "Week",
                query_date: myDate,
                type: myType,
                depart: myPart,
            },
            function (data, status) {
                alert("数据：" + data.title + "\n状态：" + status);
            });
    });

    //每天的按钮
    $("#btn_loading").click(function () {
        //移除刷新和切换主题按钮
        $('#flush').remove();
        $('#change_theme').remove();
        $('#theme-select').remove();
        document.getElementById("open_close").style.marginLeft = "0px";

        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 3500);
        //初始化图表
        var myChart = echarts.init(document.getElementById('main'));
        myChart.showLoading({
            text: '正在努力的读取数据中...',    //loading话术
        });
        myChart.hideLoading();
        $.post(forwording_url, {
                query_type: "Day",
                query_date: myDate,
                type: myType,
                depart: myPart,
            },
            function (data, status) {
                if (status) {
                    var echart_title = data.title + " [" + data.date + "] ";
                    var echart_xAxis = data.xAxis;
                    var echart_series = data.series;
                    var option = { tooltip: { trigger: "axis" },
                        title: {text: echart_title, textAlign: 'left'},
                        legend: { data: echart_legend},
                        toolbox: { show: true, feature: { mark: {show: true}, dataView: {show: true, readOnly: false}, magicType: {show: true, type: ["line", "bar", "stack", "tiled"]}, restore: {show: true}, saveAsImage: {show: true} } },
                        calculable: true,
                        xAxis: [
                            { type: "category", boundaryGap: false, data: echart_xAxis }
                        ],
                        yAxis: [
                            { type: "value" }
                        ],
                        series: echart_series
                    };
                    myChart.setOption(option);
                } else {
                    alert("异步返回数据失败！");
                }
                ;
            }, 'json');

    });


});