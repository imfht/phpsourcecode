<?php
namespace app\file\controller;

use think\Controller;

/**
 * 接收基本路径
 * 对路径操作
 * 以"/"结尾即操作目录
 * 否则最后一个为文件名
 * @var mixed
 */
class Index extends Controller
{

    protected $beforeActionList = [
        
        'checkBaseDir',
    ];

    public $base_dir = './data/';

    public $file_path = './data';

    public $file_name = '';

    public $response = [];

    /**
     * list
     * 接收额外参数
     * l:显示详情
     * h:格式化文件大小显示
     * 
     * @return mixed 
     */
    public function list()
    {

        if(is_dir($this->base_dir)){
            
            $this->response['code'] = 200;
            $this->response['msg'] = '';

            $dir_list = scandir($this->base_dir);
            array_splice($dir_list, 0, 2);
            $is_for_detail = input('post.l',false);

            if($is_for_detail == "true" && !empty($dir_list))
            {
                foreach ($dir_list as $d_key => $d_value) {
                    $temp_path = $this->base_dir.'/'.$d_value;
                    $detail_dir_list[$d_value]['name'] = $d_value;
                    $detail_dir_list[$d_value]['is_writeable'] = is_writeable($temp_path);
                    $detail_dir_list[$d_value]['is_executable'] = is_executable($temp_path);
                    $detail_dir_list[$d_value]['is_readable'] = is_readable($temp_path);
                    $detail_dir_list[$d_value]['realpath'] = realpath($temp_path);
                    $detail_dir_list[$d_value]['url'] = web_url($temp_path);
                    if(is_dir($this->base_dir.$d_value)){
                        $detail_dir_list[$d_value]['is_dir'] = true;
                        $detail_dir_list[$d_value]['size'] = 0;
                    }else{
                        $detail_dir_list[$d_value]['is_dir'] = false;
                        $file_size = filesize($temp_path);
                        $detail_dir_list[$d_value]['size'] = (input('post.h',false) == 'true') ? getFilesize($file_size) : $file_size ;
                    }
                }
                $this->response['data'] = $detail_dir_list;
            }else{
                $this->response['data'] = $dir_list;
            }

        }else{
            $this->response['code'] = 500;
            $this->response['msg'] = 'not a dir';
            
        }
        return json($this->response);
    }

    public function upload()
    {
        
        if($this->file_name != ''){
    
            if(is_dir($this->base_dir)){
                $this->response['code'] = 500;
                $this->response['msg'] = '已存在同名目录,不能上传文件';
            }else{
                
                $file = request()->file('file');
                
                if(is_array($file)){
                    $this->response['code'] = 500;
                    $this->response['msg'] = '只能上传一个文件';
        
                }else{
                    
                    $info = $file->move($this->file_path,$this->file_name,true);
                    if($info){
                        // 成功上传后 获取上传信息
                        $this->response['code'] = 200;
                        $this->response['msg'] = '上传文件成功';
            
                        $this->response['data']['getExtension'] = $info->getExtension();
                        
                        $this->response['data']['getSaveName'] = $info->getSaveName();
                        
                        $this->response['data']['getFilename'] = $info->getFilename(); 

                        $this->response['data']['url'] = \web_url($this->base_dir);
                    }else{
                        // 上传失败获取错误信息
                        $this->response['code'] = 500;
                        $this->response['data']['error'] = $file->getError();
                    }
                }
            }
        }else{
            if(file_exists($this->file_path)){
                $this->response['code'] = 500;
                $this->response['msg'] = '已存在同名文件,不能创建目录';
            }else{
                $this->response['code'] = 200;
                $this->response['msg'] = '创建目录成功';
                directory($this->file_path,0777,true);
            }
        }
        return json($this->response);
    }

    /**
     * delete
     * 接收额外参数
     * rm:强制删除目录
     * @return mixed 
     */
    public function delete()
    {
        
        if($this->file_name != ''){
            if(file_exists($this->base_dir)){
    
                $stauts = unlink($this->base_dir);
        
                if($stauts){
                    $this->response['code'] = 200;
                    $this->response['msg'] = '删除成功';
                }else{
                    $this->response['code'] = 500;
                    $this->response['msg'] = '删除异常';
                }
            }else{
                $this->response['code'] = 200;
                $this->response['msg'] = '文件不存在';
            }
        }else{
            if(is_dir($this->base_dir)){
                $dir_file_list = scandir($this->base_dir);
                array_splice($dir_file_list, 0, 2);
                if(count($dir_file_list)){
                    if(input('post.rf',false)){
                        if(delTree($this->base_dir)){
                            $this->response['code'] = 200;
                            $this->response['msg'] = '删除目录成功,目录下文件已清空';    
                        }else{
                            $this->response['code'] = 500;
                            $this->response['msg'] = '递归删除目录异常';
                        }
                    }else{
                        $this->response['code'] = 500;
                        $this->response['msg'] = '目录不为空,强制删除并清空请增加rf参数true';
                    }
                }else{
                    if(rmdir($this->base_dir)){
                        $this->response['code'] = 200;
                        $this->response['msg'] = '删除目录成功';
                    }else{
                        $this->response['code'] = 500;
                        $this->response['msg'] = '删除目录异常';
                    }
                    
                }
            }else{
                $this->response['code'] = 500;
                $this->response['msg'] = '目录不存在';
            }
        }
        
        return json($this->response);
    }

