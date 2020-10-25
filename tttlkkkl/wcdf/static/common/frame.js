/**
 * Created by m on 17-4-5.
 * 公共封装
 */

var http = {
    apiUrl: 'http://api.lihuasheng.cn',
    motheds: 'POST.GET.PUT.DELETE',
    get: function (url, data, fun) {
        return this.go('GET', url, data, fun);
    },
    post: function (url, data, fun) {
        return this.go('POST', url, data, fun);
    },
    put: function (url, data, fun) {
        return this.go('PUT', url, data, fun);
    },
    delete: function (url, data, fun) {
        return this.go('DELETE', url, data, fun);
    },
    go: function (mothed, url, data, fun) {
        if (url == '') {
            return false;
        }
        console.log(this.motheds.indexOf(mothed))
        if (this.motheds.indexOf(mothed) < 0) {
            return false;
        }
        url = url.substr(0, 7).toLowerCase() == "http://" ? url : this.apiUrl + url;
        fun = (typeof fun != "undefined" && typeof fun == 'function') ? fun : function () {
        };
        $.ajax(
            {
                url: url,
                type: mothed,
                data: data,
                dataType: 'json',
                xhrFields: {
                    withCredentials: true
                },
                crossDomain: true,
                success: fun
            }
        );
        return true;
    },
    //获取url参数
    getQueryStringParam: function (name) {
        if (name === '' || name === undefined) {
            return null;
        }
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
        var r = window.location.search.substr(1).match(reg);
        if (r != null)
            return unescape(r[2]);
        return null;
    }
}
