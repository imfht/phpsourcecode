<?php
namespace module\admin;

use lib\Action, lib\RBAC;

class yijianMod extends base
{

    public function index()
    {
        $db = model('yijian');
        $count = $db->count();
        list ($limit, $pageString) = $this->page($count);
        $list = $db->limit($limit)->select();
        $this->assign('list', $list);
        $this->assign('pageString', $pageString);
        $this->display();
    }
}