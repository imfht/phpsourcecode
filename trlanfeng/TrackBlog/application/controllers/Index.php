<?php
/**
 * 首页
 */
class Index extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('category_model');
        $this->load->model('article_model');
    }
    public function index($page = 1)
    {
        $data['cat'] = 0;
        $data['uppage'] = $page - 1;
        if ($data['uppage'] <= 0)
        {
            $data['uppage'] = 1;
        }
        $data['downpage'] = $page + 1;
        $filter = array();
        $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
        $data['catlist'] = $catList;
        $articleList= $this->article_model->getList($filter,10,0,"id DESC");
        foreach ($articleList as $article) {
            $article['datetime'] = date('Y-m-d' ,$article['times']);
            $article['catname'] = $this->getCatnameById($article['cat'])['name'];
            $article['catnickname'] = $this->getCatnameById($article['cat'])['nickname'];
            $article['tagarray'] = explode(',', $article['tags']);;
            $data['articleList'][] = $article;
        }
        $filter = array(
            "cat"=>13
        );
        $works = $this->article_model->getList($filter,4,0,"id DESC");
        $data['works'] = $works;
        $data['contentType'] = "index";
        $this->load->view('templates/'.TEMPLATES.'/header',$data);
        $this->load->view('templates/'.TEMPLATES.'/index',$data);
        $this->load->view('templates/'.TEMPLATES.'/footer');
    }
    public function getCatnameById($id)
    {
        return $cat = $this->category_model->getOne($id);
    }
}
?>