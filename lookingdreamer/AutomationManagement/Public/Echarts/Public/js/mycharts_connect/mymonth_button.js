$(document).ready(function () {
    //时间转换比较
    function datetime_to_unix(datetime) {
        var tmp_datetime = datetime.replace(/:/g, '-');
        tmp_datetime = tmp_datetime.replace(/ /g, '-');
        var arr = tmp_datetime.split("-");
        var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4], arr[5]));
        return parseInt(now.getTime() / 1000);
    }

    function unix_to_datetime(unix) {
        var now = new Date(parseInt(unix) * 1000);
        return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }

    // 从客户端传入基本数据
    // var myDate = encodeURI("2014-09-16 23:00:00");
    // var myType = "Verfy";
    // var myPart = "All";

    // 每月的按钮 --Start
    $("#btn_loading_month").click(function () {
        var depart = document.getElementById("trans_value").value;
        // 定义最小日期
        var startDate = "2014-09-03 00:00:00";
        var getdate = document.getElementById("getdateval").value;
        var unix_start = datetime_to_unix(startDate);
        var unix_end = datetime_to_unix(getdate);
        var rex = /^-?\d+$/;
        //先判断点击顺序是否正确(先点击日期,在选择分区),在判断提交的日期是否正确
        //判断提交的顺序-Start
        if (rex.test(depart) == false) {
            $("#btn_loading").attr("data-content", "点击顺序有误:请先选择日期后,再次点击下分区!");
            $('#btn_loading').popover('show');
            setTimeout(function () {
                    $('#btn_loading').popover('hide');
                },
                3000);
            //判断提交的顺序 -End
        } else {
            //判断提交的日期是否正确-Start
            if (unix_end < unix_start) {
                $("#btn_loading").attr("data-content", "您选择的时间小于2014-09-03,从这天开始没有数据!");
                $('#btn_loading').popover('show');
                setTimeout(function () {
                        $('#btn_loading').popover('hide');
                        //  $("#click_buton").attr("data-content", "您选择的时间小于2014-09-03,从这天开始没有数据!");
                    },
                    3000);
                //判断提交的日期是否正确-End
            } else {
                //开始处理提交的数据

                // 从客户端传入基本数据
                var myType = $('#choice option:selected').val();
                var myDate = encodeURI(getdate);
                var myPart = depart;

                //      alert(depart + " => " + getdate + " => " +myType);
                //       alert("开始ajax获取数据!");
                // 移除刷新和切换主题按钮
                $('#flush').remove();
                $('#change_theme').remove();
                $('#theme-select').remove();
                document.getElementById("open_close").style.marginLeft = "0px";

                var btn = $(this)
                btn.button('loading')
                setTimeout(function () {
                    btn.button('reset')
                }, 3500);
                // 初始化图表
                var myChart = echarts.init(document.getElementById('main'));
                myChart.showLoading({
                    text: '正在努力的读取数据中...', // loading话术
                });
                myChart.hideLoading();
                $.post(forwording_url, {
                    query_type: "Month",
                    query_date: myDate,
                    type: myType,
                    depart: myPart,
                }, function (data, status) {
                    if (status) {
                        if (myPart == "0") {
                            var echart_title = data.title + " [" + data.date + "] ";
                        } else {
                            var echart_title = data.title;
                        }
                        var echart_xAxis = data.xAxis;
                        var echart_series = data.series;
                        var echart_legend = data.legend;
                        var option = {
                            tooltip: {
                                trigger: "axis"
                            },
                            title: {
                                text: echart_title,
                                textAlign: 'left'
                            },
                            legend: {
                                data: echart_legend
                            },
                            toolbox: {
                                show: true,
                                feature: {
                                    mark: {
                                        show: true
                                    },
                                    dataView: {
                                        show: true,
                                        readOnly: false
                                    },
                                    magicType: {
                                        show: true,
                                        type: [ "line", "bar", "stack", "tiled" ]
                                    },
                                    restore: {
                                        show: true
                                    },
                                    saveAsImage: {
                                        show: true
                                    }
                                }
                            },
                            calculable: true,
                            xAxis: [
                                {
                                    type: "category",
                                    boundaryGap: false,
                                    data: echart_xAxis
                                }
                            ],
                            yAxis: [
                                {
                                    type: "value"
                                }
                            ],
                            series: echart_series
                        };
                        myChart.setOption(option);
                    } else {
                        alert("异步返回数据失败！");
                    }
                    ;
                }, 'json');
            }//提交的日期正确-End
        }
        //判断提交的日期是否正确-End

    });
// // 每天的按钮 --End


//大结局在这
});