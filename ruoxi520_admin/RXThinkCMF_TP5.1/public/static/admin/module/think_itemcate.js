/**
 * 栏目管理
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
            , {field: 'name', width: 250, title: '栏目名称', align: 'left'}
            , {field: 'cover_url', width: 60, title: '图片', align: 'center', templet: function (d) {
                    var coverStr = "";
                    if (d.cover_url) {
                        coverStr = '<a href="' + d.cover_url + '" target="_blank"><img src="' + d.cover_url + '" height="26" /></a>';
                    }
                    return coverStr;
                }
            }
            , {field: 'item_name', width: 100, title: '站点名称', align: 'center'}
            , {field: 'pinyin', width: 150, title: '拼音', align: 'center'}
            , {field: 'code', width: 100, title: '简拼', align: 'center'}
            , {field: 'note', width: 200, title: '备注', align: 'center'}
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 230, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.treetable(cols, 'tableList');

        //【设置弹框】
        func.setWin("栏目", 700, 650);
    }
});