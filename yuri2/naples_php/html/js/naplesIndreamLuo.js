//Published by Indream Luo
//Contact: indreamluo@qq.com
//Version: Chinese 1.0.0

/**
 * thanks for your article ,Indream Luo               ---Yuri2
 * 基于jq的互斥锁
 * */

!function ($) {
    window.indream = window.indream || {};
    $.indream = indream;

    indream.async = {
        //
        //锁
        //lock: 锁的编号
        //action: 解锁后执行的方法
        //
        lock: function (lock, action) {
            $.indream.async.waitings[lock] = $.indream.async.waitings[lock] || [];
            $.indream.async.waitings[lock].push(action);
            //如果该锁未被使用，则当前action阻塞该锁
            if (!$.indream.async.lockStatus[lock] && action) {
                $.indream.async.lockStatus[lock] = true;
                if (arguments.length > 2) {
                    var args = 'arguments[2]';
                    for (var i = 3; i < arguments.length; i++) {
                        args += ', arguments[' + i + ']';
                    }
                    eval('$.indream.async.action.call(action, ' + args + ')');
                } else {
                    $.indream.async.action.call(action);
                }
            }
        },
        //
        //解锁
        //lock: 锁的编号
        //
        releaseLock: function (lock) {
            $.indream.async.waitings[lock].shift();
            //如果等待队列有对象，则执行等待队列，否则解锁
            if ($.indream.async.waitings[lock].length) {
                $.indream.async.waitings[lock][0]();
            } else {
                $.indream.async.lockStatus[lock] = false;
            }
        },
        //
        //锁的状态
        //
        lockStatus: [],
        //
        //等待事件完成
        //lock:锁编码，相同的编码将被整合成一个序列，触发时同时触发
        //
        wait: function (lock, action) {
            $.indream.async.waitings[code] = $.indream.async.waitings[code] || [];
            $.indream.async.waitings[code].push(action);
        },
        //
        //等待序列
        //
        waitings: [],
        //
        //数据缓存
        //
        action: {
            //
            //监听和回调的相关方法
            //
            callback: {
                //
                //监听
                //
                listen: function (actionName, callback) {
                    var list = $.indream.async.action.callback.list;
                    list[actionName] = list[actionName] || [];
                    list[actionName].push(callback);
                },
                //
                //回调
                //
                call: function (actionName, args) {
                    var list = $.indream.async.action.callback.list;
                    if (list[actionName] && list[actionName].length) {
                        for (var i in list[actionName]) {
                            $.indream.async.action.call(list[actionName][i], args);
                        }
                    }
                },
                //
                //现有的回调列表
                //
                list: []
            },
            //
            //根据方法是否存在和参数是否存在选择适当的执行方式
            //
            call: function (action) {
                if (action) {
                    if (arguments.length > 1) {
                        var args = 'arguments[1]';
                        for (var i = 2; i < arguments.length; i++) {
                            args += ', arguments[' + i + ']';
                        }
                        eval('action(' + args + ')');
                    } else {
                        action();
                    }
                }
            }
        }
    }
}(window.jQuery);