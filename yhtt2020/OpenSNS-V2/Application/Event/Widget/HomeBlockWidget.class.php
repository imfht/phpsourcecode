<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 15-4-30
 * Time: 下午1:28
 * @author 郑钟良<zzl@ourstu.com>
 */

namespace Event\Widget;


use Think\Controller;

class HomeBlockWidget extends Controller
{
    public function render()
    {
        $this->assignEvent();
        import_lang('Event');
        $this->display(T('Application://Event@Widget/homeblock'));
    }

    private function assignEvent()
    {
        $title = modC('EVENT_SHOW_TITLE', '', 'Event');//标题
        $num = modC('EVENT_SHOW_COUNT', 4, 'Event');//首页展示个数
        $type = modC('EVENT_SHOW_TYPE', 0, 'Event');//删选类型，1为后台推荐，0为全部
        $field = modC('EVENT_SHOW_ORDER_FIELD', 'view_count', 'Event');//排序方式
        $order = modC('EVENT_SHOW_ORDER_TYPE', 'desc', 'Event');//活动查找升序降序
        $cache = modC('EVENT_SHOW_CACHE_TIME', 600, 'Event');//缓存时间
        $list = S('event_home_data');


        if (!$list) {
            $order = $field . " " . $order;
            if ($type == 0) {
                $list = M('Event')->where(array('status' => 1, 'eTime' => array('gt',time()),'status'=>1))->limit($num)->order($order)->select();
            } else {
                $list = M('Event')->where(array('status' => 1, 'eTime' => array('gt',time()),'is_recommend' => 1))->limit($num)->order($order)->select();

            }
            foreach ($list as &$v) {
                $v['user'] = query_user(array('space_url', 'nickname'), $v['uid']);
            }
            unset($v);

            S('event_home_data', $list, $cache);
        }
//dump($list);exit;
        $this->assign('event_lists', $list);
    }
} 