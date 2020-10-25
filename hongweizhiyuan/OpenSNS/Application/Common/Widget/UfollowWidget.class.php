<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 14-7-2
 * Time: 上午11:18
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Common\Widget;
use Think\Action;

/**input类型输入渲染
 * Class InputWidget
 * @package Usercenter\Widget
 * @郑钟良
 */
class UfollowWidget extends Action {

    /**关注渲染
     * @param int $is_following 是否已关注
     * @param int $uid
     * @author 郑钟良<zzl@ourstu.com>
     */
    public function render($is_following=0,$uid=0){
        $uid=op_t($uid);
        $this->assign('is_following',$is_following);
        $this->assign('uid',$uid);
        $this->display(T('Application://Common@Widget/ufollow'));
    }
}