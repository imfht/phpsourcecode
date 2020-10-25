<?php

// 菜单管理
class NodeController extends AdminController {

    /**
     * @var adminNodeModel
     */
    protected $model;

    public function init() {
        parent::init();
        $this->model = $this->model('adminnode');
    }

    public function indexAction() {
        $tree = new Tree($this->model);
        $list = $tree->getIconList($tree->getList(0, 'id,name,module,controller,action,ordernum,status'), 2);
        foreach ($list as &$v) {
            $v['url_edit'] = U('admin.node.edit', array('id' => $v['id']));
            $v['url_son']  = U('admin.node.add', array('pid' => $v['id']));
        }
        $this->view->list     = $list;
        $this->view->totalnum = count($this->list);
    }

    public function addAction() {
        if ($this->request->ispost()) {
            $param['name']       = $this->input->post('name', 'str', '');
            $param['pid']        = $this->input->post('pid', 'int', 0);
            $param['module']     = $this->input->post('module', 'str', '');
            $param['controller'] = $this->input->post('controller', 'str', '');
            $param['action']     = $this->input->post('action', 'str', '');
            $param['status']     = $this->input->post('status', 'int', 1);
            $param['ordernum']   = $this->input->post('ordernum', 'int', 1);
            if ($this->model->add($param)) {
                $this->success('添加成功');
            } else {
                $this->error('添加失败');
            }
        }
        $tree                   = new Tree($this->model);
        $this->view->parentlist = $tree->getIconList($tree->getList(0, 'id,name'));
    }

    public function editAction() {
        $id   = $this->input->request('id', 'int', 0);
        $info = $this->model->field('id,name,pid,module,controller,action,status,ordernum')->where(array('id' => $id))->find();
        if ($this->request->ispost()) {
            if ($info['id'] == $_POST['pid']) {
                $this->error('不能设置自己为上级节点');
            }
            $param['name']       = $this->input->post('name', 'str', '');
            $param['pid']        = $this->input->post('pid', 'int', 0);
            $param['module']     = $this->input->post('module', 'str', '');
            $param['controller'] = $this->input->post('controller', 'str', '');
            $param['action']     = $this->input->post('action', 'str', '');
            $param['status']     = $this->input->post('status', 'int', 1);
            $param['ordernum']   = $this->input->post('ordernum', 'int', 1);
            $param['id']         = $id;
            if ($this->model->edit($param)) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        }
        $tree                   = new Tree($this->model);
        $this->view->parentlist = $tree->getIconList($tree->getList(0, 'id,name'));
        $this->view->info       = $info;
    }
}