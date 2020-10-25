/**
 * 字典管理
 * @author 牧羊人
 * @since 2020/7/10
 */
layui.use(['function'], function () {

    //声明变量
    var func = layui.function
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'title', width: 200, title: '字典名称', align: 'center'}
            , {field: 'tag', width: 200, title: '内部标签', align: 'center'}
            , {field: 'value', width: 250, title: '字典值', align: 'center'}
            , {field: 'typeName', width: 100, title: '字典类型', align: 'center'}
            , {field: 'status', width: 100, title: '状态', align: 'center', templet: '#statusTpl'}
            , {field: 'note', width: 200, title: '备注', align: 'center'}
            , {field: 'sort', width: 100, title: '显示顺序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("字典", 750, 550);

        //【设置人员状态】
        func.formSwitch('status', null, function (data, res) {
            console.log("开关回调成功");
        });
    }
});