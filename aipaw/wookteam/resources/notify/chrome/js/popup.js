chrome.runtime.onMessage.addListener(function (request, sender, sendResponse) {
    if (request.act === "instances") {
        showLists(request.instances);
    }
});

chrome.runtime.sendMessage({
    act: 'getInstances'
}, function (response) {
    showLists(response);
});

function onClick(index) {
    chrome.runtime.sendMessage({
        act: 'clickInstances',
        index: index,
    });
}

function onDelete(index) {
    chrome.runtime.sendMessage({
        act: 'delInstances',
        index: index,
    });
}

function showLists(lists) {
    var html = '';
    var j = 1;
    var length = Object.keys(lists).length;
    var tempLists = $A.jsonParse($A.getStorage("configLists"), {});
    for (var index in lists) {
        if (!lists.hasOwnProperty(index)) {
            continue;
        }
        if (tempLists[index] === null || typeof tempLists[index] !== "object") {
            continue;
        }
        if (tempLists[index].disabled === true) {
            continue;
        }
        const item = Object.assign(lists[index], {
            nickname: tempLists[index].nickname,
            online: tempLists[index].online,
        });
        html+= '<li class="message_box' + (j == length ? ' last' : '') + '" data-index="' + index + '" data-token="' + item.token + '">';
        if (item.nickname) {
            html+= `<div class="message_username">${item.nickname} (${item.username})</div>`;
        } else {
            html+= '<div class="message_username">' + item.username + '</div>';
        }
        html+= '<div class="message_host">' + index + '</div>';
        html+= '<div class="message_unread' + (item.unread == 0 ? ' zero' : '') + '' + (item.online === true ? '' : ' offline') + '">未读: ' + item.unread + '</div>';
        html+= '<div class="message_delete">删除</div>';
        html+= '</li>';
        j++;
    }
    if (!html) {
        html+= '<li class="message_box">';
        html+= '<div class="message_loading">没有相关的记录！</div>';
        html+= '</li>';
    }
    $("#message_div").html('<ul class="message_lists">' + html + '</ul>');
    $("div.message_delete").click(function(){
        if (confirm("确定要删除此记录吗？")) {
            onDelete($(this).parents("li").attr("data-index"));
        }
    });
    $("div.message_unread,div.message_username,div.message_host").click(function(e){
        const index = $(this).parents("li").attr("data-index");
        const token = encodeURIComponent($(this).parents("li").attr("data-token"));
        var opurl = 'http://' + index + '/todo?token=' + token;
        if (e.target.className == 'message_unread') {
            opurl+= '&open=chat'
        }
        chrome.tabs.query({}, function (tabs) {
            var has = false;
            tabs.some(function (item) {
                if ($A.getHost(item.url) == index) {
                    var params = {rand: Math.round(new Date().getTime())};
                    if (e.target.className == 'message_unread') {
                        params.open = 'chat';
                    }
                    var url = $A.getPathname(item.url) == '/' ? opurl : ($A.urlAddParams($A.removeURLParameter(item.url, ['open', 'rand']), params))
                    chrome.windows.update(item.windowId, {focused: true});
                    chrome.tabs.highlight({tabs: item.index, windowId: item.windowId});
                    chrome.tabs.update({url: url});
                    onClick(index);
                    return has = true;
                }
            });
            if (!has) {
                chrome.tabs.create({ url: opurl });
                onClick(index);
            }
        });
    })
}

