const $A = {
    /**
     * 获取缓存
     * @param key
     * @returns {string}
     */
    getStorage(key) {
        return localStorage.getItem(key);
    },

    /**
     * 设置缓存
     * @param key
     * @param value
     */
    setStorage(key, value) {
        return localStorage.setItem(key, value);
    },

    /**
     * 删除缓存
     * @param keys
     */
    removeStorage(keys) {
        return localStorage.removeItem(keys);
    },

    /**
     * 清空缓存
     */
    clearStorage() {
        return localStorage.clear();
    },

    /**
     * 显示通知
     * @param title
     * @param options
     * @param link
     */
    showNotify(title, options, link) {
        var notification = new Notification(title, Object.assign({
            dir: "rtl",
            lang: "zh-CN",
            icon: "images/icon-message.png",
        }, options));
        notification.onclick = function () {
            if (link) {
                window.open(link);
            }
        };
    },

    /**
     * 地址获取域名
     * @param url
     * @returns {string}
     */
    getHost(url) {
        if (/^chrome:\/\//.test(url)) {
            return "";
        }
        try {
            var info = new URL(url);
            return info.host || info.hostname;
        } catch (err) {
            console.log(err);
            return "";
        }
    },

    /**
     * 地址获取目录
     * @param url
     * @returns {string}
     */
    getPathname(url) {
        if (/^chrome:\/\//.test(url)) {
            return "";
        }
        try {
            var info = new URL(url);
            return info.pathname;
        } catch (err) {
            console.log(err);
            return "";
        }
    },

    /**
     * 删除地址中的参数
     * @param url
     * @param parameter
     * @returns {string|*}
     */
    removeURLParameter(url, parameter) {
        if (parameter instanceof Array) {
            parameter.forEach((key) => {
                url = $A.removeURLParameter(url, key)
            });
            return url;
        }
        var urlparts = url.split('?');
        if (urlparts.length >= 2) {
            //参数名前缀
            var prefix = encodeURIComponent(parameter) + '=';
            var pars = urlparts[1].split(/[&;]/g);

            //循环查找匹配参数
            for (var i = pars.length; i-- > 0;) {
                if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                    //存在则删除
                    pars.splice(i, 1);
                }
            }

            return urlparts[0] + (pars.length > 0 ? '?' + pars.join('&') : '');
        }
        return url;
    },

    /**
     * 连接加上参数
     * @param url
     * @param params
     * @returns {*}
     */
    urlAddParams(url, params) {
        if (typeof params === "object" && params !== null) {
            url+= "";
            url+= url.indexOf("?") === -1 ? '?' : '';
            for (var key in params) {
                if (!params.hasOwnProperty(key)) {
                    continue;
                }
                url+= '&' + key + '=' + params[key];
            }
        }
        return url.replace("?&", "?");
    },

    /**
     * 将一个 JSON 字符串转换为对象（已try）
     * @param str
     * @param defaultVal
     * @returns {*}
     */
    jsonParse(str, defaultVal) {
        if (str === null) {
            return defaultVal ? defaultVal : {};
        }
        if (typeof str === "object") {
            return str;
        }
        try {
            return JSON.parse(str);
        } catch (e) {
            return defaultVal ? defaultVal : {};
        }
    },

    /**
     * 将 JavaScript 值转换为 JSON 字符串（已try）
     * @param json
     * @param defaultVal
     * @returns {string}
     */
    jsonStringify(json, defaultVal) {
        if (typeof json !== 'object') {
            return json;
        }
        try {
            return JSON.stringify(json);
        } catch (e) {
            return defaultVal ? defaultVal : "";
        }
    },

    /**
     * 转数字
     * @param str
     * @param fixed
     * @returns {number}
     */
    runNum(str, fixed) {
        var _s = Number(str);
        if (_s + "" === "NaN") {
            _s = 0;
        }
        if (/^[0-9]*[1-9][0-9]*$/.test(fixed)) {
            _s = _s.toFixed(fixed);
            var rs = _s.indexOf('.');
            if (rs < 0) {
                _s += ".";
                for (var i = 0; i < fixed; i++) {
                    _s += "0";
                }
            }
        }
        return _s;
    },

    /**
     * 消息内容取描述
     * @param content
     * @returns {*}
     */
    getMsgDesc(content) {
        var desc;
        switch (content.type) {
            case 'text':
                desc = content.text;
                break;
            case 'image':
                desc = '[图片]';
                break;
            case 'file':
                desc = '[文件]';
                break;
            case 'taskB':
                desc = content.text + " [任务消息]";
                break;
            case 'report':
                desc = content.text + " [工作报告]";
                break;
            default:
                desc = '[未知类型]';
                break;
        }
        return desc;
    },

    /**
     * 更新参数
     * @param key
     * @param updateConfig
     */
    updateConfigLists(key, updateConfig) {
        var configLists = $A.jsonParse($A.getStorage("configLists"), {});
        var keyConfig = configLists[key];
        if (keyConfig !== null
            && typeof keyConfig == "object"
            && updateConfig !== null
            && typeof updateConfig == "object") {
            var up = false;
            for (var k in updateConfig) {
                if (!updateConfig.hasOwnProperty(k)) {
                    continue;
                }
                if (updateConfig[k] !== keyConfig[k]) {
                    up = true;
                    break;
                }
            }
            if (up) {
                keyConfig = Object.assign(keyConfig, updateConfig);
                $A.setStorage("configLists", $A.jsonStringify(configLists));
            }
        }
    }
}
