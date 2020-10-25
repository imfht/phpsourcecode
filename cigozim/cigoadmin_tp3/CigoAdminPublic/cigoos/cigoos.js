function cigoFindCharIndexFromStringByNum(srcStr, searchStr, fromIndex, num) {
    if (num <= 0) {
        return -1;
    }
    if (fromIndex > (srcStr.length - 1)) {
        return -1;
    }
    var currIndex = srcStr.indexOf(searchStr, fromIndex);
    if (currIndex <= -1) {
        return -1;
    }

    if (--num == 0) {
        return currIndex;
    } else {
        return cigoFindCharIndexFromStringByNum(srcStr, searchStr, currIndex + 1, num);
    }
}

function show_status_label(status, tipFlags) {
    if (status < tipFlags.length) {
        return tipFlags[status];
    }
    return 'unkown';
}

function show_sex_label(sex, sexFlags) {
    if (sex < sexFlags.length) {
        return sexFlags[sex];
    }
    return 'unkown';
}

function getTitleTab(level, tabItem) {
    var tab = '';
    for (var i = 0; i < level; i++) {
        tab += tabItem;
    }
    return tab;
}

function autoTipAndGo(data, successCallBackFunc, errorCallBackFunc) {
    if (data.status == 1) {
        if (undefined != successCallBackFunc && '' != successCallBackFunc) {
            successCallBackFunc = eval(successCallBackFunc);
            successCallBackFunc(data);
        } else {
            if (data.url) {
                cigoLayer.msg(data.info + '<br/><br/>稍后页面将自动跳转~~', {icon: 6});
            } else {
                cigoLayer.msg(data.info, {icon: 6});
            }
            setTimeout(function () {
                if (data.url) {
                    location.href = data.url;
                } else {
                    location.reload(true);
                }
            }, 1500);
        }
    } else {
        if (undefined != errorCallBackFunc && '' != errorCallBackFunc) {
            errorCallBackFunc = eval(errorCallBackFunc);
            errorCallBackFunc(data);
        } else {
            cigoLayer.msg(data.info, {icon: 5});
        }
    }
}

function ajaxGet(evt, ctrlView, successCallBackFunc, errorCallBackFunc) {
    (ctrlView == undefined) ? ctrlView = $(this) : false;

    if (ctrlView.hasClass('confirm')) {
        if (!confirm('确认要执行该操作吗?')) {
            cigoLayer.msg('操作已取消!');
            return false;
        }
    }

    var target = ctrlView.attr('href') || ctrlView.attr('url');
    if (target !== undefined && target !== '' && target !== '#') {
        $.get(target, function (data) {
            autoTipAndGo(data, successCallBackFunc, errorCallBackFunc);
        });
    }
    return false;
}

function formPost(evt, ctrlView, successCallBackFunc, errorCallBackFunc, beforeSubmiteFunc) {
    (ctrlView == undefined) ? ctrlView = $(this) : false;
    var target = ctrlView.attr('href') || ctrlView.attr('url');
    if (target !== undefined && target !== '' && target !== '#') {
        var formId;
        if (formId = ctrlView.attr('formId')) {
            var requestParames = "";
            if (undefined != beforeSubmiteFunc && '' != beforeSubmiteFunc) {
                beforeSubmiteFunc = eval(beforeSubmiteFunc);
                requestParames = beforeSubmiteFunc();
            }
            requestParames += $('#' + formId).find('input, select, textarea').serialize();
            $.post(target, requestParames, function (data) {
                autoTipAndGo(data, successCallBackFunc, errorCallBackFunc);
            });
        }
    }
    return false;
}

function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}

function compare(property, ascFlag) {
    return function (a, b) {
        var value1 = a[property];
        var value2 = b[property];
        return ascFlag
            ? (value1 - value2)
            : (value2 - value1);
    }
}

$(function () {//Layer相关
    window.cigoForm = layui.form;
    window.cigoDate = layui.laydate;
    window.cigoLayer = layui.layer;
    window.cigoTable = layui.table;

    //ajax get请求
    $('.ajax-get').click(ajaxGet);
    //表单提交
    $('.form_post').click(formPost);
});


function getRandStr(lenLimit) {
    lenLimit = lenLimit || 8;
    var srcStr = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';
    var maxPos = srcStr.length;
    var desStr = '';
    for (var i = 0; i < lenLimit; i++) {
        desStr += srcStr.charAt(Math.floor(Math.random() * maxPos));
    }
    return desStr;
}