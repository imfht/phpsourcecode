/**短信验证码*/
function wechat_getsms(phone, url, css) {
    var is_phone = /^1[345678]\d{9}$/;
    if (!(is_phone.test(phone))) {
        parent.layer.alert('手机号输入错误',{icon:2});
    } else {
        var index = parent.layer.load(0,{shade: false});
        $.post(url,{phone: phone}, function (data) {
            parent.layer.close(index)
            if (data.code == 200) {
                parent.layer.alert(data.message,{icon:1});
                settime(css);
            } else {
                parent.layer.alert(data.message,{icon:6});
            }
        })
    }
}
//设置验证码按钮状态
var countdown = 60;
function settime(obj) {
    if (countdown == 0) {
        obj.removeAttribute("disabled");
        $(obj).html("获取验证码");
        countdown = 60;
        return;
    } else {
        obj.setAttribute("disabled", true);
        $(obj).html("重新发送(" + countdown + ")");
        countdown--;
    }
    setTimeout(function () {settime(obj);}, 1000)
}
