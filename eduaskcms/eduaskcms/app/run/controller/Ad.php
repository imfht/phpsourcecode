<?php
namespace app\run\controller;

use app\common\controller\Run;

class Ad extends Run
{
    public function initialize(){
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function lists()
    {
        if(!$this->local['list_fields'])
        $this->local['list_fields'] = array(
            'title',
            'image',
            'mobile_image',
            'is_verify',
            'created',
            'list_order'
        );
        $this->local['sortable'] = true ;
        call_user_func(array('parent',__FUNCTION__));
    } 
    
    
    public function create()
    {
        $this->getAdPosition();
        return call_user_func(array('parent',__FUNCTION__));
    } 
    
    public function modify()
    {
        $ad_position_id = $this->mdl->where(['id' => intval($this->args['id'])])->value('ad_position_id');
        $this->getAdPosition($ad_position_id);
        return call_user_func(array('parent',__FUNCTION__));
    } 
    
    protected function getAdPosition($ad_position_id = null)
    {
        if (!$ad_position_id) {
            $ad_position_id = $this->args['parent_id'];
        }
        if (!$ad_position_id) {
            $ad_position_id = $this->args['ad_position_id'];
        }
        if (!$ad_position_id) {
            return $this->message('error', '缺少广告位ID');
        }        
        $this->loadModel('AdPosition');
        $pos_data  = $this->AdPosition->get($ad_position_id);
        $pos_data = $pos_data->getArray();
        
        if ($pos_data['is_text']) {
            $this->mdl->form['content']['elem'] = 'textarea';
        }
        
        if ($pos_data['is_thumb']) {
            $this->mdl->form['image']['image']['thumb'] = array(
                'field' => 'thumb',
                'width' => $pos_data['width'],
                'height' => $pos_data['height'],
                'method' => 3
            );
            
            $this->mdl->form['mobile_image']['image']['thumb'] = array(
                'field' => 'mobile_thumb',
                'width' => $pos_data['mobile_width'],
                'height' => $pos_data['mobile_height'],
                'method' => 3
            );
        } else {
            $this->mdl->form['image']['image']['resize'] = array(
                'width' => $pos_data['width'],
                'height' => $pos_data['height'],
                'method' => 3
            );
            
            $this->mdl->form['mobile_image']['image']['resize'] = array(
                'width' => $pos_data['mobile_width'],
                'height' => $pos_data['mobile_height'],
                'method' => 3
            );
        }
    }
    
}
