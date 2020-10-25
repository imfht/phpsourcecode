/**
 * 关注
 * @type {Object}
 */
var follower = {
    'bind_follow': function () {
        $('[data-role="follow"]').unbind('click')
        $('[data-role="follow"]').click(function () {
            var $this = $(this);
            var uid = $this.attr('data-follow-who');
            $.post(Url('Ucenter/Public/follow'), {uid: uid}, function (msg) {
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
            $.post(Url('Ucenter/Public/unfollow'), {uid: uid}, function (msg) {
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

follower.bind_follow();