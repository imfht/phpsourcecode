<?php

class ManageController extends AdminController {

    /**
     * @var AttachmentModel
     */
    protected $model;

    public function init() {
        $this->model = $this->model('attachment');
        $this->config->set('storage_path', $this->config->get('upload_path', 'uploads'));
        parent::init();
    }

    public function indexAction() {
        $where          = $this->_parsemap();
        $this->page     = $this->input->request('page', 'int', 1);
        $this->pagesize = $this->config->get('admin_pagesie', 20);
        $this->list     = $this->model->where($where)->page($this->page)->limit($this->pagesize)->getlist();
        $this->totalnum = $this->model->where($where)->count();
        $this->pagenum  = ceil($this->totalnum / $this->pagesize);
        if ($this->request->isAjax()) {
            $this->ajax(array('data' => $this->list, 'totalnum' => $this->totalnum, 'pagenum' => $this->pagenum));
        } else {
            $this->display();
        }

    }
}