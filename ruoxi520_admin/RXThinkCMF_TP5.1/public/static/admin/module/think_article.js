/**
 * 文章管理
 * @auth 牧羊人
 * @date 2019/5/8
 */
layui.use(['form', 'function'], function () {
    var func = layui.function,
        form = layui.form,
        $ = layui.$;

    if (A == 'index') {
        //【TABLE列数组】
        var cols = [
            {type: 'checkbox', fixed: 'left'}
            , {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
            , {
                field: 'title', width: 400, title: '文章标题', align: 'center', templet: function (d) {
                    return '<a href="' + d.detail_url + '" title="' + d.title + '" class="layui-table-link" target="_blank">' + d.title + '</a>';
                }
            }
            , {
                field: 'cover_url', width: 60, title: '封面', align: 'center', templet: function (d) {
                    var coverStr = "";
                    if (d.cover_url) {
                        coverStr = '<a href="' + d.cover_url + '" target="_blank"><img src="' + d.cover_url + '" height="26" /></a>';
                    }
                    return coverStr;
                }
            }
            , {field: 'cate_name', width: 200, title: '所属分类', align: 'center'}
            , {field: 'is_show', width: 150, title: '是否显示', align: 'center', templet: "#isShowTpl"}
            , {field: 'view_num', width: 100, title: '浏览数', align: 'center', sort: true}
            , {field: 'format_create_user', width: 150, title: '创建人', align: 'center', sort: true}
            , {field: 'format_create_time', width: 180, title: '创建时间', align: 'center',}
            , {fixed: 'right', width: 150, title: '功能操作', align: 'center', toolbar: '#toolBar'}
        ];

        //【TABLE渲染】
        func.tableIns(cols, "tableList");

        //【设置弹框】
        func.setWin("文章");

        //【设置文章显示状态】
        form.on('switch(is_show)', function (obj) {
            var is_show = this.checked ? '1' : '2';

            //发起POST请求
            var url = cUrl + "/setIsShow";
            func.ajaxPost(url, {"id": this.value, "is_show": is_show}, function (data, res) {
                console.log("请求回调");
            });

        });
    }

    //【CMS选择列数组】
    var cols2 = [
        {field: 'id', width: 80, title: 'ID', align: 'center', sort: true, fixed: 'left'}
        , {
            field: 'cover_url', width: 60, title: '封面', align: 'center', templet: function (d) {
                return '<img src="' + d.cover_url + '" height="26" />';
            }
        }
        , {field: 'title', width: 400, title: '文章标题', align: 'center', event: 'setSign', style: 'cursor: pointer;'}
        , {field: 'cate_name', width: 100, title: '文章类型', align: 'center'}
        , {field: 'view_num', width: 100, title: '浏览数', align: 'center', sort: true}
        , {field: 'format_create_user', width: 100, title: '创建人', align: 'center', sort: true}
        , {field: 'format_create_time', width: 180, title: '创建时间', align: 'center',}
    ];

    //【CMS选择】
    func.tableIns(cols2, "tableList2", function (layEvent, data) {

        if (layEvent === 'setSign') {
            //layer.msg("你选中了：【"+data.id+ "】" +data.title, {time:2000});

            //关闭窗口
            //parent.layui.$(".layui-layer-close1").trigger('click'); //选中A页关闭iframe窗口
            var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
            parent.layer.close(index); //再执行关闭

//		    //第一种:调取父页面方法赋值
//		    parent.setAdVal(data.id,data.name);//访问父页面方法

            //第二种：直接赋值
            parent.layui.$("#type_id").val(data.id);
            parent.layui.$("#type_value").val(data.title);
        }
    });

    //【搜索功能】
    func.searchForm("searchForm2", "tableList2");
});

