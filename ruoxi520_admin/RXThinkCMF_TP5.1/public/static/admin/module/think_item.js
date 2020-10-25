/**
 * 站点管理
 * @author 牧羊人
 * @since 2020/7/10
 */
layui.use(['function'], function () {
    var func = layui.function,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {field: 'image_url', width: 60, title: '封面', align: 'center', templet: function (d) {
                    return '<a href="' + d.image_url + '" target="_blank"><img src="' + d.image_url + '" height="26" /></a>';
                }
            }
            , {field: 'name', width: 150, title: '站点名称', align: 'center'}
            , {field: 'type_name', width: 150, title: '站点类型', align: 'center'}
            , {field: 'url', width: 250, title: '站点地址', align: 'center'}
            , {field: 'is_domain', width: 100, title: '二级域名', align: 'center', templet: function (d) {
                    var str = "";
                    if (d.is_domain == 1) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs">是</span>';
                    } else {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">否</span>';
                    }
                    return str;
                }
            }
            , {field: 'status', width: 100, title: '状态', align: 'center', templet: function (d) {
                    var str = "";
                    if (d.status == 1) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs">可用</span>';
                    } else {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">不可用</span>';
                    }
                    return str;
                }
            }
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("站点", 700, 650);
    }
});