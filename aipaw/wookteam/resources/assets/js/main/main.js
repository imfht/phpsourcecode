/**
 * 页面专用
 */

import '../../sass/main.scss';

(function (window) {

    let apiUrl = window.location.origin + '/api/';
    let $ = window.$A;

    $.extend({

        fillUrl(str) {
            if (str.substring(0, 2) === "//" ||
                str.substring(0, 7) === "http://" ||
                str.substring(0, 8) === "https://" ||
                str.substring(0, 6) === "ftp://" ||
                str.substring(0, 1) === "/") {
                return str;
            }
            return window.location.origin + '/' + str;
        },

        webUrl(str) {
            return $A.fillUrl(str || '');
        },

        apiUrl(str) {
            if (str.substring(0, 2) === "//" ||
                str.substring(0, 7) === "http://" ||
                str.substring(0, 8) === "https://" ||
                str.substring(0, 6) === "ftp://" ||
                str.substring(0, 1) === "/") {
                return str;
            }
            return apiUrl + str;
        },

        apiAjax(params) {
            if (typeof params !== 'object') return false;
            if (typeof params.success === 'undefined') params.success = () => { };
            params.url = this.apiUrl(params.url);
            //
            let beforeCall = params.beforeSend;
            params.beforeSend = () => {
                $A.aAjaxLoad++;
                $A(".w-spinner").show();
                //
                if (typeof beforeCall == "function") {
                    beforeCall();
                }
            };
            //
            let completeCall = params.complete;
            params.complete = () => {
                $A.aAjaxLoad--;
                if ($A.aAjaxLoad <= 0) {
                    $A(".w-spinner").hide();
                }
                //
                if (typeof completeCall == "function") {
                    completeCall();
                }
            };
            //
            let callback = params.success;
            params.success = (data, status, xhr) => {
                if (typeof data === 'object') {
                    if (data.ret === -1 && params.checkRole !== false) {
                        //身份丢失
                        $A.app.$Modal.error({
                            title: '温馨提示',
                            content: data.msg,
                            onOk: () => {
                                $A.userLogout();
                            }
                        });
                        return;
                    }
                    if (data.ret === -2 && params.role !== false) {
                        //没有权限
                        $A.app.$Modal.error({
                            title: '权限不足',
                            content: data.msg ? data.msg : "你没有相关的权限查看或编辑！"
                        });
                    }
                }
                if (typeof callback === "function") {
                    callback(data, status, xhr);
                }
            };
            //
            $A.ajax(params);
        },
        aAjaxLoad: 0,

        /**
         * 编辑器参数配置
         * @returns {{modules: {toolbar: *[]}}}
         */
        editorOption() {
            return {
                modules: {
                    toolbar: [
                        ['bold', 'italic'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }, { 'background': [] }],
                        [{ 'align': [] }]
                    ]
                }
            };
        },

        /**
         * 获取token
         * @returns {boolean}
         */
        getToken() {
            let token = $A.token();
            return $A.count(token) < 10 ? false : token;
        },

        /**
         * 设置token
         * @param token
         */
        setToken(token) {
            $A.token(token);
        },

        /**
         * 获取会员账号
         * @returns string
         */
        getUserName() {
            if ($A.getToken() === false) {
                return "";
            }
            let userInfo = $A.getUserInfo();
            return $A.ishave(userInfo.username) ? userInfo.username : '';
        },

        /**
         * 获取会员昵称
         * @param nullName
         * @returns {string|*}
         */
        getNickName(nullName = true) {
            if ($A.getToken() === false) {
                return "";
            }
            let userInfo = $A.getUserInfo();
            return $A.ishave(userInfo.nickname) ? userInfo.nickname : (nullName ? $A.getUserName() : '');
        },

        /**
         * 获取用户信息（并保存）
         * @param callback                  网络请求获取到用户信息回调（监听用户信息发生变化）
         * @returns Object
         */
        getUserInfo(callback) {
            if (typeof callback === 'function' || callback === true) {
                $A.apiAjax({
                    url: 'users/info',
                    error: () => {
                        $A.userLogout();
                    },
                    success: (res) => {
                        if (res.ret === 1) {
                            $A.storage("userInfo", res.data);
                            $A.setToken(res.data.token);
                            $A.triggerUserInfoListener(res.data);
                            typeof callback === "function" && callback(res.data, $A.getToken() !== false);
                        }
                    },
                });
            }
            return $A.jsonParse($A.storage("userInfo"));
        },

        /**
         * 根据用户名获取用户基本信息
         * @param username
         * @param callback
         * @param cacheTime
         */
        getUserBasic(username, callback, cacheTime = 300) {
            if (typeof callback !== "function") {
                return;
            }
            if (!username) {
                callback({}, false);
                return;
            }
            //
            let keyName = '__userBasic:' + username.substring(0, 1) + '__';
            let localData = $A.jsonParse(window.localStorage[keyName]);
            if ($A.getObject(localData, username + '.success') === true) {
                callback(localData[username].data, true);
                if (localData[username].update + cacheTime > Math.round(new Date().getTime() / 1000)) {
                    return;
                }
            }
            //
            $A.__userBasicObject.push({
                username: username,
                callback: callback
            });
            //
            $A.__userBasicTimeout++;
            let timeout = $A.__userBasicTimeout;
            setTimeout(() => {
                timeout === $A.__userBasicTimeout && $A.__userBasicEvent();
            }, 100);
        },
        __userBasicEvent() {
            if ($A.__userBasicLoading === true) {
                return;
            }
            $A.__userBasicLoading = true;
            //
            let userArray = [];
            $A.__userBasicObject.some((item) => {
                userArray.push(item.username);
                if (userArray.length >= 30) {
                    return true;
                }
            });
            //
            $A.apiAjax({
                url: 'users/basic',
                data: {
                    username: $A.jsonStringify(userArray),
                },
                error: () => {
                    userArray.forEach((username) => {
                        let tmpLists = $A.__userBasicObject.filter((item) => { return item.username == username });
                        tmpLists.forEach((item) => {
                            if (typeof item.callback === "function") {
                                item.callback({}, false);
                                item.callback = null;
                            }
                        });
                    });
                    //
                    $A.__userBasicLoading = false;
                    $A.__userBasicObject = $A.__userBasicObject.filter((item) => { return typeof item.callback === "function"});
                    if ($A.__userBasicObject.length > 0) {
                        $A.__userBasicEvent();
                    }
                },
                success: (res) => {
                    if (res.ret === 1) {
                        res.data.forEach((data) => {
                            let keyName = '__userBasic:' + data.username.substring(0, 1) + '__';
                            let localData = $A.jsonParse(window.localStorage[keyName]);
                            localData[data.username] = {
                                success: true,
                                update: Math.round(new Date().getTime() / 1000),
                                data: data
                            };
                            window.localStorage[keyName] = $A.jsonStringify(localData);
                        });
                    }
                    userArray.forEach((username) => {
                        let tmpLists = $A.__userBasicObject.filter((item) => { return item.username == username });
                        tmpLists.forEach((item) => {
                            if (typeof item.callback === "function") {
                                let info = res.data.filter((data) => { return data.username == username });
                                if (info.length === 0) {
                                    item.callback({}, false);
                                } else {
                                    item.callback(info[0], true);
                                }
                                item.callback = null;
                            }
                        });
                    });
                    //
                    $A.__userBasicLoading = false;
                    $A.__userBasicObject = $A.__userBasicObject.filter((item) => { return typeof item.callback === "function"});
                    if ($A.__userBasicObject.length > 0) {
                        $A.__userBasicEvent();
                    }
                }
            });
        },
        __userBasicTimeout: 0,
        __userBasicLoading: false,
        __userBasicObject: [],

        /**
         * 打开登录页面
         */
        userLogout() {
            $A.token("");
            $A.storage("userInfo", {});
            $A.triggerUserInfoListener({});
            let from = window.location.pathname == '/' ? '' : encodeURIComponent(window.location.href);
            if (typeof $A.app === "object") {
                $A.app.goForward({path: '/', query: from ? {from: from} : {}}, true);
            } else {
                window.location.replace($A.webUrl() + (from ? ('?from=' + from) : ''));
            }
        },

        /**
         * 权限是否通过
         * @param role
         * @returns {boolean}
         */
        identity(role) {
            let userInfo = $A.getUserInfo();
            return $A.identityRaw(role, userInfo.identity);
        },

        /**
         * 权限是否通过
         * @param role
         * @returns {boolean}
         */
        identityRaw(role, identity) {
            let isRole = false;
            $A.each(identity, (index, res) => {
                if (res === role) {
                    isRole = true;
                }
            });
            return isRole;
        },

        /**
         * 监听用户信息发生变化
         * @param listenerName      监听标识
         * @param callback          监听回调
         */
        setOnUserInfoListener(listenerName, callback) {
            if (typeof listenerName != "string") {
                return;
            }
            if (typeof callback === "function") {
                $A.__userInfoListenerObject[listenerName] = {
                    callback: callback,
                }
            }
        },
        triggerUserInfoListener(userInfo) {
            let key, item;
            for (key in $A.__userInfoListenerObject) {
                if (!$A.__userInfoListenerObject.hasOwnProperty(key)) continue;
                item = $A.__userInfoListenerObject[key];
                if (typeof item.callback === "function") {
                    item.callback(userInfo, $A.getToken() !== false);
                }
            }
        },
        __userInfoListenerObject: {},

        /**
         * 监听任务发生变化
         * @param listenerName      监听标识
         * @param callback          监听回调
         * @param callSpecial       是否监听几种特殊事件（非操作任务的）
         */
        setOnTaskInfoListener(listenerName, callback, callSpecial) {
            if (typeof listenerName != "string") {
                return;
            }
            if (typeof callback === "function") {
                $A.__taskInfoListenerObject[listenerName] = {
                    special: callSpecial === true,
                    callback: callback,
                }
            }
        },
        triggerTaskInfoListener(act, taskDetail, sendToWS = true) {
            let key, item;
            for (key in $A.__taskInfoListenerObject) {
                if (!$A.__taskInfoListenerObject.hasOwnProperty(key)) continue;
                item = $A.__taskInfoListenerObject[key];
                if (typeof item.callback === "function") {
                    if (['addlabel', 'deleteproject', 'deletelabel', 'labelsort', 'tasksort'].indexOf(act) === -1 || item.special === true) {
                        if (typeof taskDetail.__modifyUsername === "undefined") {
                            taskDetail.__modifyUsername = $A.getUserName();
                        }
                        item.callback(act, taskDetail);
                    }
                }
            }
            if (sendToWS === true) {
                $A.WSOB.sendTo('team', {
                    type: "taskA",
                    act: act,
                    taskDetail: taskDetail
                });
            }
        },
        __taskInfoListenerObject: {},

        /**
         * 获取待推送的日志并推送
         * @param taskid
         */
        triggerTaskInfoChange(taskid) {
            $A.apiAjax({
                url: 'project/task/pushlog',
                data: {
                    taskid: taskid,
                    pagesize: 20
                },
                success: (res) => {
                    if (res.ret === 1) {
                        res.data.lists.forEach((item) => {
                            let msgData = {
                                type: 'taskB',
                                username: item.username,
                                userimg: item.userimg,
                                indate: item.indate,
                                text: item.detail,
                                other: item.other
                            };
                            res.data.follower.forEach((username) => {
                                if (username != msgData.username && username != $A.getUserName()) {
                                    $A.WSOB.sendTo('user', username, msgData, 'special');
                                }
                            });
                        });
                    }
                }
            });
        }
    });

    /**
     * =============================================================================
     * *****************************   websocket assist   ****************************
     * =============================================================================
     */
    $.extend({
        /**
         * @param config {username, url, token, channel, logCallback}
         */
        WTWS: function (config) {
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
                };

                // 连接关闭时触发
                this.__instance.onclose = function (event) {
                    thas.__log("[WS] Connection closed", event);
                    thas.__connected = false;
                    thas.__instance = null;
                    thas.__autoLine(5);
                }

                // 连接出错
                this.__instance.onerror = function (event) {
                    thas.__log("[WS] Connection error", event);
                    thas.__connected = false;
                    thas.__instance = null;
                    thas.__autoLine(5);
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
             * - user: 发送消息，指定target
             * - info: 发送消息（不保存），指定target
             * - team: 团队会员
             * - docs: 知识库
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
                if (['refresh', 'unread', 'read', 'roger', 'user', 'info', 'team', 'docs'].indexOf(messageType) === -1) {
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
        },

        WSOB: {
            instance: null,
            isClose: false,

            /**
             * 初始化
             */
            initialize() {
                let url = $A.getObject(window.webSocketConfig, 'URL');
                if (!url) {
                    url = window.location.origin;
                    url = url.replace("https://", "wss://");
                    url = url.replace("http://", "ws://");
                    url+= "/ws";
                }
                let config = {
                    username: $A.getUserName(),
                    url: url,
                    token: $A.getToken(),
                    channel: 'web'
                };
                if (this.instance === null) {
                    this.instance = new $A.WTWS(config);
                    this.instance.connection()
                } else if (this.isClose) {
                    this.isClose = false
                    this.instance.config(config);
                    this.instance.connection();
                }
            },

            /**
             * 主动连接
             */
            connection() {
                this.initialize();
                this.instance.connection();
            },

            /**
             * 监听消息
             * @param listenerName
             * @param listenerType
             * @param callback
             */
            setOnMsgListener(listenerName, listenerType, callback) {
                this.initialize();
                this.instance.setOnMsgListener(listenerName, listenerType, callback);
            },

            /**
             * 添加特殊监听
             * @param listenerName
             * @param callback
             */
            setOnSpecialListener(listenerName, callback) {
                this.initialize();
                this.instance.setOnSpecialListener(listenerName, callback);
            },

            /**
             * 发送消息
             * @param messageType
             * @param target
             * @param body
             * @param callback
             */
            sendTo(messageType, target, body, callback) {
                this.initialize();
                this.instance.sendTo(messageType, target, body, callback);
            },

            /**
             * 关闭连接
             */
            close() {
                if (this.instance === null) {
                    return;
                }
                this.isClose = true
                this.instance.config(null).close();
            },

            /**
             * 获取消息描述
             * @param content
             * @returns {string}
             */
            getMsgDesc(content) {
                let desc;
                switch (content.type) {
                    case 'text':
                        desc = content.text;
                        break;
                    case 'image':
                        desc = $A.app.$L('[图片]');
                        break;
                    case 'file':
                        desc = $A.app.$L('[文件]');
                        break;
                    case 'taskB':
                        desc = content.text + " " + $A.app.$L("[来自关注任务]");
                        break;
                    case 'report':
                        desc = content.text + " " + $A.app.$L("[来自工作报告]");
                        break;
                    case 'video':
                        desc = $A.app.$L('[视频通话]');
                        break;
                    case 'voice':
                        desc = $A.app.$L('[语音通话]');
                        break;
                    default:
                        desc = $A.app.$L('[未知类型]');
                        break;
                }
                return desc;
            }
        }
    });

    window.$A = $;
})(window);
