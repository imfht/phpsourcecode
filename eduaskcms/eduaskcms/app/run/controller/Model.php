<?php
namespace app\run\controller;

use app\common\controller\Run;
use think\Loader;

class Model extends Run
{
    //初始化方法，需要调父级方法
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    //列表方法 
    public function lists()
    {
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'model',
            'cname',
            'is_menu',
            'is_dustbin',
            'created'
        );
        $this->local['order'] = array('id'=>'ASC') ;
        //$this->local['item_actions']['delete'] = false ;
        //$this->local['actions']['batch_delete'] = false;
        $this->addItemAction('数据字典', array('Model/datadict',['id'=>'id'],'parse'=>['id']), '&#xe705;');
        return call_user_func(array('parent',__FUNCTION__));
    }
    
    public function datadict()
    {
        $id = intval($this->args['id']);
        if ($id <= 0) {
            return $this->message('error', '参数：ID获取错误');
        }
        $data = $this->mdl->get($id);
        if (empty($data)) {
            return $this->message('error', 'ID为' . $id . '的模型不存在');
        }
        
        $tableName = model($data['model'])->getTable();
        $sql = "SHOW COLUMNS FROM {$tableName}";
        $list = db()->query($sql);
        $this->assign->list = $list;
        $model= $this->loadModel($data['model']);
        $form = [];
        if (isset($model->form)) {
            $form = $model->form;
        }
        $this->assign->modelForm = $form;
        
        $this->setTitle('数据字典：' . $model->cname, 'operation');
        $this->addAction("返回模型", array($this->m . '/lists'), 'fa-reply');
        $this->assign->addJs('/files/clipboard/clipboard.min.js');
        $this->assign->addJs('tableResize');
        $this->fetch = true;
    }
    
    //添加方法
    public function create()
    {     
        if($this->Form->data[$this->m]['model']) 
            $this->Form->data[$this->m]['model'] = Loader::parseName(trim($this->Form->data[$this->m]['model']),1);        
        return call_user_func(array('parent',__FUNCTION__));
    }
    
    //修改方法
    public function modify()
    {
        if($this->Form->data[$this->m]['model']) 
            $this->Form->data[$this->m]['model'] = Loader::parseName(trim($this->Form->data[$this->m]['model']),1);
        return call_user_func(array('parent',__FUNCTION__));
        return $parent_rslt ;
    } 
}