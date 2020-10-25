$(function () {
    $("a").hover(function () {
        tips($(this));
    }).mouseleave(function () {
        layer.closeAll();
    });
    $("button").hover(function () {
        tips($(this));
    }).mouseleave(function () {
        layer.closeAll();
    });
    function tips(obj) {
        for (var i = 0; i < obj.length; i++) {
            var a = obj[i];
            if (a.hasAttribute('layTips')) {
                var _this = $(a);
                var content = _this.attr('layTips');
                var arr = content.split('|');
                var tips = typeof arr[1] === 'undefined' ? 3 : arr[1];
                var color = typeof arr[2] === 'undefined' ? '#3595CC' : arr[2];
                layer.tips(arr[0], _this, {tips: [tips, color], time:60000});
            }
        }
    }
});