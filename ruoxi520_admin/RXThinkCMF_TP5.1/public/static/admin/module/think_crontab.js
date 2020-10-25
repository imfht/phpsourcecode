/**
 * 定时任务
 * @author 牧羊人
 * @since 2020/7/4
 */
layui.use(['function'], function () {

    //【声明变量】
    var func = layui.function
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'title', width: 150, title: '任务标题', align: 'center'}
            , {field: 'type_name', width: 100, title: '任务类型', align: 'center'}
            , {field: 'schedule', width: 300, title: '任务脚本', align: 'center'}
            , {field: 'maximums', width: 100, title: '最多执行', align: 'center'}
            , {field: 'executes', width: 100, title: '执行次数', align: 'center'}
            , {field: 'weigh', width: 100, title: '权重', align: 'center'}
            , {field: 'status', width: 100, title: '状态', align: 'center', templet: '#statusTpl'}
            , {field: 'start_time', width: 180, title: '开始时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.start_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'end_time', width: 180, title: '结束时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.end_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'execute_time', width: 180, title: '最后执行时间', align: 'center',templet:'<div>{{ layui.util.toDateString(d.execute_time, "yyyy-MM-dd HH:mm:ss") }}</div>'}
            , {field: 'sort', width: 80, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center', sort: true}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("定时任务", 700, 530);

        //【设置人员状态】
        func.formSwitch('status', null, function (data, res) {
            console.log("开关回调成功");
        });
    }
});
