/**
 * WTWS
 * @param config {username, url, token, channel, logCallback}
 * @constructor
 */
const WTWS = function (config) {
    this.__instance = null;
    this.__connected = false;
    this.__callbackid = {};
    this.__openNum = 0;
    this.__autoNum = 0;

    this.__autoLine = function (timeout) {
        var tempNum = this.__autoNum;
        var thas = this;
        setTimeout(function () {
            if (tempNum === thas.__autoNum) {
                thas.__autoNum++
                if (!thas.__config.token) {
                    thas.__log("[WS] No token");
                    thas.__autoLine(timeout + 5);
                } else {
                    thas.sendTo('refresh', function (res) {
                        thas.__log("[WS] Connection " + (res.status ? 'success' : 'error'));
                        thas.__autoLine(timeout + 5);
                    });
                }
            }
        }, Math.min(timeout, 30) * 1000);
    }
    this.__log = function (text, event) {
        typeof this.__config.logCallback === "function" && this.__config.logCallback(text, event);
    }
    this.__lExists = function (string, find, lower) {
        string += "";
        find += "";
        if (lower !== true) {
            string = string.toLowerCase();
            find = find.toLowerCase();
        }
        return (string.substring(0, find.length) === find);
    }
    this.__rNum = function (str, fixed) {
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
    }
    this.__jParse = function (str, defaultVal) {
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
    }
    this.__randString = function (len) {
        len = len || 32;
        var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678oOLl9gqVvUuI1';
        var maxPos = $chars.length;
        var pwd = '';
        for (var i = 0; i < len; i++) {
            pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
        }
        return pwd;
    }
    this.__urlParams = function(url, params) {
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
    }
    this.__isArr = function (obj){
        return Object.prototype.toString.call(obj)=='[object Array]';
    }

    /**
     * 设置参数
     * @param config
     */
    this.config = function (config) {
        if (typeof config !== "object" || config === null) {
            config = {};
        }
        config.username = config.username || '';
        config.key = config.key || '';
        config.url = config.url || '';
        config.token = config.token || '';
        config.channel = config.channel || '';
        config.logCallback = config.logCallback || null;
        this.__config = config;
        return this;
    }

    /**
     * 连接
     * @param force
     */
    this.connection = function (force) {
        if (!this.__lExists(this.__config.url, "ws://") && !this.__lExists(this.__config.url, "wss://")) {
            this.__log("[WS] No connection address");
            return this;
        }

        if (!this.__config.token) {
            this.__log("[WS] No connected token");
            return this;
        }

        if (this.__instance !== null && force !== true) {
            this.__log("[WS] Connection exists");
            return this;
        }

        var configLists = $A.jsonParse($A.getStorage("configLists"), {});
        var keyConfig = configLists[this.__config.key];
        if (keyConfig !== null && typeof keyConfig == "object") {
            if (keyConfig['disabled'] === true) {
                this.__log("[WS] " + this.__config.key + " is disabled");
                return this;
            }
        }

        var thas = this;

        // 初始化客户端套接字并建立连接
        this.__instance = new WebSocket(this.__urlParams(this.__config.url, {
            token: this.__config.token,
            channel: this.__config.channel
        }));

        // 连接建立时触发
        this.__instance.onopen = function (event) {
            thas.__log("[WS] Connection opened", event);
        }

        // 接收到服务端推送时执行
        this.__instance.onmessage = function (event) {
            var msgDetail = thas.__jParse(event.data);
            if (msgDetail.messageType === 'open') {
                thas.__log("[WS] Connection connected");
                msgDetail.openNum = thas.__openNum;
                msgDetail.config = thas.__config;
                thas.__openNum++;
                thas.__connected = true;
                thas.__autoLine(30);
            } else if (msgDetail.messageType === 'back') {
                typeof thas.__callbackid[msgDetail.messageId] === "function" && thas.__callbackid[msgDetail.messageId](msgDetail.body);
                delete thas.__callbackid[msgDetail.messageId];
                return;
            }
            if (thas.__rNum(msgDetail.contentId) > 0) {
                thas.sendTo('roger', msgDetail.contentId);
            }
            thas.triggerMsgListener(msgDetail);
            $A.updateConfigLists(thas.__config.key, {
                online: true
            });
        };

        // 连接关闭时触发
        this.__instance.onclose = function (event) {
            thas.__log("[WS] Connection closed", event);
            thas.__connected = false;
            thas.__instance = null;
            thas.__autoLine(5);
            $A.updateConfigLists(thas.__config.key, {
                online: false
            });
        }

        // 连接出错
        this.__instance.onerror = function (event) {
            thas.__log("[WS] Connection error", event);
            thas.__connected = false;
            thas.__instance = null;
            thas.__autoLine(5);
            $A.updateConfigLists(thas.__config.key, {
                online: false
            });
        }

        return this;
    }

    /**
     * 添加消息监听
     * @param listenerName
     * @param listenerType
     * @param callback
     */
    this.setOnMsgListener = function (listenerName, listenerType, callback) {
        if (typeof listenerName != "string") {
            return this;
        }
        if (typeof listenerType === "function") {
            callback = listenerType;
            listenerType = [];
        }
        if (!this.__isArr(listenerType)) {
            listenerType = [listenerType];
        }
        if (typeof callback === "function") {
            this.__msgListenerObject[listenerName] = {
                callback: callback,
                listenerType: listenerType,
            }
        }
        return this;
    }
    this.triggerMsgListener = function (msgDetail) {
        var key, item;
        for (key in this.__msgListenerObject) {
            if (!this.__msgListenerObject.hasOwnProperty(key)) {
                continue;
            }
            item = this.__msgListenerObject[key];
            if (item.listenerType.length > 0 &&  item.listenerType.indexOf(msgDetail.messageType) === -1) {
                continue;
            }
            if (typeof item.callback === "function") {
                item.callback(msgDetail);
            }
        }
    }
    this.__msgListenerObject = {}

    /**
     * 添加特殊监听
     * @param listenerName
     * @param callback
     */
    this.setOnSpecialListener = function (listenerName, callback) {
        if (typeof listenerName != "string") {
            return this;
        }
        if (typeof callback === "function") {
            this.__specialListenerObject[listenerName] = {
                callback: callback,
            }
        }
        return this;
    }
    this.triggerSpecialListener = function (simpleMsg) {
        var key, item;
        for (key in this.__specialListenerObject) {
            if (!this.__specialListenerObject.hasOwnProperty(key)) {
                continue;
            }
            item = this.__specialListenerObject[key];
            if (typeof item.callback === "function") {
                item.callback(simpleMsg);
            }
        }
    }
    this.__specialListenerObject = {}

    /**
     * 发送消息
     * @param messageType       会话类型
     * - refresh: 刷新
     * - unread: 未读信息总数量
     * - read: 已读会员信息
     * - roger: 收到信息回执
     * - user: 指定target
     * - team: 团队会员
     * @param target            发送目标
     * @param body              发送内容（对象或数组）
     * @param callback          发送回调
     * @param againNum
     */
    this.sendTo = function (messageType, target, body, callback, againNum = 0) {
        if (typeof target === "object" && typeof body === "undefined") {
            body = target;
            target = null;
        }
        if (typeof target === "function") {
            body = target;
            target = null;
        }
        if (typeof body === "function") {
            callback = body;
            body = null;
        }
        if (body === null || typeof body !== "object") {
            body = {};
        }
        //
        var thas = this;
        if (this.__instance === null || this.__connected === false) {
            if (againNum < 10 && messageType != 'team') {
                setTimeout(function () {
                    thas.sendTo(messageType, target, body, callback, thas.__rNum(againNum) + 1)
                }, 600);
                if (againNum === 0) {
                    this.connection();
                }
            } else {
                if (this.__instance === null) {
                    this.__log("[WS] Service not connected");
                    typeof callback === "function" && callback({status: 0, message: '服务未连接'});
                } else {
                    this.__log("[WS] Failed connection");
                    typeof callback === "function" && callback({status: 0, message: '未连接成功'});
                }
            }
            return this;
        }
        if (['refresh', 'unread', 'read', 'roger', 'user', 'team'].indexOf(messageType) === -1) {
            this.__log("[WS] Wrong message messageType: " + messageType);
            typeof callback === "function" && callback({status: 0, message: '错误的消息类型: ' + messageType});
            return this;
        }
        //
        var contentId = 0;
        if (messageType === 'roger') {
            contentId = target;
            target = null;
        }
        var messageId = '';
        if (typeof callback === "string" && callback === 'special') {
            callback = function (res) {
                res.status === 1 && thas.triggerSpecialListener({
                    target: target,
                    body: body,
                });
            }
        }
        if (typeof callback === "function") {
            messageId = this.__randString(16);
            this.__callbackid[messageId] = callback;
        }
        this.__instance.send(JSON.stringify({
            messageType: messageType,
            messageId: messageId,
            contentId: contentId,
            channel: this.__config.channel,
            username: this.__config.username,
            target: target,
            body: body,
            time: Math.round(new Date().getTime() / 1000),
        }));
        return this;
    }

    /**
     * 关闭连接
     */
    this.close = function () {
        if (this.__instance === null) {
            this.__log("[WS] Service not connected");
            return this;
        }
        if (this.__connected === false) {
            this.__log("[WS] Failed connection");
            return this;
        }
        this.__instance.close();
        return this;
    }

    return this.config(config);
}
