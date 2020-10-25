/**
 * 考勤数据展示
 *
 * Created by m on 17-5-7.
 */
//获取并渲染数据
function load(data) {
    layer.load();
    http.get('/work/api/clocks', data, function (ret) {
        layer.closeAll('loading');
        if (ret.code === 0) {
            if (ret.data === null) {
                return false;
            }
            layui.use('laytpl', function (laytpl) {
                var getTpl = listTpl.innerHTML;
                laytpl(getTpl).render(ret, function (html) {
                    tableView.innerHTML = html;
                });
            });
        } else {
            layer.msg(data.msg || '考勤数据拉取失败!');
        }
    });
}
//获取考勤详情列表
function loadWorkList(id) {
    layer.load();
    var page = 1;
    http.get('/work/api/workLists/work_id/' + id + '/page/' + page, '', function (ret) {
        layer.closeAll('loading');
        if (ret.code === 0) {
            if (ret.data === null) {
                return false;
            }
            layui.use('laytpl', function (laytpl) {
                var getTpl = workTpl.innerHTML;
                laytpl(getTpl).render(ret, function (html) {
                    $('#workDetailView').html(html);
                });
            });
        } else {
            layer.msg(data.msg || '考勤数据拉取失败!');
        }
    });
}
$(function () {
    layui.use('laydate', function () {
        var laydate = layui.laydate;

        var start = {
            min: '1970-01-01 00:00:00'
            , max: laydate.now()
            , istoday: false
            , choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas;//将结束日的初始值设定为开始日
            }
        };

        var end = {
            min: '1970-01-01 00:00:00'
            , max: laydate.now()
            , istoday: false
            , choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };

        document.getElementById('LAY_demorange_s').onclick = function () {
            start.elem = this;
            laydate(start);
        };
        document.getElementById('LAY_demorange_e').onclick = function () {
            end.elem = this;
            laydate(end);
        };
        load({});
        //条件查询
        $('#selectByWhere').on('click', '', function () {
            var data = {
                page: 1,
                startDate: start.elem.value,
                endDate: end.elem.value
            };
            load(data);
        });
        //打卡详情
        $(document).on('click', '.showDetail', function () {
            var This = this;
            var id = $(this).attr('wid');
            layer.open({
                type: 1,
                //skin: 'layui-layer-rim', //加上边框
                area: ['1000px', '400px'], //宽高
                content: $('#workDetailHtml').html()
            });
            loadWorkList(id);
        });
    });
});

