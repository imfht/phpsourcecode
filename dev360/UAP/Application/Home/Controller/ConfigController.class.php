<?php
namespace Home\Controller;

use Think\Controller;

class ConfigController extends Controller
{

    public function index()
    {
        $User = M('sys_keyvalue');
        $count = $User->where(array_filter($_GET))->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $User->where(array_filter($_GET))->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function add()
    {
        $this->assign('action', 'addEntity');
        $this->display('form');
    }

    public function addEntity()
    {
        M('sys_keyvalue')->add($_POST);
    }

    public function deleteEntity($id)
    {
        M('sys_keyvalue')->delete($id);
    }

    public function edit($id)
    {
        $this->assign('action', 'editEntity');
        $rs = M('sys_keyvalue')->where('id='.$id)->find();
        $this->assign('entity', $rs);
        $this->display('form');
    }

    public function editEntity($id)
    {
        M('sys_keyvalue')->where('id=' . $id)->save(array_filter($_POST));
    }
}