    /**
     * info
     * 接收额外参数
     * ra:递归读取目录下所有文件和目录
     * h:文件大小格式化
     * @return mixed 
     */
    public function info()
    {
        if(is_dir($this->base_dir)){
            $this->response['code'] = 200;
            $this->response['msg'] = '';
            $this->response['data']['disk_free_space'] = disk_free_space($this->base_dir);
            $this->response['data']['disk_free_space'] = (input('post.h',false) == 'true') ? getFilesize($this->response['data']['disk_free_space']) : $this->response['data']['disk_free_space'];
            $this->response['data']['disk_total_space'] = disk_total_space($this->base_dir);
            $this->response['data']['disk_total_space'] = (input('post.h',false) == 'true') ? getFilesize($this->response['data']['disk_total_space']) : $this->response['data']['disk_total_space'];
            $this->response['data']['files'] = countFiles($this->base_dir);
            $this->response['data']['all_files'] = (input('post.ra') == 'true')?\read_all($this->base_dir):['file_count'=>0,'dir_count'=>0,'count'=>0,];
            $this->response['data']['is_writeable'] = is_writeable($this->base_dir);
            $this->response['data']['is_executable'] = is_executable($this->base_dir);
            $this->response['data']['is_readable'] = is_readable($this->base_dir);
            $this->response['data']['realpath'] = realpath($this->base_dir);
            $this->response['data']['url'] = web_url($this->base_dir);
        }else{
            if(file_exists($this->base_dir)){
                $this->response['code'] = 500;
                $this->response['msg'] = "";
                $this->response['data']['is_writeable'] = is_writeable($this->base_dir);
                $this->response['data']['is_executable'] = is_executable($this->base_dir);
                $this->response['data']['is_readable'] = is_readable($this->base_dir);
                $this->response['data']['realpath'] = realpath($this->base_dir);
                $this->response['data']['url'] = web_url($this->base_dir);
            }else{
                $this->response['code'] = 500;
                $this->response['msg'] = "文件不存在";
            }
        }
        return json($this->response);
    }

    protected function checkBaseDir()
    {
        $base_dir = input("post.base_dir");
        if(!$base_dir){
            $file_path = \request()->url();   
            $path_info = explode('/',$file_path);
            $storge_path = './data';
            $save_name = array_pop($path_info);
            array_splice($path_info,0,2);
            foreach ($path_info as $p_key => $p_value) {
                if(!empty($p_value)){
                    $storge_path .= '/'.$p_value;
                }
            }
            $this->file_name = $save_name;
            $this->file_path = $storge_path;
            $this->base_dir = $storge_path.'/'.$save_name;
        }else{
            // echo $base_dir;
            $is_up_dir_start = preg_match('/^\.\.\/[\w\n].*/',$base_dir);
            if($is_up_dir_start){
                $this->base_dir = str_replace('../','./data/',$base_dir);
            }
            $is_char_start = \preg_match('/^[\w\n].*/',$base_dir);
            if($is_char_start){
                $this->base_dir .= $base_dir;
            }
            $is_root_start = preg_match('/^\/[\w\n].*/',$base_dir);
            if($is_root_start){
                $this->base_dir = './data'.$base_dir;
            }
            $is_point_dir_start = preg_match('/^\.\/[\w\n].*/',$base_dir);
            if($is_point_dir_start){
                $this->base_dir = str_replace('./','./data/',$base_dir);
            }
            $storge_path = '';
            $path_info = \explode('/',$this->base_dir);
            $this->file_name = array_pop($path_info);
            array_splice($path_info,0,1);
            foreach ($path_info as $p_key => $p_value) {
                if(!empty($p_value)){
                    $storge_path .= '/'.$p_value;
                }
            }
            $this->file_path = $storge_path;
            
            // echo $this->base_dir;
        }
        $this->response['file_path'] = $this->file_path;
        $this->response['file_name'] = $this->file_name;
        $this->response['base_dir'] = $this->base_dir;
    }

}
