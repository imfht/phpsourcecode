/**
 * 通知公告
 * @author 牧羊人
 * @since 2020/7/11
 */
layui.use(['function'], function () {

    //声明变量
    var func = layui.function
        , form = layui.form
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
              {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'title', width: 350, title: '通知标题', align: 'center'}
            , {field: 'source', width: 100, title: '通知来源', align: 'center', templet(d) {
                var cls = "";
                if (d.source == 1) {
                    // 云平台
                    cls = "layui-btn-normal";
                } 
				return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.source_name+'</span>';
            }}
            , {field: 'is_top', width: 100, title: '是否置顶', align: 'center', templet(d) {
                var cls = "";
                if (d.is_top == 1) {
                    // 已置顶
                    return '<span class="layui-btn layui-btn-normal layui-btn-xs">已置顶</span>';
                } else {
                    // 未置顶
                    return '<span class="layui-btn layui-btn-danger layui-btn-xs">未置顶</span>';
                } 
            }}
            , {field: 'view_num', width: 100, title: '阅读量', align: 'center'}
            , {field: 'status', width: 100, title: '发布状态', align: 'center', templet(d) {
                var cls = "";
                if (d.status == 1) {
                    // 草稿箱
                    cls = "layui-btn-normal";
                } else if (d.status == 2) {
                    // 立即发布
                    cls = "layui-btn-danger";
                } else if (d.status == 3) {
                    // 定时发布
                    cls = "layui-btn-warm";
                } 
				return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.status_name+'</span>';
            }}
            , {field: 'publish_time', width: 180, title: '发布时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.publish_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'is_send', width: 100, title: '推送状态', align: 'center', templet(d) {
                var cls = "";
                if (d.is_send == 1) {
                    // 已推送
                    return '<span class="layui-btn layui-btn-normal layui-btn-xs">已推送</span>';
                } else {
                    // 未推送
                    return '<span class="layui-btn layui-btn-danger layui-btn-xs">未推送</span>';
                }
            }}
            , {field: 'send_time', width: 180, title: '推送时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.send_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'create_user_name', width: 100, title: '添加人', align: 'center'}
            , {field: 'create_time', width: 180, title: '添加时间', align: 'center'}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center'}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("通知公告");

        //【设置状态】
        func.formSwitch('status', null, function (data, res) {
            console.log("开关回调成功");
        });
    } else {
        //监听推荐类型
        var status = $("#status").val();
        console.log("已选择："+status);
        if (status == 3) {
            // 显示
            $(".publishTime").removeClass("layui-hide");
        } else {
            // 隐藏
            $(".publishTime").addClass("layui-hide");
        }
        form.on('select(status)', function (data) {
            status = data.value;
            console.log(status);
            if (status == 3) {
                // 显示
                $(".publishTime").removeClass("layui-hide");
            } else {
                // 隐藏
                $(".publishTime").addClass("layui-hide");
            }
        });
    }
});
