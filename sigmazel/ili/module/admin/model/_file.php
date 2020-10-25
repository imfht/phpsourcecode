<?php
//版权所有(C) 2014 www.ilinei.com

namespace admin\model;

/**
 * 模板文件
 * @author sigmazel
 * @since v1.0.2
 */
class _file{
	//获取路径
	public function get_path($path){
		$path = str_replace('..', '', $path);
		$path = str_replace('//', '/', $path);
		
		$path = $path == '/' ? '' : $path;
		
		return $path;
	}
	
	//获取真实路径
	public function get_real_path($path){
		global $setting;
		
		$real_path = '';
		
		if($path && substr($path, 0, 6) == '/_DATA') $real_path = ROOTPATH.'/_cache/database'.substr($path, 6);
		elseif($path && substr($path, 0, 7) == '/_DEBUG') $real_path = ROOTPATH.'/_cache/debug'.substr($path, 7);
        elseif($path && substr($path, 0, 7) == '/_BLOCK') $real_path = ROOTPATH.'/tpl/_res/block'.substr($path, 7);
		elseif($path && is_dir(ROOTPATH."/{$setting[SiteTheme]}/{$path}")) $real_path = ROOTPATH."/{$setting[SiteTheme]}/{$path}";
		else $real_path = ROOTPATH.'/'.$setting['SiteTheme'];
		
		return $real_path;
	}
	
	//获取路径
	public function get_crumbs($path){
		$path_array = explode('/', $path);
		
		$path_crumbs = array();
		foreach ($path_array as $key => $temp_path){
			if($key > 0) $path_crumbs[] = array('path' => implode('/', array_slice($path_array, 1, $key)), 'name' => $temp_path);
		}
		
		return $path_crumbs;
	}

	//获取模板列表
    public function get_tpls(){
	    global $setting;

        $tpls = array();
        $dirs = scandir(ROOTPATH.'/tpl/');

        //读取模板列表
        foreach ($dirs as $file){
            if(is_dir(ROOTPATH.'/tpl/'.$file) && $file != '.' && $file != '..' && is_file(ROOTPATH.'/tpl/'.$file.'/_info.xml')){
                $info_xml = (array)simplexml_load_file(ROOTPATH.'/tpl/'.$file.'/_info.xml');

                if(is_array($info_xml) && $info_xml['application'] == $setting['Application']){
                    $info_xml['rel'] = $file;
                    $info_xml['theme'] = 'tpl/'.$file;
                    $tpls[] = $info_xml;
                }
            }
        }

        return $tpls;
    }
}
?>