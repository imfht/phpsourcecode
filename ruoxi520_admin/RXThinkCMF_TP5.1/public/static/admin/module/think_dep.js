/**
 * 部门管理
 * @author 牧羊人
 * @since 2020/7/10
 */
layui.use(['function'], function () {

    //【声明变量】
    var func = layui.function
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {field: 'id', width: 80, title: 'ID', align: 'center'}
            , {field: 'name', width: 300, title: '部门名称', align: 'left'}
            , {field: 'type', width: 100, title: '类型', align: 'center', templet(d) {
                    if (d.type == 1) {
                        // 公司
                        return '<span class="layui-btn layui-btn-normal layui-btn-xs">公司</span>';
                    } else if (d.type == 2) {
                        // 部门
                        return '<span class="layui-btn layui-btn-danger layui-btn-xs">部门</span>';
                    }
                    return '';
                }}
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 230, title: '功能操作', align: 'left', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.treetable(cols, 'tableList');

        //【设置弹框】
        func.setWin("部门", 500, 350);
    }
});