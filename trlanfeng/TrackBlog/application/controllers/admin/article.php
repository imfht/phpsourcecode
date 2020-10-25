<?php

/**
 * 文章管理
 */
class Article extends TB_Admin
{

    public function __construct()
    {
        parent::__construct();
        //加载model
        $this->load->model('article_model');
        $this->load->model('category_model');
    }

    public function index()
    {
        $this->show_list();
    }

    public function show_list($category = 0, $page = 1)
    {
        $filter = array();
        $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
        $data['catlist'] = $catList;
        $eachpage = 15;
        $data['catname'] = '所有栏目';
        if ($category != 0) {
            $cat = $this->category_model->getOne($category);
            $data['catname'] = $cat['name'];
            $filter = array(
                'cat' => $category
            );
        }
        $articleList = $this->article_model->getList($filter, $eachpage, ($page - 1) * $eachpage, "id DESC");
        for ($i = 0; $i < count($articleList); $i++) {
            $articleList[$i]['catname'] = $this->getCatnameById($articleList[$i]['cat']);
            $articleList[$i]['datetime'] = date('Y-m-d', $articleList[$i]['times']);
        }
        $data['articleList'] = $articleList;
        $this->load->view('admin/header');
        $this->load->view('admin/article_list', $data);
        $this->load->view('admin/footer');
    }

    public function add()
    {
        if (isset($_POST['submit'])) {
            if ($this->article_model->add($_POST)) {
                unset($_POST);
                $this->trackblog->showMessage('success', '数据添加成功！', '/admin/article/show_list');
            } else {
                $this->trackblog->showMessage('danger', '数据添加失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/article/show_list');
            }
        } else {
            $filter = array();
            $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
            $data['catlist'] = $catList;
            $data['name'] = "";
            $data['link'] = "";
            $data['content'] = "";
            $data['cat'] = 0;
            $data['allowcmt'] = 0;
            $data['orders'] = 0;
            $data['status'] = 1;
            $data['thumbpic'] = "";
            $data['slug'] = array();
            $data['tags'] = "";
            $data['keywords'] = "";
            $data['description'] = "";
            $data['controlType'] = "add";
            $this->load->view('admin/header');
            $this->load->view('admin/article_one', $data);
            $this->load->view('admin/footer');
        }
    }

    public function edit($id)
    {
        if (isset($_POST['submit'])) {
            if ($this->article_model->edit($id, $_POST)) {
                unset($_POST);
                $this->trackblog->showMessage('success', '数据修改成功！', '/admin/article/show_list');
            } else {
                $this->trackblog->showMessage('danger', '数据修改失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/article/show_list');
            }
        } else {
            $data = $this->article_model->getOne($id);
            $data['slug'] = explode(',',$data['slug']);
            $filter = array();
            $catList = $this->category_model->getList($filter, 0, 0, 'orders ASC');
            $data['catlist'] = $catList;
            $data['controlType'] = "edit";
            $this->load->view('admin/header');
            $this->load->view('admin/article_one', $data);
            $this->load->view('admin/footer');
        }
    }

    public function delete($id)
    {
        if ($this->article_model->delete($id)) {
            $this->trackblog->showMessage('success', '数据删除成功！', '/admin/article/show_list');
        } else {
            $this->trackblog->showMessage('danger', '数据删除失败，未知错误！如需帮助，请联系开发者。微博：@孤月蓝风', '/admin/article/show_list');
        }
    }

    public function getCatnameById($id)
    {
        $cat = $this->category_model->getOne($id);
        return $cat['name'];
    }

}

?>