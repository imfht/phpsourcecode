/**
 * 布局描述
 * @author 牧羊人
 * @since 2020/7/4
 */
layui.use(['function'], function () {
    var func = layui.function,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'loc_desc', width: 250, title: '位置描述', align: 'center'}
            , {field: 'loc_id', width: 150, title: '位置编号', align: 'center'}
            , {field: 'item_name', width: 150, title: '所属站点', align: 'center'}
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("布局描述", 470, 350);
    }
});
