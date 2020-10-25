<?php

if(!function_exists('get_group_tpl')){
    /**
     * 用户组自定义模板
     * @param string $type 参数只能是 page 或者 member
     * @param number $groupid
     * @return string|void|string
     */
    function get_group_tpl($type='',$groupid=0){
        $groupdb = getGroupByid($groupid,false);
        if (IN_WAP===true) {
            $filename = $groupdb['wap_'.$type];
        }else{
            $filename = $groupdb['pc_'.$type];
        }
        if (strstr($filename,'/') && is_file(TEMPLATE_PATH.$filename)) {
            return TEMPLATE_PATH.$filename;
        }elseif($filename){
            return getTemplate($filename);
        }elseif($groupid){
            return getTemplate('index'.$groupid);
        }
    }    
}