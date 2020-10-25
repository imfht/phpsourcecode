define(['jquery'], function($) {
    var module = {};

    module.ready = function(callback) {
        var weixinReady = function() {
            if($.isFunction(callback)) {
                callback('weixin');
            }
        }
        if(typeof WeixinJSBridge == "undefined") {
            if(document.addEventListener){
                document.addEventListener('WeixinJSBridgeReady', weixinReady, false);
            } else if (document.attachEvent){
                document.attachEvent('WeixinJSBridgeReady', weixinReady);
                document.attachEvent('onWeixinJSBridgeReady', weixinReady);
            }
        } else {
            weixinReady();
        }
    };

    return module;
});

