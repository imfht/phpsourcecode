<?php
namespace Scoreshop\Widget;

use Think\Controller;

class HomeBlockWidget extends Controller
{
    public function render()
    {
        $this->assignIssue();
        $this->display(T('Application://Scoreshop@Widget/homeblock'));
    }

    public function assignIssue()
    {
        $num = modC('SCORESHOP_SHOW_COUNT', 4, 'Scoreshop');
        $field = modC('SCORESHOP_SHOW_ORDER_FIELD', 'view_count', 'Scoreshop');
        $order = modC('SCORESHOP_SHOW_ORDER_TYPE', 'desc', 'Scoreshop');
        $cache = modC('SCORESHOP_SHOW_CACHE_TIME', 600, 'Scoreshop');
        $show_type=modC('SCORESHOP_SHOW_TYPE',1,'Scoreshop');


        $data = S('Scoreshop_home_data');
        if (empty($data)) {
            $map = array('status' => 1);
            if($show_type==1){
                $map['is_new']=$show_type;
            }

            $content = D('Scoreshop')->where($map)->order($field . ' ' . $order)->limit($num)->select();

            foreach ($content as &$v) {
                $v['user'] = query_user(array('id', 'nickname', 'space_url', 'space_link', 'avatar128', 'rank_html'), $v['uid']);
                $v['issue'] = D('Issue')->field('id,title')->find($v['issue_id']);
            }
            $data = $content;
            S('Scoreshop_home_data', $data, $cache);
        }
        unset($v);

        $this->assign('contents_new', $data);
    }
} 