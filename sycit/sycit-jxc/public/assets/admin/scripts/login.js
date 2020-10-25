/**
 * 三叶草IT QQ-316262448
 * www.sycit.cn, hyzwd@outlook.com
 * Created by Peter on 2017/8/20.
 */
// 定义 login
var Login = function () {
    // 判断登陆
    var handleLogin = function () {
        //
        $("#login-form").validate({
            errorElement: 'em', //默认输入错误消息容器
            //errorClass: 'help-block', // 默认输入错误消息类
            errorContainer: "#success", // 指定显示的容器
            errorLabelContainer: $("#login-form #success"), // 如果表单验证不通过，所有错误消息提示的label元素都会插入到该元素中
            //debug: true, // 调试时用，只验证不提交表单
            wrapper: "li", // 错误的标签
            focusInvalid: false, // 不要集中最后一个无效输入
            rules: {
                username: {
                    required: true
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 16

                },
                remember: {
                    required: false
                }
            },
            messages: {
                username: {
                    required: "用户名是必需的"
                },
                password: {
                    required: "密码是必需的",
                    minlength: $.validator.format("密码不能小于{0}个字符")
                },
                verify: {
                    required: "验证码是必需的",
                    minlength: $.validator.format("验证码不能小于{0}个字符")
                }
            },

            // 如果表单验证不通过
            invalidHandler: function(){
                //
            },
            // 表单验证成功，调用Ajax表单提交
            submitHandler: function() {
                //
                var submitAjax = {
                    url: '/login/login.html',
                    type: 'post',
                    dataType: 'JSON',
                    data: $("#login-form").serialize(),
                    success: function (result) {
                        if (result.code > 0) {
                            window.location.href=result.url;
                        } else {
                            alert(result.msg);
                            window.location.href="/index";
                        }
                    }
                };
                // 调用app.js的 blockUI事件
                App.blockUI({
                    boxed: true
                });
                window.setTimeout(function() {
                    App.unblockUI(); // 消除blockUI事件
                    $.ajax(submitAjax);
                }, 1000);
                return false; // 阻止表单自动提交事件
            }
        });
    }

    // 自动运行
    return {
        // 运行 init
        init: function () {
            handleLogin();
        }
    };
}();

jQuery(document).ready(function () {
   Login.init();
});