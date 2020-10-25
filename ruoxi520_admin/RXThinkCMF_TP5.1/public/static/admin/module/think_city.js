/**
 * 行政区域
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
            , {field: 'name', width: 300, title: '城市名称', align: 'left'}
            , {field: 'level', width: 100, title: '城市级别', align: 'center', templet(d) {
                    if (d.level == 1) {
                        // 省份
                        return '<span class="layui-btn layui-btn-normal layui-btn-xs">省份</span>';
                    } else if (d.level == 2) {
                        // 市区
                        return '<span class="layui-btn layui-btn-danger layui-btn-xs">城市</span>';
                    } else if (d.level == 3) {
                        // 区县
                        return '<span class="layui-btn layui-btn-warm layui-btn-xs">县区</span>';
                    } else if(d.level == 4) {
                        // 街道
                        return '<span class="layui-btn layui-bg-cyan layui-btn-xs">街道</span>';
                    }
                    return '';
                }}
            , {field: 'citycode', width: 200, title: '城市编码(区号)', align: 'center'}
            , {field: 'citycode', width: 200, title: '城市编码(区号)', align: 'center'}
            , {field: 'citycode', width: 200, title: '城市编码(区号)', align: 'center'}
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {fixed: 'right', width: 230, title: '功能操作', align: 'left', toolbar: '#toolBar'}
        ];

        //【TREE渲染】
        func.treetable(cols, "tableList", false, 1);

        //【设置弹框】
        func.setWin("城市", 700, 400);
    }
});
