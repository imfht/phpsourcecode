/**
 * Created by Administrator on 14-6-27.
 */
$(function () {


    $('.event_sign').magnificPopup({
        type: 'ajax',
        overflowY: 'scroll',
        modal: true,
        callbacks: {
            ajaxContentAdded: function () {
                // Ajax content is loaded and appended to DOM

                console.log(this.content);
            }
        }
    });

    $('.unSign').click(function () {
        if (confirm('确定要取消报名么？')) {
            var event_id = $(this).attr('data-eventID');
            $.post(U('Event/Index/unSign'), {event_id: event_id}, function (res) {
                if (res.status) {
                    toast.success(res.info);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
                else {
                    toast.error(res.info);
                }
            }, 'json');
        }
    });


    $('.delEvent').click(function () {
        if (confirm('确定要删除活动么？')) {
            var event_id = $(this).attr('data-eventID');
            $.post(U('Event/Index/doDelEvent'), {event_id: event_id}, function (res) {
                if (res.status) {
                    toast.success(res.info);
                    setTimeout(function () {
                        location.href = res.url;
                    }, 1500);
                }
                else {
                    toast.error(res.info);
                }
            }, 'json');
        }
    });

    $('.endEvent').click(function () {
        if (confirm('确定要提前结束活动么？')) {
            var event_id = $(this).attr('data-eventID');
            $.post(U('Event/Index/doEndEvent'), {event_id: event_id}, function (res) {
                if (res.status) {
                    toast.success(res.info);
                    setTimeout(function () {
                        location.reload();
                    }, 1500);
                }
                else {
                    toast.error(res.info);
                }
            }, 'json');
        }
    });
})

function shenhe(obj) {
    var uid = obj.attr('data-uid');
    var event_id = obj.attr('data-eventID');
    var tip = 1;
    $.post(U('Event/Index/shenhe'), {uid: uid, event_id: event_id, tip: tip}, function (res) {
        if (res.status) {
            toast.success(res.info);

            var html = '<a data-eventID="'+event_id+'" data-uid="' + uid + '" onclick="qushenhe($(this))" title="取消审核"><span class="glyphicon glyphicon-remove"></span></a>';
            obj.parent().html(html);
            $('#attend_count').html(parseInt($('#attend_count').text()) + 1);
            $('#sign_count').html(parseInt($('#sign_count').text()) - 1);
        }
        else {
            toast.error(res.info);
        }
    }, 'json');
}

function qushenhe(obj) {
    var uid = obj.attr('data-uid');
    var event_id = obj.attr('data-eventID');
    var tip = 0;
    $.post(U('Event/Index/shenhe'), {uid: uid, event_id: event_id, tip: tip}, function (res) {
        if (res.status) {
            toast.success(res.info);
            var html = '<a data-eventID="'+event_id+'" data-uid="' + uid + '" onclick="shenhe($(this))" title="通过审核"><span class="glyphicon glyphicon-ok"></span></a>';
            obj.parent().html(html);
            $('#attend_count').html(parseInt($('#attend_count').text()) - 1);
            $('#sign_count').html(parseInt($('#sign_count').text()) + 1);
        }
        else {
            toast.error(res.info);
        }
    }, 'json');
}