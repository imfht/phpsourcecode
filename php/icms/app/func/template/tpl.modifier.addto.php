<?php
/**
 * template_lite strip modifier plugin
 *
 * Type:     modifier
 * Name:     addto
 */
function tpl_modifier_addto($string,$to,$flag=false)
{
    // $arg_list = func_get_args ();
    // //{'字符'|cat:'字符1':'字符2':'字符3'}
    // $pieces = array_slice($arg_list, 1);
    // return $string . implode('', $pieces);
    if($flag){
    	return $to.$string;
    }
    return $string.$to;
}

