<?php
namespace Home\Controller;

use Think\Controller;
use Think\Exception;

class AppController extends Controller
{

    public function index()
    {

        $model = M('sys_app');

        $count = $model->where(array_filter($_GET))->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $model->where(array_filter($_GET))->limit($Page->firstRow . ',' . $Page->listRows)->select();
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
        $model=M('sys_app');
        $con['name']=$_POST['name'];

        if($model->where($con)->count()>0) {
            iconvEcho('应用名称不可重复');
            return;
        }
        M('sys_app')->add($_REQUEST);
    }

    public function deleteEntity($id)
    {
        $rs=M('sys_app')->delete($id);

    }

    public function edit($id)
    {
        $this->assign('action', 'editEntity');
        $rs = M('sys_app')->where('id=' . $id)->find();
        $this->assign('entity', $rs);
        $this->display('form');
    }

    public function editEntity($id)
    {
        $model=M('sys_app');
        $con['name']=$_POST['name'];

        if($model->where($con)->count()>1) {
            iconvEcho('应用名称不可重复');
            return;
        }
        M('sys_app')->where('id=' . $id)->save(array_filter($_POST));
    }
}