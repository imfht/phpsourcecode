/**
 * 数据库管理
 * @author 牧羊人
 * @since 2020/7/11
 */
layui.use(['function'], function () {

    //【声明变量】
    var func = layui.function
        , $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'name', width: 250, title: '表名', align: 'center'}
            , {field: 'engine', width: 100, title: '引擎', align: 'center'}
            , {field: 'version', width: 100, title: '版本', align: 'center'}
            , {field: 'collation', width: 200, title: '编码', align: 'center'}
            , {field: 'rows', width: 100, title: '记录数', align: 'center'}
            , {field: 'data_length', width: 100, title: '大小', align: 'center'}
            , {field: 'auto_increment', width: 100, title: '自增索引', align: 'center'}
            , {field: 'comment', width: 250, title: '表备注', align: 'center'}
            , {field: '', width: 100, title: '状态', align: 'center', templet: function (d) {
                    return '未备份';
                }
            }
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 180, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【渲染TABLE】
        func.tableIns(cols, "tableList", function (layEvent, data) {
            if (layEvent === 'optimize') {
                // 优化表
                layer.msg("优化表");
            } else if (layEvent === 'repair') {
                // 修复表
                layer.msg("修复表");
            }
        });

        //【立即备份】
        $(".btnBackup").click(function () {
            layer.msg("立即备份");
            //$(this).parent().children().addClass("disabled");
            $(this).html("正在发送备份请求...");

            return false;
        });

        //【优化表】
        $(".btnOptimize").click(function () {
            layer.msg("优化表");
        });

        //【修复表】
        $(".btnRepair").click(function () {
            layer.msg("修复表");
        });
    }
});