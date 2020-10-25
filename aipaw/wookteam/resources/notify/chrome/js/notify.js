/**
 * 连接已有会员
 * @type {*}
 */
var tagId = 0;
const instances = {};
const updateBadgeNum = function () {
    var badgeNum = 0;
    for (var index in instances) {
        if (!instances.hasOwnProperty(index)) {
            continue;
        }
        badgeNum+= instances[index].unread;
    }
    if (badgeNum == 0) {
        chrome.browserAction.setBadgeText({text: ''});
    } else {
        chrome.browserAction.setBadgeText({text: (badgeNum > 99 ? '99+' : badgeNum) + ''});
    }
    chrome.runtime.sendMessage({
        act: 'instances',
        instances: instances
    });
}
const getBadgeNum = function () {
    var configLists = $A.jsonParse($A.getStorage("configLists"), {});
    for (var index in configLists) {
        if (!configLists.hasOwnProperty(index)) {
            continue;
        }
        const key = index;
        const config = configLists[key];
        if (typeof instances[key] === "undefined") {
            instances[key] = {};
        }
        //
        if (instances[key].username !== config.username) {
            instances[key].username = config.username;
            instances[key].token = config.token;
            instances[key].unread = 0;
            instances[key].open = false;
            updateBadgeNum();
            if (typeof instances[key].ws !== "undefined") {
                instances[key].ws.config(null).close();
            }
            instances[key].ws = new WTWS({
                username: config.username,
                token: config.token,
                url: config.url,
                key: key,
                channel: 'chromeExtend'
            }).setOnMsgListener('notify', ['open', 'unread', 'user'], function (msgDetail) {
                let body = msgDetail.body;
                if (['taskA'].indexOf(body.type) !== -1) {
                    return;
                }
                var tempLists = $A.jsonParse($A.getStorage("configLists"), {});
                if (typeof tempLists[key] == "object" && tempLists[key].disabled === true) {
                    return;
                }
                switch (msgDetail.messageType) {
                    case 'open':
                        instances[key].open = true;
                        break;
                    case 'unread':
                        instances[key].unread = msgDetail.body.unread;
                        break;
                    case 'user':
                        instances[key].unread++;
                        chrome.tabs.query({active: true, currentWindow: true}, function (tabs) {
                            if ($A.getHost(tabs[0].url) != key) {
                                var opurl = 'http://' + key + '/todo?token=' + encodeURIComponent(instances[key].token) + '&open=chat';
                                $A.showNotify(key, {
                                    body: $A.getMsgDesc(body),
                                    icon: body.userimg
                                }, opurl);
                            }
                        });
                        break;
                }
                updateBadgeNum();
            }).sendTo('unread', function (res) {
                if (res.status === 1) {
                    instances[key].unread = $A.runNum(res.message);
                    updateBadgeNum();
                }
            });
        }
    }
    //
    tagId++;
    const tmpID = tagId;
    setTimeout(function () {
        if (tmpID === tagId) {
            getBadgeNum();
        }
    }, 5000);
}
getBadgeNum();

/**
 * 监听来自网站的会员信息
 */
chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
    var configLists;
    switch (request.act) {
        case "config":
            if (sender.tab) {
                var hostname = $A.getHost(sender.tab.url);
                if (hostname) {
                    configLists = $A.jsonParse($A.getStorage("configLists"), {});
                    if (typeof configLists[hostname] !== "object" || configLists[hostname] === null) {
                        configLists[hostname] = {};
                    }
                    configLists[hostname] = Object.assign(configLists[hostname], request.config, {
                        hostname: hostname,
                    });
                    $A.setStorage("configLists", $A.jsonStringify(configLists));
                    sendResponse(configLists);
                }
            }
            break;

        case "getInstances":
            sendResponse(instances);
            break;

        case "clickInstances":
            if (typeof instances[request.index] === "object") {
                instances[request.index].ws.sendTo('unread', function (res) {
                    if (res.status === 1) {
                        instances[request.index].unread = $A.runNum(res.message);
                        updateBadgeNum();
                    }
                    sendResponse(res);
                })
            }
            break;

        case "delInstances":
            configLists = $A.jsonParse($A.getStorage("configLists"), {});
            if (typeof configLists[request.index] === "object") {
                delete configLists[request.index];
                $A.setStorage("configLists", $A.jsonStringify(configLists));
            }
            if (typeof instances[request.index] === "object") {
                if (typeof instances[request.index].ws !== "undefined") {
                    instances[request.index].ws.config(null).close();
                }
                delete instances[request.index];
            }
            updateBadgeNum();
            break;
    }
});
