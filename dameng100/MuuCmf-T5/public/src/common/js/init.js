/**
 * 绑定回到顶部
 */
$(function () {
    $(window).on('scroll', function () {
        var st = $(document).scrollTop();
        if (st > 0) {
            $('#go-top').css('display','block');
        } else {
            $('#go-top').hide();
        }
    });
    $('#tool .go-top').on('click', function () {
        $('html,body').animate({'scrollTop': 0}, 500);
    });

    $('#go-top .uc-2vm').hover(function () {
        $('#go-top .uc-2vm-pop').removeClass('dn');
    }, function () {
        $('#go-top .uc-2vm-pop').addClass('dn');
    });
});

/**
 * 绑定登出事件
 */
$(function(){
    $('[event-node=logout]').click(function () {
        var url = $(this).data('url');
        $.get(url, function (msg) {
            toast.success(msg.msg + '。', '温馨提示');
            setTimeout(function () {
                location.href = msg.url;
            }, 1500);
        }, 'json')
    });
})


