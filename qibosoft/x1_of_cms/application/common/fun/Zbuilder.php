<?php
namespace app\common\fun;

/**
 * Zbuilder表单器
 */
class Zbuilder{
    
    /**
     * 获取附件路径
     * @param int $id 附件id
     * @return string
     */
    public function get_file_path($id=0){
        if(strstr($id,'uploads/')){
            if(!is_numeric($id)){
                return PUBLIC_URL.$id;
            }
            $path=model('admin/attachment')->getFilePath($id);
            if(!$path){
                return '/public/static/admin/img/none.png';
            }
            return $path;
        }else{
            return $id;
        }
    }
    
    /**
     * 获取图片缩略图路径
     * @param int $id 附件id
     * @return string
     */
    public function get_thumb($id=0){
        if(strstr($id,'uploads/')){
            if(!is_numeric($id)){
                return PUBLIC_URL.$id;
            }
            $path=model('admin/attachment')->getThumbPath($id);
            if(!$path){
                return '/public/static/admin/img/none.png';
            }
            return $path;
        }else{
            return $id;
        }
    }
    
    public function parse_name($name, $type = 0) 
    {
    }
    
    
    public function format_date($time = null, $format='yyyy-mm-dd') {
    }
    

    public function load_assets($assets = '', $type = 'css')
    {
    }
    

    public function format_linkage($data = [])
    {
    }
    

    public function get_level_key_data($table = '', $id = '', $id_field = 'id', $name_field = 'name', $pid_field = 'pid', $level = 1)
    {
    }
    

    public function get_level_pid($table = '', $id = 1, $id_field = 'id', $pid_field = 'pid')
    {
    }
    

    public function get_level_data($table = '', $pid = 0, $pid_field = 'pid')
    {
    }
    

    public function format_moment($time = null, $format='YYYY-MM-DD HH:mm')
    {
    }
    
}