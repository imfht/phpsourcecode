<?php
namespace app\common\util;


class Module{
    
    /**
     * 列出模块供选择
     * @return unknown[]
     */
    public static function getTitleList($title='请选择...',$type='id'){
        $array = modules_config();
        $_ar = $title ? ['0'=>$title] : [];
        foreach($array AS $rs){
            $_ar[$rs[$type]] = $rs['name'];
        }
        return $_ar;
    }
	
}