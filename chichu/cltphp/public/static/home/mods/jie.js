/**

 @Name: 求解板块

 */

layui.define('fly', function (exports) {

    var $ = layui.jquery;
    var layer = layui.layer;
    var util = layui.util;
    var laytpl = layui.laytpl;
    var form = layui.form;
    var fly = layui.fly;

    var gather = {}, dom = {
        jieda: $('#jieda')
        , content: $('#L_content')
        , jiedaCount: $('#jiedaCount')
    };
    fly.form['set-mine'] = function (data, required) {
        layer.msg('修改成功', {
            icon: 1
            , time: 1000
            , shade: 0.1
        }, function () {
            location.reload();
        });
    }
    $('body').on('click', '.jie-admin', function () {
        var othis = $(this), type = othis.attr('type');
        gather.jieAdmin[type] && gather.jieAdmin[type].call(this, othis.parent());
    });
    //定位分页
    if (/\/page\//.test(location.href) && !location.hash) {
        var replyTop = $('#flyReply').offset().top - 80;
        $('html,body').scrollTop(replyTop);
    }

    exports('jie', null);
});