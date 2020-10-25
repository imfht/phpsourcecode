<?php
function smarty_modifier_strcut($str, $start=0, $length, $suffix=false){
    $slice=msubstr($str,$start,$length);
    if($suffix && $slice != $str){
		return $slice."...";
	}
    return $slice;
}
?>