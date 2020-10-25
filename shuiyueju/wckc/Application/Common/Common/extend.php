<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 水月居 <singliang@163.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 性别格式化输出
 * @param int $time
 * @return string 1男2女 0保密 显示
 * @author 水月居 <singliang@163.com>
 */
function sex_format($sex = NULL){
    switch ($sex){
    case 1:
    $format="男";break;
    case 2:
    $format="女";break;
    default:
    $format="保密";break;             ;
    }
          return $format;
}
function str_DeleteHtml($str=NULL){
	$str=htmlspecialchars_decode(str_replace( '&nbsp;', '', $str));
	$str = trim($str); //清除字符串两边的空格
	$str = strip_tags($str,""); //利用php自带的函数清除html格式
	$str = preg_replace("/\t/","",$str); //使用正则表达式替换内容，如：空格，换行，并将替换为空。
	$str = preg_replace("/\r\n/","",$str); 
	$str = preg_replace("/\r/","",$str); 
	$str = preg_replace("/\n/","",$str); 
	$str = preg_replace("/ /","",$str);
	$str = preg_replace("/  /","",$str);  //匹配html中的空格
	return trim($str); //返回字符串

}
/**
 * c函数用于过滤标签，输出没有html的干净的文本内容content
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_c($text)
{
    //$text=htmlspecialchars_decode(str_replace( '&nbsp;', '', $text));

    // $reg="/style=\"[^\"]*?\"/i";  
    // $text=preg_replace($reg,"",$text); 
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<br><hr><a><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';

    //$html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    $content_tags= $base_tags . '<ul><ol><li><dl><dd><dt><table><td><th><tr><thead><tbody><tfoot><col><colgroup><div>';
    //过滤标签
    $text = strip_tags($text, $content_tags );
    // 过滤攻击代码
    
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
     $text = trim($text);
    return $text;

}
