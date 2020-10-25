<?php

class manageController extends AdminController {

    /**
     * @var PageModel
     */
    protected $model;
    public function init() {
        parent::init();
        $this->model=$this->model('page');
    }
    public function indexAction() {
        $this->view->list=$this->model->order('ordernum asc,id desc')->getlist();
    }

    public function addAction() {
        if($this->request->ispost()){
            $param['name']=$this->input->post('name','str','');
            $param['key']=$this->input->post('key','en','');
            $param['litpic']=$this->input->post('litpic','str','');
            $param['ordernum']=$this->input->post('ordernum','int','');
            $param['type']=$this->input->post('type','int',1);
            $param['status']=$this->input->post('status','int',1);
            $param['content']=($param['type']==1)?text::simditor($this->input->post('content','str','')):$this->input->post('htmlcontent','str','');
            if($this->model->add($param)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }
    }

    public function editAction() {
        $id=$this->input->request('id','int',0);
        $info=$this->model->where(array('id'=>$id))->find();
        if ($this->request->ispost()){
            $param['name']=$this->input->post('name','str','');
            $param['key']=$this->input->post('key','en','');
            $param['litpic']=$this->input->post('litpic','str','');
            $param['ordernum']=$this->input->post('ordernum','int','');
            $param['type']=$this->input->post('type','int',1);
            $param['status']=$this->input->post('status','int',1);
            $param['content']=($param['type']==1)?text::simditor($this->input->post('content','str','')):$this->input->post('htmlcontent','str','');
            $param['id']=$id;
            if ($this->model->edit($param)){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }
        $this->view->info=$info;
    }
}