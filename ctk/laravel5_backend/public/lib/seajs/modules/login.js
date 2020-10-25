/** login */
define(function(require, exports, module) {

    var cache_publickey_str = 'publickey';
    var loading_image = '<img width="18" src="images/loading-icons/loading7.gif">';
    var login_form_obj = $('#login-form');
    var msg_obj = $('#msg');
    var submit_obj = $('#submit');
    var loading_obj = $('#loading');

    var $_GET = (function(){
        var url = window.document.location.href.toString();
        var u = url.split("?");
        if(typeof(u[1]) == "string"){
            u = u[1].split("&");
            var get = {};
            for(var i in u){
                var j = u[i].split("=");
                get[j[0]] = j[1];
            }
            return get;
        } else {
            return {};
        }
    })();

    //登录
    function login(username, password) {
        $.ajax({
            type: "get",
            url: "/login/proc",
            data: {username:username,password:password},
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback:"callback",
            headers: {
                'X-XSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(data) {
                if(!data.result) {
                    prelogin(true);
                }
                msg_obj.html(data.msg).show();
                if(data.result && typeof data.jumpUrl != 'undefined') {
                    window.location.href = data.jumpUrl;
                }else if(data.result) {
                    window.location.href = '/';
                }
            },
            beforeSend: function() {
                submit_obj.html(loading_image);
            },
            timeout: 30000,
            complete: function(request, status) {
                if(status == 'timeout') {
                    msg_obj.html('登录超时，请重试！');
                }
                submit_obj.html('登陆');
            }
        });
    }

    //登录前处理
    function prelogin(is_show_loading) {
        $.ajax({
            type: "get",
            url: "/login/prelogin",
            data: "",
            dataType: "jsonp",
            jsonp: "callback",
            jsonpCallback:"callback",
            success: function(data) {
                $('input').attr('disabled', false);
                //确保正确返回了数据才会显示登录框
                if( ! is_show_loading) {
                    msg_obj.hide();
                    loginForm(true);
                }
                submit_obj.data(cache_publickey_str, data.pKey);
                $('meta[name="csrf-token"]').attr('content', data.a);
            },
            beforeSend: function() {
                msg_obj.html('处理中...').show();
                $('input').attr('disabled', true);
                if( ! is_show_loading) {
                    loginForm(false);
                }
            },
            timeout: 10000,
            complete: function(request, status) {
                if(status == 'timeout') {
                    //todo
                }
            }
        });
    }

    //登录框的显示与隐藏
    function loginForm(show_or_hide) {
        if(!show_or_hide) {
            login_form_obj.hide();
            loading_obj.show()
        } else {
            login_form_obj.show();
            loading_obj.hide();
        }
    }

    //侦听登录的点击事件
    function submit() {
        submit_obj.on('click', function() {
            var username = $('#username').val();
            var password = $('#password').val();
            if(username == '') {
                msg_obj.html('请输入用户名').show();
                return false;
            }
            if(password == '') {
                msg_obj.html('请输入密码').show();
                return false;
            }
            password = CryptoJS.MD5(password);
            var publickey = submit_obj.data(cache_publickey_str);
            if(typeof publickey == 'undefined') {
                msg_obj.html('登录失败，非法操作。');
                return false;
            }
            password = password + publickey;
            password = (""+CryptoJS.MD5(password)).toUpperCase();
            login(username, password);
        });
    }

    return {
        login:login,
        prelogin:prelogin,
        submit:submit
    }

});