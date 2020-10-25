<?php

/**
 * 栏目管理
 */
class Tag extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('category_model');
        $this->load->model('article_model');
    }

    public function index($tag)
    {
        $this->showlist($tag);
    }

    public function showlist($tag, $page = 1)
    {
        $tag = urldecode($tag);
        $data['cat'] = 0;
        $data['tagname'] = $tag;
        $data['uppage'] = $page - 1;
        if ($data['uppage'] <= 0)
        {
            $data['uppage'] = 1;
        }
        $data['downpage'] = $page + 1;
        $filter = array();
        $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
        $data['catlist'] = $catList;
        $articleList= $this->article_model->getListByTag($tag,10,($page - 1) * 10,"id DESC");
        foreach ($articleList as $article) {
            $article['datetime'] = date('Y-m-d' ,$article['times']);
            $article['catname'] = $this->getCatnameById($article['cat'])['name'];
            $article['catnickname'] = $this->getCatnameById($article['cat'])['nickname'];
            $article['tagarray'] = array();
            $tagarray = explode(',', $article['tags']);
            foreach ($tagarray as $tag) {
                $tagOne['name'] = $tag;
                $tagOne['url'] = '/tag/'.$tag;
                $article['tagarray'][] = $tagOne;
            }
            $data['articleList'][] = $article;
        }
        $data['contentType'] = "tag";
        $this->load->view('templates/'.TEMPLATES.'/header', $data);
        $this->load->view('templates/'.TEMPLATES.'/tag', $data);
        $this->load->view('templates/'.TEMPLATES.'/footer');
    }

    public function getCatnameById($id)
    {
        return $cat = $this->category_model->getOne($id);
    }
}

?>