/**
 * 广告管理
 * @author 牧羊人
 * @since 2020/7/10
 */
layui.use(['form', 'function'], function () {
    var form = layui.form,
        func = layui.function,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'cover_url', width: 60, title: '封面', align: 'center', templet: function (d) {
                    return '<a href="' + d.cover_url + '" target="_blank"><img src="' + d.cover_url + '" height="26" /></a>';
                }
            }
            , {field: 'title', width: 200, title: '广告标题', align: 'center'}
            , {field: 'type_name', width: 100, title: '广告类型', align: 'center'}
            , {field: 'sort_name', width: 200, title: '广告位', align: 'center'}
            , {field: 'description', width: 300, title: '描述', align: 'center'}
            , {field: 'sort', width: 80, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("广告");
    }
});

