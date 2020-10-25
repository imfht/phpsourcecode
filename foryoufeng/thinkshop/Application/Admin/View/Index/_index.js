$(function() {
    if (!$('body').hasClass("body-index")) {
        return false;
    }

    // 用户增长曲线图
    var chart_data = {
        labels: $_var_index.user_reg_date,
        datasets: [{
            label: "用户增长曲线图",
            fillColor: "rgba(151,187,205,0.2)",
            strokeColor: "rgba(151,187,205,1)",
            pointColor: "rgba(151,187,205,1)",
            pointStrokeColor: "#fff",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(151,187,205,1)",
            data: $_var_index.user_reg_count
        }]
    };
    var chart_options = {
        scaleLineColor: "rgba(0,0,0,.1)", //X/Y轴的颜色
        scaleLineWidth: 1 //X/Y轴的宽度
    };

    var chart_element = document.getElementById("mychart").getContext("2d");
    var myLine = new Chart(chart_element).Line(chart_data, chart_options);



    //图表时间段选择
    $('#daterange_set').daterangepicker({
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            minDate: '01/01/2015',
            maxDate: '12/31/2100',
            dateLimit: {
                days: 360
            },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                '最近7天': [moment().subtract(6, 'days'), moment()],
                '这个月': [moment().startOf('month'), moment().endOf('month')],
                '上个月': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            buttonClasses: ['btn btn-default'],
            applyClass: 'btn-sm btn-primary',
            cancelClass: 'btn-sm',
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: '确定',
                cancelLabel: '取消',
                fromLabel: '开始',
                toLabel: '结束',
                customRangeLabel: '自定义',
                daysOfWeek: ['日', '一', '二', '三', '四', '五', '六'],
                monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'],
                firstDay: 1
            }
        },

        function(start, end, label) {
            var url = $_var_index.index_url;
            var query = 'start_date=' + start + '&end_date=' + end;
            if (url.indexOf('?') > 0) {
                url += '&' + query;
            } else {
                url += '?' + query;
            }
            window.location.href = url;
        }
    );



    //检测更新
    $.ajax({
        url: $_var_index.check_version_url,
        type: 'GET',
    }).done(function(data) {
        if (data.status == 1) {
            $('.update').html(data.info);
        } else {
            $.alertMessager(data.info, 'danger');
        }
    });


});