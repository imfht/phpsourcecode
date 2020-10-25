<?php
class forum_cateAction extends backendAction
{
    public function _initialize()
    {
        parent::_initialize();
        $this->_mod = D('forum_cate');
    }

     

 

    public function ajax_check_name()
    {
        $name = $this->_get('name', 'trim');
        $id = $this->_get('id', 'intval');
        if ($this->_mod->name_exists($name, $id)) {
            $this->ajaxReturn(0, '链接名称已经存在');
        } else {
            $this->ajaxReturn();
        }
    }
 
}