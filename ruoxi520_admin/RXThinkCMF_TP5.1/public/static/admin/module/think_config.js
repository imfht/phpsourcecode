/**
 * 配置管理
 * @author 牧羊人
 * @since 2020/7/10
 */
layui.use(['function', 'form'], function () {

    //【声明变量】
    var func = layui.function
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'name', width: 150, title: '配置名称', align: 'center'}
            , {field: 'title', width: 150, title: '配置标题', align: 'center'}
            , {field: 'type', width: 200, title: '配置类型', align: 'center', templet: function (d) {
                    return d.type + " | " + d.type_name;
                }
            }
            , {field: 'status', width: 100, title: '状态', align: 'center', templet: function (d) {
                    var str = "";
                    if (d.status == 1) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs">在用</span>';
                    } else {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
                    }
                    return str;
                }
            }
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_user_name', width: 100, title: '创建人', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        // 请求地址
        var group_id = $("#group_id").val();
        var url = cUrl + "/index?group_id=" + group_id;

        //【渲染TABLE】
        func.tableIns(cols, "tableList", null, url);

        //【设置弹框】
        func.setWin("配置项", 700, 650);

    }
});
