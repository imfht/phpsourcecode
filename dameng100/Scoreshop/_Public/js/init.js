$(function () {
    $('input,area').placeholder();//修复ieplace holder
    follower.bind_follow();
});

$(function () {
    /**
     * ajax-post
     * 将链接转换为ajax请求，并交给handleAjax处理
     * 参数：
     * data-confirm：如果存在，则点击后发出提示。
     * 示例：<a href="xxx" class="ajax-post">Test</a>
     */
    $(document).on('click', '.ajax-post', function (e) {
        //取消默认动作，防止跳转页面
        e.preventDefault();

        //获取参数（属性）
        var url = $(this).attr('href');
        var confirmText = $(this).attr('data-confirm');

        //如果需要的话，发出确认提示信息
        if (confirmText) {
            var result = confirm(confirmText);
            if (!result) {
                return false;
            }
        }

        //发送AJAX请求
        $.post(url, {}, function (a, b, c) {
            handleAjax(a);
        });
    });

    /**
     * ajax-form
     * 通过ajax提交表单，通过oneplus提示消息
     * 示例：<form class="ajax-form" method="post" action="xxx">
     */
    $(document).on('submit', 'form.ajax-form', function (e) {
        //取消默认动作，防止表单两次提交
        e.preventDefault();

        //禁用提交按钮，防止重复提交
        var form = $(this);
        $('[type=submit]', form).addClass('disabled');

        //获取提交地址，方式
        var action = $(this).attr('action');
        var method = $(this).attr('method');

        //检测提交地址
        if (!action) {
            return false;
        }

        //默认提交方式为get
        if (!method) {
            method = 'get';
        }

        //获取表单内容
        var formContent = $(this).serialize();

        //发送提交请求
        var callable;
        if (method == 'post') {
            callable = $.post;
        } else {
            callable = $.get;
        }
        callable(action, formContent, function (a) {
            handleAjax(a);
            $('[type=submit]', form).removeClass('disabled');
        });

        //返回
        return false;
    });
    
});


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
        $.get(U('Ucenter/System/logout'), function (msg) {
            $('body').append(msg.html);
            toast.success(msg.message + '。', '温馨提示');
            setTimeout(function () {
                location.href = msg.url;
            }, 1500);
        }, 'json')
    });
})

var follower = {
    'bind_follow': function () {
        $('[data-role="follow"]').unbind('click')
        $('[data-role="follow"]').click(function () {
            var $this = $(this);
            var uid = $this.attr('data-follow-who');
            $.post(U('Ucenter/Public/follow'), {uid: uid}, function (msg) {
                if (msg.status) {

                    $this.attr('class', $this.attr('data-before'));
                    $this.attr('data-role', 'unfollow');
                    $this.html('已关注');
                    follower.bind_follow();
                    toast.success(msg.info, '温馨提示');
                } else {
                    toast.error(msg.info, '温馨提示');
                }
            }, 'json');
        })

        $('[data-role="unfollow"]').unbind('click')
        $('[data-role="unfollow"]').click(function () {
            var $this = $(this);
            var uid = $this.attr('data-follow-who');
            $.post(U('Ucenter/Public/unfollow'), {uid: uid}, function (msg) {
                if (msg.status) {
                    $this.attr('class', $this.attr('data-after'));
                    $this.attr('data-role', 'follow');
                    $this.html('关注');
                    follower.bind_follow();
                    toast.success(msg.info, '温馨提示');
                } else {
                    toast.error(msg.info, '温馨提示');
                }
            }, 'json');
        })
    }
}


/**
 * 更新附件表单值
 * @return void
 */
var upAttachVal = function (type, attachId, obj) {
    var $attach_ids = obj;
    var attachVal = $attach_ids.val();
    var attachArr = attachVal.split(',');
    var newArr = [];
    for (var i in attachArr) {
        if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
            newArr.push(attachArr[i]);
        }
    }
    type === 'add' && newArr.push(attachId);
    $attach_ids.val(newArr.join(','));
    return newArr;
}



