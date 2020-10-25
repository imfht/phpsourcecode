// +----------------------------------------------------------------------+
// | ThinkPHP                                                             |
// +----------------------------------------------------------------------+
// | Copyright (c) 2006 liu21st.com All rights reserved.                  |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the 'License');      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an 'AS IS' BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: liu21st <liu21st@gmail.com>                                  |
// +----------------------------------------------------------------------+
// $Id$

// Ajax for ThinkPHP
var m = {
    '\b': '\\b',
    '\t': '\\t',
    '\n': '\\n',
    '\f': '\\f',
    '\r': '\\r'
};
var ThinkAjax = {
    method: 'POST',			// 默认发送方法
    bComplete: false,			// 是否完成
    status: 0, //返回状态码
    info: '',	//返回信息
    data: '',	//返回数据
    type: '', // JSON EVAL XML ...
    intval: 0,
    options: {},
    debug: false,
    activeRequestCount: 0,
    // Ajax连接初始化
    getTransport: function () {//lee99
        http_request = false;
        if (window.XMLHttpRequest) {//Mozilla浏览器
            http_request = new XMLHttpRequest();
            if (http_request.overrideMimeType) {//设置MIME类别
                http_request.overrideMimeType("text/xml");
            }
        } else if (window.ActiveXObject) {//IE浏览器
            var versions = ['Microsoft.XMLHTTP', 'MSXML.XMLHTTP', 'Microsoft.XMLHTTP', 'Msxml2.XMLHTTP.7.0', 'Msxml2.XMLHTTP.6.0', 'Msxml2.XMLHTTP.5.0', 'Msxml2.XMLHTTP.4.0', 'MSXML2.XMLHTTP.3.0', 'MSXML2.XMLHTTP'];
            for (var i = 0; i < versions.length; i++) {
                try {
                    http_request = new ActiveXObject(versions[i]);//alert (versions[i]);
                } catch (e) {
                    //alert(e.message);
                }
            }
        }
        if (!http_request) {//异常，创建对象实例失败
            window.alert("创建XMLHttp对象失败！");
            return false;
        }
        return http_request;
    },
    response: function (response) {
        this.options['response'] = response;
        return this;
    },
    url: function (url) {
        this.options['url'] = url;
        return this;
    },
    params: function (vars) {
        this.options['var'] = vars;
        return this;
    },
    ajaxResponse: function (request, response) {
        // 获取ThinkPHP后台返回Ajax信息和数据
        // 此格式为ThinkPHP专用格式
        //alert(request.responseText);
        var str = request.responseText;
        str = str.replace(/([\x00-\x1f\\"])/g, function (a, b) {
            var c = m[b];
            if (c) {
                return c;
            } else {
                return b;
            }
        });
        try {
            $return = eval('(' + str + ')');
            if (this.debug) {
                alert(str);
            }
        } catch (ex) {
            return;
        }
        this.status = $return.status;
        this.info = $return.info;
        this.data = $return.data;
        this.type = $return.type;

        if (this.type == 'EVAL') {
            // 直接执行返回的脚本
            eval($this.data);
        } else {
            // 处理返回数据
            // 需要在客户端定义ajaxReturn方法
            if (response == undefined) {
                try {
                    (ajaxReturn).apply(this, [this.data, this.status, this.info, this.type]);
                }
                catch (e) {
                }

            } else {
                try {
                    (response).apply(this, [this.data, this.status, this.info, this.type]);
                }
                catch (e) {
                }
            }
        }

    },
    // 发送Ajax请求
    send: function (url, pars, response) {
        var xmlhttp = this.getTransport();
        url = (url == undefined) ? this.options['url'] : url;
        pars = (pars == undefined) ? this.options['var'] : pars;
        if (this.intval) {
            window.clearTimeout(this.intval);
        }
        this.activeRequestCount++;
        this.bComplete = false;
        try {
            if (this.method == "GET") {
                xmlhttp.open(this.method, url + "?" + pars, true);
                pars = "";
            }
            else {
                xmlhttp.open(this.method, url, true);
                xmlhttp.setRequestHeader("Method", "POST " + url + " HTTP/1.1");
                xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            }
            var _self = this;
            xmlhttp.onreadystatechange = function () {
                if (xmlhttp.readyState == 4) {
                    if (xmlhttp.status == 200 && !_self.bComplete) {
                        _self.bComplete = true;
                        _self.activeRequestCount--;
                        _self.ajaxResponse(xmlhttp, response);
                    }
                }
            }
            xmlhttp.send(pars);
        }
        catch (z) {
            return false;
        }
    },
    // 绑定Ajax到HTML元素和事件
    // event 支持根据浏览器的不同
    // 包括 focus blur mouseover mouseout mousedown mouseup submit click dblclick load change keypress keydown keyup
    bind: function (source, event, url, vars, response) {
        var _self = this;
        $(source).addEvent(event, function () {
            _self.send(url, vars, response)
        });
    },
    // 页面加载完成后执行Ajax操作
    load: function (url, vars, response) {
        var _self = this;
        window.addEvent('load', function () {
            _self.send(url, vars, response)
        });
    },
    // 延时执行Ajax操作
    time: function (url, vars, time, response) {
        var _self = this;
        myTimer = window.setTimeout(function () {
            _self.send(url, vars, response)
        }, time);
    },
    // 定制执行Ajax操作
    repeat: function (url, vars, intervals, response) {
        var _self = this;
        _self.send(url, vars, response);
        myTimer = window.setInterval(function () {
            _self.send(url, vars, response)
        }, intervals);
    }
}
