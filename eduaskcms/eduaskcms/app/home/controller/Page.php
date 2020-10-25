<?php
namespace app\home\controller;

use app\common\controller\Home;

class Page extends Home
{
    public function initialize()
    {
        
        call_user_func(array('parent', __FUNCTION__)); 
    }
    
    public function show()
    {
        $menu_id = intval($this->args['menu_id']);
        if (empty($menu_id)) {
            return $this->notFound();
        }
			
        $this->local['where']['menu_id'] =   ['menu_id', '=', $menu_id]; 
        $this->local['order'] = array('id'=>'ASC' );
        $this->local['limit'] =  1 ;
        call_user_func(array('parent', __FUNCTION__)); 
        if (empty($this->assign->list)) {
             return $this->notFound();
        }
        $this->redirect($this->redirect($this->m.'/view', ['id' => $this->assign->list[0]['id']]));
    }
    
    public function view(){
        
        call_user_func(array('parent', __FUNCTION__)); 
        if ($this->assign->meta['title'][0] == $this->assign->meta['title'][1]) {
            unset($this->assign->meta['title'][0]);
        }
    }
}
