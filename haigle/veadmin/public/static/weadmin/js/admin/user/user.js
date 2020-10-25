layui.use(['form', 'layer', 'jquery', 'laydate'], function () {
    var layer = layui.layer;
    $ = layui.jquery;
    form = layui.form;
    laydate = layui.laydate;

    form.verify({
        //我们既支持上述函数式的方式，也支持下述数组的形式
        //数组的两个值分别代表：[正则匹配、匹配不符时的提示文字]
        // ,pass: [
        //     /^[\S]{6,12}$/
        //     ,'密码必须6到12位，且不能出现空格'
        // ]
        pass: function (value) {
            if (value === "") {
                return "请输入二次密码！";
            }
            var pwd = $('input[name=password]').val();
            if (pwd !== value) {
                return "二次输入的密码不一致！";
            }
        },
        pa: function (value) {
            if (value === "") {
                return "请输入二次密码！";
            }
            var pwd = $('input[id=password-edit]').val();
            if (pwd !== value) {
                return "二次输入的密码不一致！";
            }
        }
    });
    $(".user-add").click(function () { //弹出添加列表
        $("user-from").show();
        //执行一个laydate实例
        laydate.render({
            elem: '#birthday',
            type: 'datetime'
        });
        layer.open({
            type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
            title: '用户添加',
            closeBtn: 1, // 关闭按钮 closeBtn: 0
            area: '516px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
            shadeClose: false, //是否点击遮罩关闭 默认：false
            content: $('.user-from')
        });
    });
    form.on('submit(formUser)', function (data) { //监听提交
        $.post("/admin/user/save", data.field, function (result) {
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
                $.get("/admin/user/del?id=" + id, function (result) {
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
    $(".edit").click(function () {
        var id = $(this).attr("id");
        if (id) {
            $.get("/admin/user/get_find?id=" + id, function (result) {
                if (result.code === 0) {
                    layer.msg("暂无权限", {icon: 2, time: 1000});
                    return false;
                }
                var data = result.data;
                // layer.msg(JSON.stringify(data));
                $("user-edit").show();
                $("#id-edit").val(data.id);
                $("#name-edit").val(data.name);
                $("#email-edit").val(data.email);
                $("#phone-edit").val(data.phone);
                $("#password-edit").val(data.password);
                $("#passwords-edit").val(data.password);
                var html = '';
                for (var i = 0; i < data.role.length; i++) {
                    if (data.role[i].checked === 'checked') {
                        html += '<input type="checkbox" name="role[]" value="' + data.role[i].id + '" title="' + data.role[i].name + '" checked >'
                    } else {
                        html += '<input type="checkbox" name="role[]" value="' + data.role[i].id + '" title="' + data.role[i].name + '">'
                    }
                }
                $(".role-edit-input").html(html);
                laydate.render({
                    elem: '#birthday-edit',
                    type: 'datetime',
                    value: data.birth_at
                });
                layer.open({
                    type: 1, // 0（信息框，默认）1（页面层）2（iframe层）3（加载层）4（tips层）
                    title: '菜单修改',
                    closeBtn: 1, // 关闭按钮 closeBtn: 0
                    area: '516px', // 宽高 area: ['500px', '300px'], 当一个值是高度自适应
                    shadeClose: false, //是否点击遮罩关闭 默认：false
                    content: $('.user-edit')
                });
                form.render();
            });
        } else {
            layer.msg("操作错误", {icon: 2, time: 1000});
        }
    });
    form.on('submit(formUserEdit)', function (data) { //监听提交
        // console.log(data.field.role);
        // if(data.role.valueOf() === obj){
        //     layer.msg("请选择角色", {icon: 2, time: 1000 });
        //     return false;
        // }
        $.post("/admin/user/save", data.field, function (result) {
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