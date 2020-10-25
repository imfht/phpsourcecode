/**
 * 友链管理
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
            , {
                field: 'image_url', width: 80, title: '图片', align: 'center', templet: function (d) {
                    var imageStr = "";
                    if (d.image_url) {
                        imageStr = '<a href="' + d.image_url + '" target="_blank"><img src="' + d.image_url + '" height="26" /></a>';
                    }
                    return imageStr;
                }
            }
            , {field: 'name', width: 150, title: '友链名称', align: 'center', event: 'setSign', style: 'cursor: pointer;'}
            , {field: 'url', width: 250, title: 'URL地址', align: 'center'}
            , {field: 'type_name', width: 100, title: '友链类型', align: 'center'}
            , {field: 'platform_name', width: 100, title: '使用平台', align: 'center'}
            , {field: 'form_name', width: 100, title: '友链形式', align: 'center'}
            , {field: 'status', width: 80, title: '状态', align: 'center', templet: function (d) {
                    var str = "";
                    if (d.status == 1) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs">正常</span>';
                    } else if (d.status == 2) {
                        str = '<span class="layui-btn layui-btn-normal layui-btn-xs layui-btn-danger">停用</span>';
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
        func.setWin("友链");

    } else {
        //【监听友链类型】
        var link_form = $('#form').val();
        if (link_form == 1) {
            //文字
            $(".image").addClass("layui-hide");
        } else if (link_form == 2) {
            //图片
            $(".image").removeClass("layui-hide");
        }
        form.on('select(form)', function (data) {
            if (data.value == 1) {
                $(".image").addClass("layui-hide");
            } else if (data.value == 2) {
                $(".image").removeClass("layui-hide");
            }
        });
    }
});
