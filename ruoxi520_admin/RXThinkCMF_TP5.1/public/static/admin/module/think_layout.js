/**
 * 布局管理
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
            , {field: 'title', width: 200, title: '布局标题', align: 'center'}
            , {field: 'loc_name', width: 250, title: '推荐位置编号', align: 'center'}
            , {field: 'image_url', width: 60, title: '封面', align: 'center', templet: function (d) {
                    return '<a href="' + d.image_url + '" target="_blank"><img src="' + d.image_url + '" height="26" /></a>';
                }
            }
            , {field: 'type_name', width: 100, title: '推荐类型', align: 'center'}
            , {field: 'content', width: 350, title: '推荐内容', align: 'center'}
            , {field: 'item_name', width: 100, title: '所属站点', align: 'center'}
            , {field: 'sort', width: 100, title: '排序', align: 'center'}
            , {field: 'create_time', width: 180, title: '创建时间', align: 'center', sort: true}
            , {field: 'update_time', width: 180, title: '更新时间', align: 'center', sort: true}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("布局");

    } else {
        //监听推荐类型
        var type = $("#type").val();
        var typeStr = '';
        form.on('select(type)', function (data) {
            type = data.value;
            typeStr = data.elem[data.elem.selectedIndex].text;
        });

        //选择类型对象
        $("#type_desc").click(function () {
            //推荐类型
            var title, url;
            if (type == 1) {
                //CMS文章
                title = "请选择推荐模块";
                url = mUrl + "/article/index/?simple=1";
            } else {
                //其他

            }

            if (!url) {
                layer.msg("请选择类型");
                return false;
            }

            //【弹开窗体】
            func.showWin("选择内容", url, 1000, 600);
        });
    }
});