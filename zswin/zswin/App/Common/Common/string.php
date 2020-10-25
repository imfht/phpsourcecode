<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2014-2020 http://zswin.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zswin.cn
// +----------------------------------------------------------------------
function array_subtract($a, $b)
{
    return array_diff($a, array_intersect($a, $b));
}
/**
 +----------------------------------------------------------
 * 把返回的数据集转换成array形式的字符串
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param 参数不限制
 +----------------------------------------------------------
 * @return string
 +----------------------------------------------------------
 */
function strapiarr(){
	
	$args=func_get_args();
	$str='array(';
	foreach($args as $key => $vo){
		if(strpos($vo, '$')!==false){
		$str .= $vo;	
		}else{
		$str .= "'".$vo."'";
		
		
		}
		
		$str .= ',';
	}
	$str .= ')';
	return $str;
}


/**
 +----------------------------------------------------------
 * 把返回的数据集转换成Tree
 +----------------------------------------------------------
 * @access public
 +----------------------------------------------------------
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 +----------------------------------------------------------
 * @return array
 +----------------------------------------------------------
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root=0) {
	// 创建Tree
	$tree = array();
	if (is_array($list)) {
		// 创建基于主键的数组引用
		$refer = array();
		foreach ($list as $key => $data) {
			$refer[$data[$pk]] = & $list[$key];
		}
		foreach ($list as $key => $data) {
			// 判断是否存在parent
			$parentId = $data[$pid];
			if ($root == $parentId) {
				$tree[] = & $list[$key];
			} else {
				if (isset($refer[$parentId])) {
					$parent = & $refer[$parentId];
					$parent[$child][] = & $list[$key];
				}
			}
		}
	}
	return $tree;
}
/**
 * 对查询结果集进行排序
 * @access public
 * @param array  $list 查询结果
 * @param string $field 排序的字段名
 * @param array  $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data)
            $refer[$i] = & $data[$field];
        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val)
            $resultSet[] = & $list[$key];
        return $resultSet;
    }
    return false;
}
function int_to_string(&$data,$map=array('status'=>array(1=>'正常',-1=>'删除',0=>'禁用',2=>'未审核',3=>'草稿'))) {
    if($data === false || $data === null ){
        return $data;
    }
    $data = (array)$data;
    foreach ($data as $key => $row){
        foreach ($map as $col=>$pair){
            if(isset($row[$col]) && isset($pair[$row[$col]])){
                $data[$key][$col.'_text'] = $pair[$row[$col]];
            }
        }
    }
    return $data;
}
/**过滤转义字符*/
function stripslashes_deep($value) {
	if (is_array($value)) {
		$value = array_map('stripslashes_deep', $value);
	} elseif (is_object($value)) {
		$vars = get_object_vars($value);
		foreach ($vars as $key => $data) {
			$value->{$key} = stripslashes_deep($data);
		}
	} else {
		$value = stripslashes($value);
	}

	return $value;
}
/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}
/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}
/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
	
	
	
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    //截取内容时去掉图片，仅保留文字
    
    
    return $suffix ? $slice.'...' : $slice;
}
function clearHtml($content){
	$content=preg_replace("/<a[^>]*>/i","",$content);
	$content=preg_replace("/<\/a>/i","",$content);
	$content=preg_replace("/<div[^>]*>/i","",$content);
	$content=preg_replace("/<\/div>/i","",$content);
	$content=preg_replace("/<!--[^>]*-->/i","",$content);//注释内容     
	$content=preg_replace("/style=.+?['|\"]/i",'',$content);//去除样式     
	$content=preg_replace("/class=.+?['|\"]/i",'',$content);//去除样式     
	$content=preg_replace("/id=.+?['|\"]/i",'',$content);//去除样式        
	$content=preg_replace("/lang=.+?['|\"]/i",'',$content);//去除样式         
	$content=preg_replace("/width=.+?['|\"]/i",'',$content);//去除样式      
	$content=preg_replace("/height=.+?['|\"]/i",'',$content);//去除样式      
	$content=preg_replace("/border=.+?['|\"]/i",'',$content);//去除样式      
	$content=preg_replace("/face=.+?['|\"]/i",'',$content);//去除样式      
	$content=preg_replace("/face=.+?['|\"]/",'',$content);//去除样式 只允许小写 正则匹配没有带 i 参数   
	return $content;
}
function cutstr_html($string,$length=0,$ellipsis='…'){
   
	$string=strip_tags($string);
	$string=preg_replace("/\n/is",'',$string);
	$string=preg_replace("/\r\n/is",'',$string);
	
	$string=preg_replace('/ |　/is','',$string);
	$string=preg_replace('/&nbsp;/is','',$string);

 if(mb_strlen($string,'utf-8')<=$length){
		$ellipsis='';		
	}
	preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/",$string,$string);
	if(is_array($string)&&!empty($string[0])){
		if(is_numeric($length)&&$length){
			
			
			
			$string=join('',array_slice($string[0],0,$length)).$ellipsis;
		}else{
			$string=implode('',$string[0]);
		}
	}else{
		$string='';
	}
	return $string;
}