// cookie 操作
function cookie_set(name, value, expire) {
    if(typeof expire == 'number') {
        var _time = new Date();
        _time.setTime(_time.getTime()+expire*1000);
        _time = _time.toGMTString();
    } else {
        expire = null;
    }
    document.cookie = name+'='+value+';'
    +((expire === null) ? '' : 'expires='+_time+';')
    +'path=/';
}

function cookie_get(name) {
    var str = document.cookie;
    if(str.length < 1) return null;
    var begin = str.indexOf(name+'=');
    if(begin == -1) return null;
    begin = begin+name.length+1;
    var end = str.indexOf(';', begin);
    if(end == -1) end = str.length;
    return str.substring(begin, end);
}

// 获取post请求token
function get_token() {
    var _token = String(Math.random());
    cookie_set("_token", _token, null);
    return _token;
}

// ajax 上传
function ajax_upload(id, url, callback) {
    var el = $("#"+id);

    if(!el.data("is_init")) {
        var name = "ajax-upload-"+new Date().getTime();
        var iframe = $("<iframe name=\""+name+"\" style=\"display: none;\" />");
        var form = $("<form method=\"POST\" enctype=\"multipart/form-data\" target=\""+name+"\" />");
        el.wrap(form);
        var form = el.parent();
        form.after(iframe);
        el.data("form", form);
        el.data("iframe", iframe);
        el.data("is_init", 1);
    }

    var iframe = el.data("iframe");
    var form = el.data("form");
    form.attr("action", url);
    form.append($("<input name=\"_token\" type=\"hidden\" />").val(get_token()));
    iframe.unbind();
    iframe.load(function() {
        var r = this.contentWindow.document.body.innerHTML;
        try {
            r = $.parseJSON(r);
            if(r && typeof(callback) == "function") {
                callback(r);
            }
        } catch(e) {

        }
    });
    form.submit();
    $("input[name='_token']", form).remove();
}

