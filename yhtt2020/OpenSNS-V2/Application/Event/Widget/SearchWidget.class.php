<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/18
 * Time: 10:52
 * @author 路飞<lf@ourstu.com>
 */

namespace Event\Widget;

use Think\Controller;

class SearchWidget extends Controller
{
    public function render()
    {
        $this->assignEvent();
        import_lang('Event');
        $this->display(T('Application://Event@Widget/search'));
    }

    private function assignEvent()
    {
        //TODO
        $keywords = I('post.keywords','','text');

        if($keywords) {
            $title = modC('EVENT_SHOW_TITLE', '', 'Event');//标题
            $type = modC('EVENT_SHOW_TYPE', 0, 'Event');//删选类型，1为后台推荐，0为全部
            $field = modC('EVENT_SHOW_ORDER_FIELD', 'view_count', 'Event');//排序方式
            $order = modC('EVENT_SHOW_ORDER_TYPE', 'desc', 'Event');//活动查找升序降序
            $order = $field . " " . $order;

            if ($type == 0) {
                $list = M('Event')->where(array('status' => 1, 'eTime' => array('gt',time()), 'title' => array('like', '%' . $keywords . '%')))->order($order)->select();
            } else {
                $list = M('Event')->where(array('status' => 1, 'eTime' => array('gt',time()),'is_recommend' => 1, 'title' => array('like', '%' . $keywords . '%')))->order($order)->select();

            }
            foreach ($list as &$v) {
                $v['user'] = query_user(array('space_url', 'nickname'), $v['uid']);
            }
            unset($v);
        }


        $this->assign('event_lists', $list);
    }
}