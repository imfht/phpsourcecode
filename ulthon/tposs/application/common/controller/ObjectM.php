<?php
namespace app\common\controller;

use app\common\controller\LoadModule;

/**
 * 无论是本地还是集群模块的storage模块，
 * 本质都一样，
 * 上传一张图片，
 * 返回一个图片唯一值，
 * 根据图片唯一值获取图片
 * 
 * 上传时包含目录，
 * 真实件环境中，但也可以通过上传目录访问
 * 都必须有的方法
 * @var mixed
 */
abstract class ObjectM extends LoadModule
{

    public function index(){
        $this->get();
    }

    abstract function get();

    abstract function post();

    abstract function put();

    abstract function delete();

    abstract function patch();

    abstract function chmod();

    public function bulidAndSaveName($save_name,$file_ticket,$mime_type_id){
        if(\checkSaveName($save_name)){
            $dir = model('Dir');
            $name_list = \explode('/',$save_name);
            $size = \sizeof($name_list);
            $temp_dir_id = 0;
            for ($i=0; $i < $size-1; $i++) { 
                $dir_name_id = model('Name')->getId($name_list[$i]);
                $temp_dir_id = $dir->setDir($dir_name_id,$temp_dir_id);
                if(!$temp_dir_id){
                    return false;
                }
            }
            $file_name_id = model('Name')->getId($name_list[$size-1]);
            $add_result = model('File')->addFile($file_name_id,$temp_dir_id,$file_ticket,$mime_type_id);
            if($add_result){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    
}