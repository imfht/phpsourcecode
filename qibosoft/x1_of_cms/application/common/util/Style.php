<?php
namespace app\common\util;


class Style{
    
    /**
     * 列出网站前台所有风格
     * @return unknown[]
     */
    public static function listStyle(){
        return static::get_style('index');
    }
    
    /**
     * 网站风格,可以是前台或后台或会员中心
     * @param unknown $type 可以index admin member
     * @return unknown[]
     */
    public static function get_style($type=''){
        $style_db = [];
        if (!is_dir(TEMPLATE_PATH.$type.'_style')) {
            return $style_db;
        }
        $dir = opendir(TEMPLATE_PATH.$type.'_style');
        while (($file=readdir($dir))!==false) {
            $path = TEMPLATE_PATH.$type.'_style/'.$file.'/info.php';
            if($file!='.'&&$file!='..'&&is_file($path)){
                if(check_bom($path,true)){
                    write_file($path, check_bom($path));
                }
                $ar = include $path;
                $style_db[str_replace('.php', '', basename($file))] = $ar['name'];
            }
        }
        return $style_db;
    }
    
    
    public static function select_indexstyle_template($type='layout',$ctype='wap',$form_name=''){
        $show = '<div>请选择:';
        $array = self::get_indexstyle_template($type,$ctype);
        foreach($array AS $path=>$name){
            $show .= '<span style="border:1px solid orange;padding:3px;margin-right:10px;cursor:pointer;" onclick=\'$("#atc_'.$form_name.'").val("'.$path.'")\'>'.$name.'</span>';
        }
        $show .= '</div>';
        return $show;
    }
    
    /**
     * 主要是给频道设置个性模板用
     * @param string $type
     * @param string $ctype
     * @return unknown[]
     */
    public static function get_indexstyle_template($type='layout',$ctype='wap'){
        $style_db = [];
        $dir = opendir(TEMPLATE_PATH.'index_style');
        while (($file=readdir($dir))!==false) {
            $path = TEMPLATE_PATH.'index_style/'.$file.'/info.php';
            if($file!='.'&&$file!='..'&&is_file($path)){
                if(check_bom($path,true)){
                    write_file($path, check_bom($path));
                }
                $ar = include $path;
                $style_file = '';
                if ($ctype=='wap') {
                    if(in_array($type, ['index','layout'])){
                        $style_file = self::check_file($file,'index',$type,'wap');                        
                    }elseif(in_array($type, ['list','show'])){
                        $style_file = self::check_file($file,config('system_dirname').'/content',$type,'wap');                        
                    }
                }else{
                    if(in_array($type, ['index','layout'])){
                        $style_file = self::check_file($file,'index',$type,'pc');
                    }elseif(in_array($type, ['list','show'])){
                        $style_file = self::check_file($file,config('system_dirname').'/content',$type,'pc');
                    }
                }
                if ($style_file) {
                    $style_db[$style_file] = $ar['name'];
                }                
            }
        }
        return $style_db;
    }
    
    protected static function check_file($style_name='default',$path='index',$filename='show',$type='wap'){
        if(is_file(TEMPLATE_PATH.'index_style/'.$style_name.'/'.$path.'/'.$type.'_'.$filename.'.'.config('template.view_suffix'))){
            return $style_name.'/'.$path.'/'.$type.'_'.$filename.'.'.config('template.view_suffix');
        }elseif(is_file(TEMPLATE_PATH.'index_style/'.$style_name.'/'.$path.'/'.$filename.'.'.config('template.view_suffix'))){
            return $style_name.'/'.$path.'/'.$filename.'.'.config('template.view_suffix');
        }
    }
    
    /**
     * 会员中心风格
     * @return string[]
     */
    public static function listMemberStyle(){
        return [
                        'default'=>'会员中心X1.0默认风格',
                ];
    }
	
}