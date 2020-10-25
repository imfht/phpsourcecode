<?php
namespace app\ucenter\widget;

use think\Controller;

class Follow extends Controller
{
    /**
     * follow  关注按钮
     */
    public function follow($follow_who = 0)
    {
        $follow_who = intval($follow_who);
        $who_follow = is_login();
        $is_following = model('common/Follow')->isFollow($who_follow, $follow_who);
        $this->assign('is_following', $is_following ? 1 : 0);
        $this->assign('is_self', $who_follow == $follow_who);
        $this->assign('follow_who', $follow_who);

        return $this->fetch();
    }


}