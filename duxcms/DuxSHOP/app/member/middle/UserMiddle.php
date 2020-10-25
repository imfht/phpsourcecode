<?php
namespace app\member\middle;
/**
 * 用户信息
 */
class UserMiddle extends \app\base\middle\BaseMiddle {

    protected $_model = 'member/MemberUser';

    
    protected function data() {
        $keyword = html_clear($this->params['keyword']);
        $list = target($this->_model)->loadList(['_sql' => 'A.nickname like "%'.$keyword.'%" OR A.email like "%'.$keyword.'%" OR A.tel like "%'.$keyword.'%"']);
        foreach ($list as $key => $vo) {
            $desc = [];
            $desc[] = $vo['email'] ? $vo['email'] : '';
            $desc[] = $vo['tel'] ? $vo['tel'] : '';
            $list[$key]['id'] = $vo['user_id'];
            $list[$key]['text'] = $vo['show_name'];
            $list[$key]['image'] = $vo['avatar'];
            $list[$key]['desc'] =  implode(' ', $desc);
        }
        return $this->run($list);
    }

}