<?php
namespace app\home\controller;

use app\common\controller\Home;

class Article extends Home
{
    public function initialize()
    {
        
        call_user_func(array('parent',__FUNCTION__)); 
    }
    
    public function show()
    {
        /*
        ##如果列表页也需要将每个文章的关联图片查询出来打开注释即可
        if (in_array($this->m, json_decode(setting('use_picture_model'), true))) {
            $this->local['contain']  = [
                'ArticlePicture' => [
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
    }
             
    public function view()
    {
        if (in_array($this->m, json_decode(setting('use_picture_model'), true))) {
            $this->local['contain']  = [
                'ArticlePicture' => [
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
    }        
}
