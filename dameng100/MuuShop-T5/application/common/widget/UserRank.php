<?php
namespace app\common\widget;

use think\Controller;

class UserRank extends Controller{
    public function render($uid){
        $user=query_user(array('rank_link'),$uid);
        $this->assign('rank_link',$user['rank_link']);
        return $this->fetch('common@widget/userrank');
    }
}