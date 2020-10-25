<?php
namespace app\run\controller;

use app\common\controller\Run;

class AdPosition extends Run
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists()
    {
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'vari',
            'width'=>0,
            'height'=>0,
            'mobile_width'=>0,
            'mobile_height'=>0,
            'size'=>array(
				'name'=>'PC端长×宽',
				'list'=>'show',
				'show'=>"{width}×{height}"
			),
            'mobile_size'=>array(
				'name'=>'移动端长×宽',
				'list'=>'show',
				'show'=>"{mobile_width}×{mobile_height}"
			),
            'limit',
            'is_thumb',
            'ad_count'
        );
        $this->addItemAction('添加广告', array('Ad/create',['parent_id'=>'id'],'parse'=>['parent_id']), '&#xe654;');
        call_user_func(array('parent', __FUNCTION__));
        
    } 
    
    
    public function create()
    {
        $this->assignDefault('width',-1);
        $this->assignDefault('height',-1);
        $this->assignDefault('mobile_width',600);
        $this->assignDefault('mobile_height',450);
        $this->assignDefault('limit',0);
        return call_user_func(array('parent', __FUNCTION__));
    }   
         
}
