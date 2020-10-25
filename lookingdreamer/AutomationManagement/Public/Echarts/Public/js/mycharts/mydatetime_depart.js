$(function () {
    var datetimepicker = $('#datetimepicker2').datetimepicker({
        language: 'zh-CN',
        pickDate: true,
        pickTime: true
    });

    function datetime_to_unix(datetime) {
        var tmp_datetime = datetime.replace(/:/g, '-');
        tmp_datetime = tmp_datetime.replace(/ /g, '-');
        var arr = tmp_datetime.split("-");
        var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8,
            arr[4], arr[5]));
        return parseInt(now.getTime() / 1000);
    }

    function unix_to_datetime(unix) {
        var now = new Date(parseInt(unix) * 1000);
        return now.toLocaleString().replace(/年|月/g, "-").replace(/日/g, " ");
    }

    // 下拉框数据
    var contents = [
        {
            value: 1,
            title: "一分区",
            selected: true
        },
        {
            value: 2,
            title: "二分区",
            selected: false
        },
        {
            value: 3,
            title: "三分区",
            selected: false
        },
        {
            value: 4,
            title: "四分区",
            selected: false
        },
        {
            value: 5,
            title: "五分区",
            selected: false
        },
        {
            value: 6,
            title: "六分区",
            selected: false
        },
        {
            value: 0,
            title: "所有分区",
            selected: false
        },
    ];
    // 显示下拉框
    $("#mySelect").select({

        contents: contents
    });

    $("#choice_test").click(function () {
        var vala = $('#choice option:selected').val();
        alert(vala);
    });
    // Go按钮点击事件
    $("#click_buton").click(
        function () {
            var depart = document.getElementById("trans_value").value;
            // 定义最小日期
            var startDate = "2014-09-03 00:00:00";
            var getdate = document.getElementById("getdateval").value;
            var unix_start = datetime_to_unix(startDate);
            var unix_end = datetime_to_unix(getdate);
            var rex = /^-?\d+$/;
            alert(depart + " => " + getdate);
            // 先判断点击顺序是否正确(先点击日期,在选择分区),在判断提交的日期是否正确
            if (rex.test(depart) == false) {
                alert(222);
                $("#click_buton").attr("data-content", "选择日期后,请再次点下分区!");
                $('#click_buton').popover('show');
                setTimeout(function () {
                    $('#click_buton').popover('hide');
                    $("#click_buton").attr("data-content",
                        "您选择的时间小于2014-09-03,从这天开始没有数据!");
                }, 3000);

            } else {
                if (unix_end < unix_start) {
                    alert(111);
                    $('#click_buton').popover('show');
                    setTimeout(function () {
                        $('#click_buton').popover('hide');
                    }, 3000);
                } else {
                    $('#click_buton').popover('hide');
                    // 次数开始ajax提交数据
                    alert("开始ajax获取数据!");
                }
            }

        });
});