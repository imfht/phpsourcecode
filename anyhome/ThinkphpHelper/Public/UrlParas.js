// 当前页面url参数操作插件
// Author:Bean
// Date:2014/09/17
;
(function(window, document) {
 
    var UrlParas = function(url) {
        return UrlParas.fn.init(url);
    }
    UrlParas.VERSION = '1.0.0';
    UrlParas.fn = UrlParas.prototype = {
 
        url: "",
        pathname: "",
        paras: "",
        init: function(url) {
            this.url = url;
            this.pathname = url.split("?")[0];
            this.paras = this.get();
            return this;
        },
 
        //以object类型返回url参数及其取值
        get: function(option) {
            var paraStr, paras,
                url = this.url;
            if (url) {
                paraStr = url.split("?")[1];
                if (paraStr) {
                    paras = {};
                    paraStr = paraStr.split("&");
                    for (var n in paraStr) {
                        var name = paraStr[n].split("=")[0];
                        var value = paraStr[n].split("=")[1];
                        paras[name] = value;
                    }
                } else {
                    return {};
                }
                if (!option) {
                    return paras;
                } else {
                    return paras[option] ? paras[option] : "";
                }
 
 
            }
        },
 
        //重设url参数取值，若无此参数则进行创建,若参数赋值为null则进行删除
        set: function(option) {
            var i, name, val;
            if (arguments.length == 2) {
                name = arguments[0];
                val = arguments[1];
                option = {
                    name: val
                };
            }
            if ("string" === typeof option) {
                this.paras[option] = "";
            } else if ("object" === typeof option) {
                for (i in option) {
                    if (option[i] === null) {
                        delete this.paras[i];
                    } else {
                        this.paras[i] = option[i];
                    }
                }
            } else {
 
            }
            return this.build();
        },
 
        //删除url中指定参数返回新url
        remove: function(option) {
            var i;
            if ("string" === typeof option) {
                option = option.split(",");
                for (i in option) {
                    delete this.paras[option[i]]
                }
 
            }
            return this.build();
        },
 
        //根据url和处理过的paras重新构件url
        build: function() {
            var i,
                newUrl = this.pathname + "?";
 
            for (i in this.paras) {
                newUrl += (i + "=" + this.paras[i] + "&");
            }
 
            return newUrl.substr(0, newUrl.length - 1);
        }
 
 
    }
 
    UrlParas.fn.init.prototype = UrlParas.fn;
 
    window.urlParas = UrlParas;
 
})(window, document);