<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 14:03
 * @author 路飞<lf@ourstu.com>
 */

namespace Group\Widget;

use Group\Model\GroupMemberModel;
use Group\Model\GroupModel;
use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignGroupPost();
        $this->display(T('Application://Group@Widget/search'));
    }

    private function assignGroupPost()
    {
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $order_key=modC('FORUM_POST_ORDER','last_reply_time', 'Forum');
            $order_type=modC('FORUM_POST_TYPE','desc', 'Forum');

            $groupModel =  new GroupModel();
            $group_ids = $groupModel->where(array('status' => 1))->field('id')->select();
            $group_ids = getSubByKey($group_ids, 'id');
            $list = M('GroupPost')->where(array('status' => 1, 'group_id' => array('in', $group_ids), 'title' => array('like', '%' . $keywords . '%')))->order($order_key.' '.$order_type)->select();
            foreach($list as &$val){
                $val['group']=$groupModel->getGroup($val['group_id']);
            }
            unset($val);
        }

        $this->assign('group_post_list', $list);
    }
}