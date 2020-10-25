<?php
namespace Home\Controller;

use Think\Controller;

class MenuController extends Controller
{

    public function index()
    {
        $this->assign('apps',M('sys_app')->select());

        $model = M('sys_menu');

        if (!empty($_GET['name'])) {
            $map['sys_menu.name'] = $_GET['name'];
        }

        if (!empty($_GET['parentid'])) {
            $map['sys_menu.parentid'] = $_GET['parentid'];
        }
        if (!empty($_GET['appid'])) {
            $map['sys_menu.appid'] = $_GET['appid'];
        }

        $count = $model->join('sys_menu as a ON a.id = sys_menu.parentid')->
        where($map)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $model->field('sys_menu.*,a.name as pname,c.name as appname')
            ->join('left join sys_menu as a ON a.id = sys_menu.parentid')
            ->join('left join sys_app as c on c.id=sys_menu.appid')
            ->where($map)
            ->limit($Page->firstRow . ',' . $Page->listRows)
            ->order('orders asc')
            ->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('menuslist', M('sys_menu')->where('parentid=0')->select());
        $this->display();
    }

    public function add()
    {
        $this->assign('apps',M('sys_app')->select());
        $this->assign('action', 'addEntity');
        $this->assign('menus', M('sys_menu')->select());
        $this->display('form');
    }

    public function addEntity()
    {
        M('sys_menu')->add($_POST);
    }

    public function deleteEntity($id)
    {
        M('sys_menu')->delete($id);
    }

    public function edit($id)
    {
        $this->assign('apps',M('sys_app')->select());
        $this->assign('action', 'editEntity');
        $this->assign('menus', M('sys_menu')->select());
        $rs = M('sys_menu')->where('id=' . $id)->find();
        $this->assign('entity', $rs);
        $this->display('form');
    }

    public function editEntity($id)
    {
        M('sys_menu')->where('id=' . $id)->save(array_filter($_POST));
    }
}