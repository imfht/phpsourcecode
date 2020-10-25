<?php
/**
* iCMS - i Content Management System
* Copyright (c) 2007-2017 iCMSdev.com. All rights reserved.
*
* @author icmsdev <master@icmsdev.com>
* @site https://www.icmsdev.com
* @licence https://www.icmsdev.com/LICENSE.html
*/
defined('iPHP') OR exit('What are you doing?');

class former_helper {
    public static function widget($name,$attr=null) {
        $widget = new iQuery($name);
        $attr && $widget->attr($attr);
        return $widget;
    }
	public static function option($data,$attr){
        if(is_array($data)){
            $flag = true;
        }else{
            $flag = false;
            $data = explode(";", $data);
        }

	    foreach ($data as $optk => $val) {
	        $val = trim($val,"\r\n");
	        if($val==='') continue;

            if($flag){
                $opt_text  = $optk;
                $opt_value = $val;
            }else{
                list($opt_text,$opt_value) = explode("=", $val);
            }

	        $opt_value===null && $opt_value = $opt_text;
            $attr2 = $attr;
            $attr2['value'] = $opt_value;
            $attr2['class'].= ' '.$attr2['id'];
            $attr2['id'].='_'.$optk;
            $option.= self::widget('label',array('for'=>$attr2['id'],'class'=>$attr['type'].'-inline'))->html($opt_text);
            $_input = self::widget('input',$attr2);
            if(former::$template['class']['input']){
                $_input->removeClass(former::$template['class']['input']);
            }
            $option.= $_input;
	    }
	    return $option;
	}
	public static function s_option($data,$name){
		is_array($data) OR $data = explode(";", $data);
	    $option = '';
        foreach ($data as $ok => $val) {
            $val = trim($val,"\r\n");
            if($val){
                list($opt_text,$opt_value) = explode("=", $val);
                $opt_value===null && $opt_value = $opt_text;
                $option.='<option value="'.$opt_value.'">'.$opt_text;
                former::$config['option'] && $option.=' ['.$name.'="'.$opt_value.'"]';
                $option.='</option>';
            }
        }
	    return $option;
	}
}
