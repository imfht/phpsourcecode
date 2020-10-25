<?php
namespace app\common\fun;


class Img{
    /**
     * 缩略图
     * @param string $src
     * @param number $width
     * @param number $height
     * @return string
     */
	function TT($src='',$width=400,$height=300){
	    if (is_file(ROOT_PATH.'_tim.php')) {
	        return request()->domain().'/_tim.php?src='.urlencode($src).'&w='.$width.'&h='.$height;
	    }else{
	        return $src;
	    }		
	}
}