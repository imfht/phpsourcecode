$(function () {
    //每天的按钮
    $("#btn_loading_test1").click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 1000);
//定义textarea的值
        var str = 'option = { tooltip : { trigger: "axis" },';
        str += 'legend: { data:["分区一","分区二","分区三","分区四","分区五"] },';
        str += 'toolbox: { show : true, feature : { mark : {show: true}, dataView : {show: true, readOnly: false}, magicType : {show: true, type: ["line", "bar", "stack", "tiled"]}, restore : {show: true}, saveAsImage : {show: true} } },';
        str += 'calculable : true,';
        str += 'xAxis : [ { type : "category", boundaryGap : false, data : ["周一","周二","周三","周四","周五","周六","周日"] } ],';
        str += 'yAxis : [ { type : "value" } ],';
        str += 'series : [';
        str += '{ name:"分区一", type:"line", stack: "总量", data:[120, 132, 101, 134, 90, 230, 210] },';
        str += '{ name:"分区二", type:"line", stack: "总量", data:[220, 182, 191, 234, 290, 330, 310] },';
        str += '{ name:"分区三", type:"line", stack: "总量", data:[150, 232, 201, 154, 190, 330, 410] },';
        str += '{ name:"分区四", type:"line", stack: "总量", data:[320, 332, 301, 334, 390, 330, 320] },';
        str += '{ name:"分区五", type:"line", stack: "总量", data:[820, 932, 901, 934, 1290, 1330, 1320]}';
        str += ']';
        str += '};';

        alert('修改前:\n\r' + document.getElementById("code").value);
        //document.write(document.getElementById("code").value);
        //document.getElementById("code").value = str;
        $("textarea").text(str);
        var text_val = $("#code").val();
        alert('修改后:\r\n' + text_val);
        //document.write(document.getElementById("code").value)
        refresh(true);
        重新加载图表
    })
    //测试按钮
    $("#btn_loading_test").click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
            btn.button('reset')
        }, 1000);
        var myChart = echarts.init(document.getElementById('main'));
        myChart.showLoading({
            text: '正在努力的读取数据中...',    //loading话术
        });
        myChart.hideLoading();
        var option = { tooltip: { trigger: "axis" },
            title: {text: '各个分区初审数量', textAlign: 'left'},
            legend: { data: ["一", "二", "三", "四", "五", "六"] },
            toolbox: { show: true, feature: { mark: {show: true}, dataView: {show: true, readOnly: false}, magicType: {show: true, type: ["line", "bar", "stack", "tiled"]}, restore: {show: true}, saveAsImage: {show: true} } },
            calculable: true,
            xAxis: [
                { type: "category", boundaryGap: false, data: ["周一", "周二", "周三", "周四", "周五", "周六", "周日"] }
            ],
            yAxis: [
                { type: "value" }
            ],
            series: [
                { name: "一", type: "line", stack: "总量", data: [120, 132, 101, 134, 90, 230, 210] },
                { name: "二", type: "line", stack: "总量", data: [220, 182, 191, 234, 290, 330, 310] },
                { name: "三", type: "line", stack: "总量", data: [150, 232, 201, 154, 190, 330, 410] },
                { name: "四", type: "line", stack: "总量", data: [320, 332, 301, 334, 390, 330, 320] },
                { name: "五", type: "line", stack: "总量", data: [820, 932, 901, 934, 1290, 1330, 1320]},
                { name: "六", type: "line", stack: "总量", data: [820, 932, 901, 934, 1290, 1330, 1320]}
            ]
        };
        myChart.setOption(option);

    })

    $("#open_close").click(function () {
        var option = document.getElementById("sidebar-code");
        var display_value = option.style.display;
        if (display_value == "block") {
            option.style.display = "none";
        } else {
            option.style.display = "block";
        }
        //$( '#sidebar-code' ).remove();

    })


})

