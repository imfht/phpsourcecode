<?php
class GroupController extends AdminController{

    /**
     * @var AdminGroupModel;
     */
    protected $model;
    public function init() {
        parent::init();
        $this->model=$this->model('admingroup');
    }
    public function indexAction() {
        $this->view->list=$this->model->order('id asc')->getlist();
    }

    public function addAction() {
        if ($this->request->ispost()){
            $param['name']=$this->input->post('name','str');
            $param['intro']=$this->input->post('intro','str');
            $param['node']=implode(',',$this->model('adminnode')->toNodeAuth($this->input->post('node','arr',array())));
            $param['create_user_id']=$_SESSION['admin']['userid'];
            $param['create_time']=NOW_TIME;
            if($this->model->add($param)){
                $this->success('添加成功',U('index'));
            }else{
                $this->error('添加失败');
            }
        }
        $tree=new Tree($this->db('admin_node'));
        $this->view->menu=$tree->getAuthList(0,'id,name');
    }

    public function editAction() {
        $id=$this->input->request('id','int',0);
        $info=$this->model->field('id,name,node,intro')->where(array('id'=>$id))->find();
        if ($this->request->ispost()){
            $param['name']=$this->input->post('name','str');
            $param['intro']=$this->input->post('intro','str');
            $param['node']=implode(',',$this->model('adminnode')->toNodeAuth($this->input->post('node','arr',array())));
            $param['update_user_id']=$_SESSION['admin']['userid'];
            $param['update_time']=NOW_TIME;
            $param['id']=$id;
            if ($this->model->edit($param)){
                $this->success('修改成功',U('index'));
            }else{
                $this->error('修改失败');
            }
        }
        $info['node']=explode(',',$info['node']);
        $tree=new Tree($this->db('admin_node'));
        $this->view->menu=$tree->getAuthList(0,'id,name');
        $this->view->info=$info;
    }

}