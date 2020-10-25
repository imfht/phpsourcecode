/**
 * 菜单管理
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
            , {field: 'name', width: 200, title: '菜单名称', align: 'left'}
            , {field: 'icon', width: 100, title: '图标', align: 'center', templet: '<p><i class="layui-icon {{d.icon}}"></i></p>'}
            , {field: 'type', width: 80, title: '类型', align: 'center', templet(d) {
                    var cls = "";
                    if (d.type == 1) {
                        // 模块
                        cls = "layui-btn-normal";
                    } else if (d.type == 2) {
                        // 导航
                        cls = "layui-btn-danger";
                    } else if (d.type == 3) {
                        // 菜单
                        cls = "layui-btn-warm";
                    } else if (d.type == 4) {
                        // 节点
                        cls = "layui-btn-primary";
                    }
                    return '<span class="layui-btn ' + cls + ' layui-btn-xs">'+d.type_name+'</span>';
                }}
            , {field: 'url', width: 200, title: '菜单地址', align: 'center'}
            , {field: 'permission', width: 200, title: '权限标识', align: 'center'}
            , {field: 'status', width: 100, title: '状态', align: 'center', templet(d) {
                    return (d.status == 1 ? '<span class="layui-btn layui-btn-normal layui-btn-xs">显示</span>' : '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">不显示</span>');
                }
            }
            , {field: 'is_public', width: 100, title: '是否公共', align: 'center', templet(d) {
                    return d.is_public == 1 ? "是" : "否";
                }
            }
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {fixed: 'right', width: 220, title: '功能操作', align: 'left', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.treetable(cols, 'tableList');

        //【设置弹框】
        func.setWin("菜单", 700, 600);
    }
});