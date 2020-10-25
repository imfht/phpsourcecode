<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Admin\Widget;
use Think\Template\TagLib;
/**
 * Html标签库驱动
 */
class Html extends TagLib{
    // // 标签定义
    // protected $tags   =  array(
    //     // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
    //     'text'     =>array('attr'=>'name,value,tip,param','close'=>0),
    // );

    // /**
    //  * editor标签解析 插入可视化编辑器
    //  * 格式： <html:editor id="editor" name="remark" type="FCKeditor" style="" >{$vo.remark}</html:editor>
    //  * @access public
    //  * @param array $tag 标签属性
    //  * @return string|void
    //  */
    // public function _editor($tag,$content) {
    //     $id			=	!empty($tag['id'])?$tag['id']: '_editor';
    //     $name   	=	$tag['name'];
    //     $style   	    =	!empty($tag['style'])?$tag['style']:'';
    //     $width		=	!empty($tag['width'])?$tag['width']: '100%';
    //     $height     =	!empty($tag['height'])?$tag['height'] :'320px';
    //  //   $content    =   $tag['content'];
    //     $type       =   $tag['type'] ;
    //     switch(strtoupper($type)) {
    //         case 'FCKEDITOR':
    //             $parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKeditor/fckeditor.js"></script><textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKeditor/" ; oFCKeditor.ReplaceTextarea() ;function resetEditor(){setContents("'.$id.'",document.getElementById("'.$id.'").value)}; function saveEditor(){document.getElementById("'.$id.'").value = getContents("'.$id.'");} function InsertHTML(html){ var oEditor = FCKeditorAPI.GetInstance("'.$id.'") ;if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){oEditor.InsertHtml(html) ;}else	alert( "FCK必须处于WYSIWYG模式!" ) ;}</script> <!-- 编辑器调用结束 -->';
    //             break;
    //         case 'FCKMINI':
    //             $parseStr   =	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/FCKMini/fckeditor.js"></script><textarea id="'.$id.'" name="'.$name.'">'.$content.'</textarea><script type="text/javascript"> var oFCKeditor = new FCKeditor( "'.$id.'","'.$width.'","'.$height.'" ) ; oFCKeditor.BasePath = "__ROOT__/Public/Js/FCKMini/" ; oFCKeditor.ReplaceTextarea() ;function resetEditor(){setContents("'.$id.'",document.getElementById("'.$id.'").value)}; function saveEditor(){document.getElementById("'.$id.'").value = getContents("'.$id.'");} function InsertHTML(html){ var oEditor = FCKeditorAPI.GetInstance("'.$id.'") ;if (oEditor.EditMode == FCK_EDITMODE_WYSIWYG ){oEditor.InsertHtml(html) ;}else	alert( "FCK必须处于WYSIWYG模式!" ) ;}</script> <!-- 编辑器调用结束 -->';
    //             break;
    //         case 'EWEBEDITOR':
    //             $parseStr	=	"<!-- 编辑器调用开始 --><script type='text/javascript' src='__ROOT__/Public/Js/eWebEditor/js/edit.js'></script><input type='hidden'  id='{$id}' name='{$name}'  value='{$conent}'><iframe src='__ROOT__/Public/Js/eWebEditor/ewebeditor.htm?id={$name}' frameborder=0 scrolling=no width='{$width}' height='{$height}'></iframe><script type='text/javascript'>function saveEditor(){document.getElementById('{$id}').value = getHTML();} </script><!-- 编辑器调用结束 -->";
    //             break;
    //         case 'NETEASE':
    //             $parseStr   =	'<!-- 编辑器调用开始 --><textarea id="'.$id.'" name="'.$name.'" style="display:none">'.$content.'</textarea><iframe ID="Editor" name="Editor" src="__ROOT__/Public/Js/HtmlEditor/index.html?ID='.$name.'" frameBorder="0" marginHeight="0" marginWidth="0" scrolling="No" style="height:'.$height.';width:'.$width.'"></iframe><!-- 编辑器调用结束 -->';
    //             break;
    //         case 'UBB':
    //             $parseStr	=	'<script type="text/javascript" src="__ROOT__/Public/Js/UbbEditor.js"></script><div style="padding:1px;width:'.$width.';border:1px solid silver;float:left;"><script LANGUAGE="JavaScript"> showTool(); </script></div><div><TEXTAREA id="UBBEditor" name="'.$name.'"  style="clear:both;float:none;width:'.$width.';height:'.$height.'" >'.$content.'</TEXTAREA></div><div style="padding:1px;width:'.$width.';border:1px solid silver;float:left;"><script LANGUAGE="JavaScript">showEmot();  </script></div>';
    //             break;
    //         case 'KINDEDITOR':
    //             $parseStr   =  '<script type="text/javascript" src="__ROOT__/Public/Js/KindEditor/kindeditor.js"></script><script type="text/javascript"> KE.show({ id : \''.$id.'\'  ,urlType : "absolute"});</script><textarea id="'.$id.'" style="'.$style.'" name="'.$name.'" >'.$content.'</textarea>';
    //             break;
    //         default :
    //             $parseStr  =  '<textarea id="'.$id.'" style="'.$style.'" name="'.$name.'" >'.$content.'</textarea>';
    //     }

    //     return $parseStr;
    // }



}