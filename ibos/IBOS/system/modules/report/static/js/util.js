define(function(){
    var fetch = (function(){
        var defaults = {
            type: 'POST',
            data: {},
            success: $.noop,
            dataType: 'json',
            contentType: "application/json;utf-8",
            error: function(res){
                try {
                    res = JSON.parse(res.responseText);
                    if( res.isLogin == false ){
                        location.reload();
                        Ui.tip('请重新登录', "danger");
                        return;
                    }
                    Ui.tip(res.msg, 'danger');
                } catch(e) {
                    Ui.tip(res.responseText, 'danger');
                }
                location.hash = "error";
            }
        };
        return function(url, params){
            var opts = $.extend({}, defaults, params, {
                url: Ibos.app.url(url)
            });
            return $.ajax(opts);
        };
    })();
    return {
        fetch: fetch,
        fieldType: ['长文本', '短文本', '数字', '日期与时间','时间','日期','下拉', '富文本' ],
        queue: function(queue, fn){
            var task = null,
                data = [],
                finish = false,
                obj = $({}); 

            var _getFn = function(eventname){
                return function(val){
                    val = val || null;
                    obj.trigger(eventname, val);
                };
            };
            var _next = function(){
                if( finish ) return;
                if( (task = queue.shift()) ){
                    var eventName = "queueEvent";
                    obj.on(eventName, function(evt, val){
                        data.push(val);
                        _next();
                    });
                    task.call(null, _getFn(eventName));
                }else{
                    finish = true;
                    fn.apply(null, data);
                }
            };
            _next();
        }
    };
});
