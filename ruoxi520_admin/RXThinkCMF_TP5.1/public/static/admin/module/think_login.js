/**
 * 系统登录
 */
layui.define(['form'], function (exports) {

    // 声明变量
    var form = layui.form,
        $ = layui.$;

    // 登录事件
    form.on('submit(submit)', function (data) {

        // 初始化Ladda对象
        var l = Ladda.create(this);
        // 开始
        l.start();

        // // 设置按钮文字“登录中...”及禁止点击状态
        // $(data.elem).attr('disabled', true).text('登录');

        // 网络请求
        $.post("/login/login", data.field, function (result) {
            if (result.success) {
                layer.msg('登录成功', {
                    icon: 1,
                    time: 1000
                });

                // 延迟3秒
                setTimeout(function () {

                    // 结束
                    l.stop();

                    // 跳转后台首页
                    window.location.href = "/index/index";

                }, 2000);

                return false;
            } else {
                // tips提示
                layer.tips(result.msg, $("#" + result.data), {
                    tips: [3, '#FF5722']
                });

                // 延迟3秒恢复可登录状态
                setTimeout(function () {

                    // 结束
                    l.stop();
                    //
                    // // 设置按钮状态为“登陆”
                    // var login_text = $(data.elem).text().replace('中', '');
                    // // 设置按钮为可点击状态
                    // $(data.elem).text(login_text).removeAttr('disabled');
                }, 3000);
            }
        }, 'json');

        return false;
    });

    // 模块输出
    exports('login', {});

});