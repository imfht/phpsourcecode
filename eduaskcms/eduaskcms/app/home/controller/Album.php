<?php
namespace app\home\controller;

use app\common\controller\Home;

class Album extends Home
{
    public function initialize()
    {
        call_user_func(array('parent',__FUNCTION__)); 
    }
             
    
    public function show()
    {
        /*
        ##如果列表页也需要将每个图集的图片查询出来打开注释即可
        if (in_array($this->m, json_decode(setting('use_picture_model'), true))) {
            $this->local['contain']  = [
                'AlbumPicture' => [
                    'where' => [
                        'is_verify' => 1
                    ],
                    'order' => [
                        'list_order' => 'DESC',
                        'id' => 'ASC'
                    ]
                ]
            ];
        }*/
        call_user_func(array('parent',__FUNCTION__)); 
        if (count($this->assign->list) == 1) {
            $this->redirect($this->m.'/view', ['id'=>$this->assign->list[0]['id']]);
        }
            
    }
             
    public function view()
    {
        if (in_array($this->m, json_decode(setting('use_picture_model'), true))) {
            $this->local['contain']  = [
                'AlbumPicture' => [
                    'where' => [
                        'is_verify' => 1
                    ],
                    'order' => [
                        'list_order' => 'DESC',
                        'id' => 'ASC'
                    ]
                ]
            ];
        }
        call_user_func(array('parent',__FUNCTION__)); 
        
        $viewStyle = $this->assign->data['view_style'] ? $this->assign->data['view_style'] : 'simpleLide';
                
        switch ($viewStyle) {
            case 'simpleLide':
                $this->assign->addJs('/files/simple-lide/js/183.js');
                $this->assign->addJs('/files/simple-lide/js/simple.slide.min.js');
                $this->assign->addCss('/files/simple-lide/css/simple.slide.css');
                break;
            case 'lightGallery':
                $this->assign->addJs('/files/lightGallery/js/lightGallery.min.js');
                $this->assign->addCss('/files/lightGallery/css/lightGallery.css');
                break;
            default:
                break;
        }
    }
}
