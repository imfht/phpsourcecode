// layui.use(['form','layer','jquery'],function(){
;!function () {
    var layer = layui.layer;
    form = layui.form;
    $ = jQuery = layui.jquery;

    $(".role-add").click(function () { //弹出添加列表
        $("role-from").show();
        layer.open({
            type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
            title: '角色添加',
            closeBtn: 1, // 关闭按钮 closeBtn: 0
            area: '516px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
            shadeClose: false, //是否点击遮罩关闭 默认：false
            content: $('.role-from')
        });
        // form.render('select');  //这回非模块化必须要渲染了，下同
        // form.render('checkbox');
        form.render();  //全部渲染，比较费性能，管不了许多了
    });
    form.on('submit(formRole)', function (data) { //监听提交
        $.post("/admin/role/save", data.field, function (result) {
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
    $(".edit").click(function () { //删除标签（a标签没有value！！！）
        var id = $(this).attr("id");
        if (id) {
            $.get("/admin/role/get_find?id=" + id, function (result) {
                if (result.code === 0) {
                    layer.msg("暂无权限", {icon: 2, time: 1000});
                    return false;
                }
                var data = result.data;
                $("role-edit").show();
                $("#id-edit").val(data.id);
                $("#name-edit").val(data.name);
                $("#ename-edit").val(data.ename);
                $("#usable-edit").val(data.usable);
                $("#sel-edit").val(data.role_type);
                form.render("select"); //渲染什么的必须要渲染
                $("#display-edit").val(data.display_name);
                $("#description-edit").val(data.description);
                layer.open({
                    type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                    title: '菜单修改',
                    closeBtn: 1, // 关闭按钮 closeBtn: 0
                    area: '516px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
                    shadeClose: false, //是否点击遮罩关闭 默认：false
                    content: $('.role-edit')
                });
                form.render();
            });
        } else {
            layer.msg("操作错误", {icon: 2, time: 1000});
        }

    });
    form.on('submit(formRoleEdit)', function (data) { //监听提交
        $.post("/admin/role/save", data.field, function (result) {
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
    $(".del").click(function () { //删除标签a标签没有value！！！
        var id = $(this).attr("id");
        if (id) {
            layer.confirm('确认删除？', {
                btn: ['删除', '取消'] //按钮
            }, function () {
                $.get("/admin/role/del?id=" + id, function (result) {
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
    $(".power").click(function () {
        var id = $(this).attr("id");
        $("#id-power").val(id);  //把id赋值到页面 form请求调用
        $.get("/admin/role/get_power?role_id=" + id, function (result) {
            if (result.code === 0) {
                layer.msg("暂无权限", {icon: 2, time: 1000});
                return false;
            }
            // zNodes = result.data;
            zNodes = JSON.parse(result.data);  //将字符串转换成obj
            $("role-power").show();
            layer.open({
                type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                title: '权限分配',
                closeBtn: 1, // 关闭按钮 closeBtn: 0
                // area: '516px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
                area: ['350px', '80%'],
                shadeClose: false, //是否点击遮罩关闭 默认：false
                content: $('.role-power')
            });
            var setting = {  //设置zetree
                check: {enable: true},
                data: {simpleData: {enable: true}}
            };
            $.fn.zTree.init($("#treeType"), setting, zNodes);
            var zTree = $.fn.zTree.getZTreeObj("treeType");
            zTree.expandAll(true);
        });
        // layer.msg("暂未开放", {icon: 2, time: 1000});
    });
    //确认分配权限
    // $("#postpower").click(function(){
    form.on('submit(formPower)', function () {
        // layer.msg("暂未开放", {icon: 2, time: 1000});
        var zTree = $.fn.zTree.getZTreeObj("treeType");
        var nodes = zTree.getCheckedNodes(true);
        var NodeString = '';
        $.each(nodes, function (n, value) {
            if (n > 0) {
                NodeString += ',';
            }
            NodeString += value.id;
        });
        var id = $("#id-power").val();
        //写入库
        $.post('/admin/role/post_power', {'type': 'power', 'id': id, 'rule': NodeString}, function (result) {
            if (result.code === 200) {
                layer.msg("分配成功", {icon: 1, time: 1000}, function () {
                    window.location.reload();
                });
            } else if (result.code === 0) {
                layer.msg("暂无权限", {icon: 2, time: 1000});
            } else {
                layer.msg("分配失败", {icon: 2, time: 1000});
            }
        });
        return false;  //必须加返回值为false 否则会有重定向
    });
}();
// });
