<?php
namespace module\admin;

use lib\Action, lib\RBAC;

class meetingMod extends base
{

    public function index()
    {
        $db = model('meeting');
        $count = $db->count();
        list ($limit, $pageString) = $this->page($count);
        $list = $db->limit($limit)->select();
        $this->assign('list', $list);
        $this->assign('pageString',$pageString);
        $this->display();
    }
}