<?php
namespace app\run\controller;

use app\common\controller\Run;

class Setting extends Run
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__));
    }
    
    public function lists()
    {
        if (!$this->local['filter']) {
            $this->local['filter'] = [
                'title',
                'vari',
                'value'
            ];
        }
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'setting_group_id',
            'title',
            'value'=>'call.show_value',
            'type',
            'vari',
            'options'=>0
        );
        $this->addItemAction('set');
        $this->local['order'] = array('id' => 'ASC');
        if (!config('app_debug')) {
            $this->local['item_actions']['modify'] = false;
            $this->local['item_actions']['delete'] = false;
            $this->local['actions']['batch_delete'] = false;
        }
        
        $parent_rslt  =  call_user_func(array('parent', __FUNCTION__));
        return $parent_rslt ;
    }
    
    public function set()
    {
        config('app_trace', false);
        $id=$this->args['id'];
		if (empty($id)) {
            return $this->_message('error','缺少参数:ID');
		}
        $data   = $this->mdl->find($id);
        if (empty($data)) {
            return $this->_message('error','需要设置的数据不存在');
        }
        $data = $data->getAssocData();
        
        if ($this->request->isPost()) {
            $this->Form->data[$this->m]['id']=$id;
            $this->Form->data[$this->m]['type'] = $data['type'] ;
            $this->mdl->data($this->Form->data[$this->m]); 
            $this->mdl->is_validate = false ;
            $result  = $this->mdl->isUpdate(true)->save();
            $this->mdl->write_cache() ;
            return $this->fetch = 'set_success';
            
        } else {
            $this->Form->data[$this->m]   =   $data ;
            $this->assign->data = $this->Form->data;
        }
        $this->assign->addJs('artTemplate.js');
		$this->assign->addJs('json2.js');
        $this->assign->addCss('/files/colorpicker/css/colorpicker.css');
        $this->assign->addJs('/files/colorpicker/js/colorpicker.js');
        
        return $this->fetch = true;
    }
    
    
    
}
