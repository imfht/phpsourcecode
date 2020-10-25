<?php

namespace app\storage\controller;

use app\common\controller\ObjectM;

class Object extends ObjectM
{
    public function get(){
        $ticket = input('ticket');
        $dir = \substr($ticket,0,2);
        $file = \substr($ticket,2);
        $data = @\file_get_contents(ROOT_PATH.'data/'.$dir.'/'.$file);
        
    }

    public function post(){
        $file = input('file.file');
        $save_name = input('save_name');

        // dump($file);
        if($file){
            $info = $file->rule('md5')->move(ROOT_PATH . 'data' );
            if($info){
                $mime_type_id= model('MimeType')->getId($info->getType());
                $file_ticket = $info->hash('md5');
                $post_result['code'] = 200;
                $post_result['info']['file_ticket'] = $file_ticket;
                $build_result = $this->bulidAndSaveName($save_name,$file_ticket,$mime_type_id);
                if($build_result){
                    return $post_result;
                }else{
                    return false;
                }

            }else{
                return false;
            }
        }else{
            return $file->getError();
        }

        
    }

    public function put(){
        
    }

    public function delete(){
        
    }

    public function patch(){
        
    }

    public function chmod(){
        
    }

}