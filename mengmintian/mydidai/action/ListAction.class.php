<?php

class ListAction extends Action {

    public $model;

    public function __construct(&$tpl) {

        //正文内容
        $this->model = new ContentModel();
        $this->nav = new ColumnModel();
        $this->tpl = $tpl;
        if (isset($_GET['id'])) {
            $this->model->column_id = $_GET['id'];
            $c_list = $this->model->showList();
            $column_title = $this->nav->getNavTitle($_GET['id']);
            foreach ($c_list as $vo) {
                $vo['column_title'] = $column_title;
                $vo['time'] = date('Y-m-d H:i:s', $vo['time']);
                $list[] = $vo;
            }
            $this->tpl->assign('list', $list);
            $this->tpl->assign('page_title', $list[0]['column_title']);
        } else {
            foreach ($this->model->showAllList() as $vo) {
                $vo['column_title'] = $this->nav->getNavTitle($vo['column_id']);
                $vo['time'] = date('Y-m-d H:i:s', $vo['time']);
                $list[] = $vo;
            }
            $this->tpl->assign('list', $list);
            $this->tpl->assign('page_title', '全部列表');
        }
    }

}

