layui.use(['form', 'layer', 'jquery'], function () {
    var layer = layui.layer;
    $ = layui.jquery;
    form = layui.form;
    $(".menu-add").click(function () { //弹出添加列表
        $("menu-from").show();
        layer.open({
            type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
            title: '菜单添加',
            closeBtn: 1, // 关闭按钮 closeBtn: 0
            area: '556px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
            shadeClose: false, //是否点击遮罩关闭 默认：false
            content: $('.menu-from')
        });
    });
    form.on('submit(formMenu)', function (data) { //监听提交
        $.post("/admin/menu/save", data.field, function (result) {
            if (result.code === 200) {
                layer.msg("添加成功", {
                    icon: 1, time: 1000 //1秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else if (result.code === 0) {
                layer.msg("暂无权限", {icon: 2, time: 1000});
            } else {
                layer.msg("添加失败", {icon: 2, time: 1000});
            }
        });
        return false;
    });
    $(".del").click(function () { //删除标签a标签没有value！！！
        var id = $(this).attr("id");
        if (id) {
            layer.confirm('确认删除？', {
                btn: ['删除', '取消'] //按钮
            }, function () {
                $.get("/admin/menu/del?id=" + id, function (result) {
                    if (result.code === 200) {
                        layer.msg("删除成功", {icon: 1, time: 1000}, function () {
                            window.location.reload();
                        });
                    } else if (result.code === 0) {
                        layer.msg("暂无权限", {icon: 2, time: 1000});
                    } else {
                        layer.msg("删除失败", {icon: 2, time: 1000});
                    }
                });
            });
        } else {
            layer.msg("操作错误", {icon: 2, time: 1000});
        }
    });
    $(".edit").click(function () { //删除标签a标签没有value！！！
        var id = $(this).attr("id");
        if (id) {
            $.get("/admin/menu/get_find?id=" + id, function (result) {
                var data = result.data;
                // layer.msg(JSON.stringify(data));
                $("menu-edit").show();
                $("#id-edit").val(data.id);
                $("#name-edit").val(data.name);
                $("#href-edit").val(data.href);
                $("#parent-edit").val(data.parent_id);
                form.render("select"); //渲染什么的必须要渲染
                $("#icon-edit").val(data.icon);
                $("#sort-edit").val(data.sort);
                layer.open({
                    type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                    title: '菜单修改',
                    closeBtn: 1, // 关闭按钮 closeBtn: 0
                    area: '556px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
                    shadeClose: false, //是否点击遮罩关闭 默认：false
                    content: $('.menu-edit')
                });

            });
        } else if (result.code === 0) {
            layer.msg("暂无权限", {icon: 2, time: 1000});
        } else {
            layer.msg("操作错误", {icon: 2, time: 1000});
        }
    });
    form.on('submit(formMenuEdit)', function (data) { //监听提交
        $.post("/admin/menu/save", data.field, function (result) {
            if (result.code === 200) {
                layer.msg("修改成功", {
                    icon: 1, time: 1000 //1秒关闭（如果不配置，默认是3秒）
                }, function () {
                    window.location.reload();
                });
            } else if (result.code === 0) {
                layer.msg("暂无权限", {icon: 2, time: 1000});
            } else {
                layer.msg("修改失败", {icon: 2, time: 1000});
            }
        });
        return false;
    });
});