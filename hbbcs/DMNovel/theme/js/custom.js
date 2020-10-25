//错误提醒窗口
var show_error = function (option) {
    var options;
    if (option.message) {
        options = $.extend({
            'message': '',
            'color': 'info' // success | info | warning | danger
        }, option || {});
    } else {
        options = {'message':option,'color':'info'}
    }

    var height=$(document).height();

    var str = $('<div>', {'class': 'boxmsg alert alert-' + options.color, 'role': "alert"})
        .html(options.message);

    $('body').append(str);
    str.fadeIn().fadeOut(6000);
    setTimeout(function () {
        str.remove();
    }, 6000);
}

//检测字符串是否是JSON
var check_json = function (str) {
    return ($.parseJSON(str)) ? true : false;
};

//JSON数组去重
function unique(arr) {
    var hash = {};
    var result = [];
    for (var i = 0, len = arr.length; i < len; i++) {
        if (!hash[arr[i]['id']]) {
            result.push(arr[i]);
            hash[arr[i]['id']] = true;
        }
    }
    return result;
}
//差集
minus = function (a, b) {
    return $.merge($.grep(a, function (i) {
            return $.inArray(i, b) == -1;
        }), $.grep(b, function (i) {
            return $.inArray(i, a) == -1;
        })
    );
};


var ajax_dialog = function (title, url) {
    var size=arguments[2]?arguments[2]:'modal-lg';
    var mymodal = $('body').find('#modal_ajax').remove();
    $('body').find('.modal-backdrop').remove();
    $('body').attr('style', '');
    if (mymodal.length == 0) {
        mymodal = $('<div>', {
            'class': 'modal fade',
            'tabindex': '-1',
            'role': 'dialog',
            'id': 'modal_ajax',
            'aria-labelledby': 'myModalLabel'
        }).append(
            $('<div>', {'class': 'modal-dialog '+size, 'role': 'document'}).append(
                $('<div>', {'class': 'modal-content'}).append(
                    $('<div>', {'class': 'modal-header'}).append(
                        $('<button>', {
                            'type': 'button',
                            'class': 'close',
                            'data-dismiss': 'modal',
                            'aria-label': 'Close'
                        }).append(
                            $('<span>', {'aria-hidden': 'true'}).html('&times;')
                        )
                    ).append(
                        $('<h4>', {'class': 'modal-title', 'id': 'myModalLabel'}).text(title)
                    )
                ).append(
                    $('<div>', {'class': 'modal-body'}).load(url)
                )
            )
        );
        $('body').append(mymodal);
    }
    mymodal.modal('show');
    mymodal.on('hidden.bs.modal', function () {
        mymodal.remove();
        $('body').find('.modal-backdrop').remove();
    });
}

ajax_dialog.close=function () {
    $('#modal_ajax').modal('hide');
}

$(function () {
    $(window).resize(function () {
        $('body').find('iframe').attr('height', $('body').height() - 70);
    });

});

//图片焦点滚动
function focusSwitch(focusBox, focusList, focusTab, speed) {
    if (!focusBox && !focusList && !focusTab)
        return;
    var i = 1, t = null, len = $(focusList + ' li').length;
    $(focusTab + ' li').mouseover(function () {
        i = $(focusTab + ' li').index($(this));
        addCurrent(i);
    });
    t = setInterval(init, speed);
    $(focusBox).hover(function () {
        clearInterval(t);
    }, function () {
        t = setInterval(init, speed);
    });
    function init() {
        addCurrent(i);
        i = (i + 1) % len;
    }

    function addCurrent(i) {
        $(focusTab + ' li').removeClass('on').eq(i).addClass('on');
        $(focusList + ' li').hide().eq(i).show();
    }
}