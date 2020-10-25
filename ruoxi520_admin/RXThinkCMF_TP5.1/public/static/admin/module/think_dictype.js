/**
 * 字典类型
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
            , {field: 'name', width: 300, title: '类型名称', align: 'center'}
            , {field: 'sort', width: 80, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("字典类型", 500, 250);
    }
});
