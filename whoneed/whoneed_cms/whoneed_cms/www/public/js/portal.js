/**
 * portal相关业务
 *
 * @name portal
 * @author Ican Cheung <zhangrq@ucweb.com>
 *
 */
var portal = portal || {};
var P = portal;

portal.Statis = portal.Statis || {};

(function(statis) {
    // 如果在节点上找不到统计参数，则往上再查几级
    var _getStatis = function($target, bubble) {
        if (bubble <= 0) {
            return undefined;
        }
        if (undefined == $target || $target.length == 0) {
            return undefined;
        }
        var statis = $target.attr("data-statis");
        if (null != statis && undefined != statis && statis.length >= 1) {
            return statis;
        }
        return _getStatis($target.parent(), --bubble);
    };

    statis.handler = function(event) {
        // 在需要统计的节点上，添加data-statis属性
        var $target = $(event.target);
        // 设置统计信息到cookie
        var statis = _getStatis($target, 5);
        if (null != statis && undefined != statis && statis.length >= 1) {
            ucb.Cookie.set("statis", statis, {
                path : "/",
                domain : ".9game.cn" // 揪心
            });
        } else {
            ucb.Cookie.remove("statis");
        }
    };

    statis.documentListener = function() {
        var event = "click";
        if (ucb.Supports.Touch) {
            event = "touchstart";
        } else if ("onmousedown" in window) {
            event = "mousedown";
        }
        // 每个页面初始化的时候，清除已有的值，确保页面点击的统计数据是干净的
        ucb.Cookie.remove("statis");
        $(document).on(event, statis.handler);
    };
})(portal.Statis);

// dom ready之后，开始监听事件
$(document).ready(function($) {
    P.Statis.documentListener();
});
