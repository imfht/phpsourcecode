$(function () {

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