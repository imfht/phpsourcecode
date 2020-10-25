/**
 * Created by chen on 2016/6/24.
 * 后台公共js文件
 */
$(function(){
    $(window).keydown(function(event){
        if (event.keyCode == 13){
            $("#admin-login").click();
        }
    });
    //登录
    $("#admin-login").on('click',function(){
        var username = $("#username").val();
        var password = $("#password").val();
        var error = $("#error");
        var i = 0;
        error.hide();
        error.html('');
        if (username == '') {
            error.fadeIn("slow");
            error.html('用户名不能为空.^_^');
            var t = setTimeout(function(){
                error.fadeOut("slow");
                clearTimeout(t);
            },3000);
            return false;
        } else if(password == '') {
            error.fadeIn("slow");
            error.html('密码不能为空.^_^');
            var t2 = setTimeout(function(){
                error.fadeOut("slow");
                clearTimeout(t2);
            },3000);
            return false;
        }

        if (username && password) {
            datas = {
                'username' : username,
                'password' : password
            };
            var loadColse = layer.load(2, {time: 5*1000});
            $.post(sendLoginUrl ,datas,function(data){
                if (data.status) {
                    layer.close(loadColse);
                    window.location.href = data.url;

                } else {
                    layer.close(loadColse);
                    error.fadeIn("slow");
                    error.html(data.msg);
                    var t3 = setTimeout(function(){
                        error.fadeOut("slow");
                        clearTimeout(t3);
                    },3000);
                }
            },"json");
        }
    });
});