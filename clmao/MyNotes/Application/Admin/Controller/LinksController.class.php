<?php

namespace Admin\Controller;

use Think\Controller;

class LinksController extends CommonController {

    //添加链接视图
    public function addLinks() {
        $this->title = '添加链接';
        $this->display();
    }

    //添加链接的操作
    public function addLinksProcess() {
        $arr = $_POST;
        S('links', null);
        if (M('links')->add($arr)) {
            $this->success('添加成功，可继续添加', U('Admin/Links/addLinks'), 2);
        }
    }

    //链接列表
    public function listLinks() {
        $this->title = '所有链接';
        $data = D('Common/Page')->getPage('links');
        $this->assign('list', $data['list']); // 赋值数据集
        $this->assign('page', $data['page']); // 赋值分页输出
        $this->display();
    }

    //删除链接
    public function delLinksProcess() {
        if (M('links')->delete(I('get.id', 0, 'intval'))) {
            $this->success('删除成功', '', 1);
        }
    }

    //编辑链接的视图
    public function editLinks() {
        $this->title = '编辑链接';
        $id = I('get.id') + 0;
        $data = M('links')->find($id);
        $this->assign('links', $data);

        $this->display();
    }

    //编辑链接的操作
    public function editLinksProcess() {
        S('links', null);
        $data = $_POST;
        if (M('links')->save($data)) {

            $this->success('保存成功', U('Admin/Links/editLinks', array('id' => $data['id'])));
        }
        $this->redirect('/Admin/Links/listLinks');
    }

}
