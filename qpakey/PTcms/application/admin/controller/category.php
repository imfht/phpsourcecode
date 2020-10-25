<?php
class categoryController extends adminController{

    /**
     * @var categoryModel
     */
    protected $model;
    public function init() {
        $this->model=$this->model('category');
        parent::init();
    }

    public function indexAction() {
        $tree=new Tree($this->model);
        $list=$tree->getIconList($tree->getList(0,'id,name,ordernum,status,key'),2);
        foreach($list as &$v){
            $v['url_edit']=U('admin.category.edit',array('id'=>$v['id']));
            $v['url_son']=U('admin.category.add',array('pid'=>$v['id']));
        }
        $this->view->list=$list;
        $this->view->set('totalnum', count($this->list));
    }

    public function addAction() {
        if($this->request->ispost()){
            $param['name']=$this->input->post('name','str','');
            $param['key']=$this->input->post('key','en','');
            $param['pid']=$this->input->post('pid','int',0);
            $param['status']=$this->input->post('status','int',1);
            $param['ordernum']=$this->input->post('ordernum','int',1);
            if($this->model->add($param)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }
        $tree=new Tree($this->model);
        $this->view->parentlist=$tree->getIconList($tree->getList(0,'id,name'));
    }

    public function editAction() {
        $id=$this->input->request('id','int',0);
        $info=$this->model->where(array('id'=>$id))->find();
        if ($this->request->ispost()){
            if ($info['id']==$_POST['pid']){
                $this->error('不能设置自己为上级分类');
            }
            $param['name']=$this->input->post('name','str','');
            $param['key']=$this->input->post('key','en','');
            $param['pid']=$this->input->post('pid','int',0);
            $param['status']=$this->input->post('status','int',1);
            $param['ordernum']=$this->input->post('ordernum','int',1);
            $param['id']=$id;
            if ($this->model->edit($param)){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }
        $tree=new Tree($this->model);
        $this->view->parentlist=$tree->getIconList($tree->getList(0,'id,name'));
        $this->view->info=$info;
    }
}