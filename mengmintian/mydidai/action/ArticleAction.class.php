<?php

class ArticleAction extends Action {

    public $tpl;
    public $model;

    public function __construct(&$tpl) {

        //正文内容
        $this->model = new ContentModel();
        $this->nav = new ColumnModel();
        $this->tpl = $tpl;
        $this->model->id = $_GET['id'];
        $one = $this->model->oneContent();
        $one[0]['column_title'] = $this->nav->getNavTitle($one[0]['column_id']);
        $one[0]['time'] = date('Y-m-d H:i:s', $one[0]['time']);
        $this->tpl->assign('column_title', $one[0]['column_title']);
        $this->tpl->assign('title', $one[0]['title']);
        $this->tpl->assign('one', $one);

        //下一篇文章
        $next = $this->model->showNextList($one[0]['column_id']);
        $this->tpl->assign('next_id', $next[0]['id']);
        $this->tpl->assign('next_title', $next[0]['title']);

        //上一篇文章
        $prev = $this->model->showPrevList($one[0]['column_id']);
        $this->tpl->assign('prev_id', $prev[0]['id']);
        $this->tpl->assign('prev_title', $prev[0]['title']);

        //热点推荐
        $hot = $this->model->showHotList($one[0]['column_id']);
        $this->tpl->assign('hot', $hot);

        //相关推荐
        $rec = $this->model->showRecommendList($one[0]['column_id']);
        $this->tpl->assign('rec', $rec);

        //导航栏
        $this->nav = new ColumnModel();
        $this->tpl->assign('nav', $this->nav->showNav());
    }

}

?>