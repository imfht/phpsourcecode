<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2012 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------

defined('THINK_PATH') or exit();
/**
 * Html标签库驱动
 * @category   Extend
 * @package  Extend
 * @subpackage  Driver.Taglib
 */
class TagLibDatacall extends TagLibHtml{
    // 标签定义
    protected $tags   =  array(
        // 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
        'baidueditor' => array('attr'=>'id,name,style,width,height','close'=>1),
        'focus' => array('attr'=>'code','close'=>0),        
        'module' => array('attr'=>'code','close'=>0),
        'sql' => array('attr'=>'code','close'=>0),
        'html' => array('attr'=>'code','close'=>0)
        );
		
    /**
     * editor标签解析 插入可视化编辑器
     * 格式： <datacall:editor id="editor" name="remark" type="FCKeditor" style="" >{$vo.remark}</datacall:editor>
     * @access public
     * @param string $attr 标签属性
     * @return string|void
     */
    public function _baidueditor($attr,$content) {
        $tag        =	$this->parseXmlAttr($attr,'editor');
        $id			=	!empty($tag['id'])?$tag['id']: 'editor_'.$tag['name'];
        $name   	=	$tag['name'];
        $style   	    =	!empty($tag['style'])?$tag['style']:'';
        $width		=	!empty($tag['width'])?$tag['width']: '550px';
        $height     =	!empty($tag['height'])?$tag['height'] :'320px';
        $ue_name = "UE_".$id;
        $parseStr    =  '';
        $parseStr   .=	'<!-- 编辑器调用开始 --><script type="text/javascript" src="__ROOT__/Public/Js/BaiduEditor/editor_config.js"></script>';
        $parseStr   .=	'<script type="text/javascript" src="__ROOT__/Public/Js/BaiduEditor/editor_all.js"></script>';
        $parseStr   .=	'<link rel="stylesheet" type="text/css" href="__ROOT__/Public/Js/BaiduEditor/themes/default/ueditor.css"/>';
        $parseStr   .=	'<script type="text/plain" id="'.$id.'" name="'.$name.'" style="width:'.$width.';">'.$content.'</script>';
        $parseStr   .=	'<script type="text/javascript">SyntaxHighlighter.highlight();';
        $parseStr   .=	'for(var i=0,di;di=SyntaxHighlighter.highlightContainers[i++];){';
        $parseStr   .=	'var tds = di.getElementsByTagName(\'td\');';
        $parseStr   .=	'for(var j=0,li,ri;li=tds[0].childNodes[j];j++){';
        $parseStr   .=	'ri = tds[1].firstChild.childNodes[j];';
        $parseStr   .=	'ri.style.height = li.style.height = ri.offsetHeight + \'px\';';
        $parseStr   .=	'}';
        $parseStr   .=	'}';
        $parseStr   .=	'var '.$ue_name.' = new UE.ui.Editor( {  } ); '.$ue_name.'.render( "'.$id.'" );</script>';

        return $parseStr;
    }		
		
    /**
     * focus标签解析
     * 格式： <datacall:focus fscode="" />
     * @access public
     * @param string $attr 标签属性
     * @return string|void
     */
    public function _focus($attr) {
        $tag        = $this->parseXmlAttr($attr,'focus');
        $fscode       = strtoupper($tag['code']);                //名称
				$focuspattern = include(DATA_PATH.'~datacall_focus.php');
				$focus = $focuspattern[$fscode];
				$focus_id = "Focus_".implode("", build_count_rand(1, rand(8, 16), 0));
				$parseStr = '{$list_'.$focus_id.'|getListByModule=\'Focuspic\','.intval($focus["recordcount"]).',\''.$focus["orderby"].'\', \'\', \'focuspic_'.$focus_id.'\','.intval($focus["cachetime"]).'}';
				$parseStr .= '<if condition="$list_'.$focus_id.'"><div id="'.$focus_id.'">';
				$parseStr .= "<div class=\"loading\"></div>";
				$parseStr .= "<div class=\"pic\">";
				$parseStr .= '<ul><volist name="list_'.$focus_id.'" id="fvo">';
				$parseStr .= '<li><a href="{$fvo.url}"><img src="http://{$Think.server.server_name}/Public/Uploads/Focus/{$fvo.pic}" thumb="" alt="{$fvo.title}" text="{$fvo.remark}" /></a></li>';
				$parseStr .= "</volist></ul>";
				$parseStr .= "</div>";
				$parseStr .= "</div>";
				$parseStr .= "<script type=\"text/javascript\">";
				$parseStr .= "myFocus.set({ ";
				$parseStr .= "id:'".$focus_id."',";
				$parseStr .= "width: ".intval($focus["focuswidth"]).",";
				if(is_numeric($focus["focusheight"]) && $focus["focusheight"]){
					$parseStr .= "height: ".$focus["focusheight"].",";
				}
				$parseStr .= "pattern:'".$focus["focusstyle"]."'";
				$parseStr .= " });";
				$parseStr .= "</script></if>";
        return $parseStr;
    }
    
    //<include file="Datacall:infobox_section" />
    /**
     * focus标签解析
     * 格式： <datacall:module code="" />
     * @access public
     * @param string $attr 标签属性
     * @return string|void
     */
    public function _module($attr) {
        $tag        = $this->parseXmlAttr($attr,'module');
        $callcode       = strtoupper($tag['code']);                //名称
				$datacall = include(DATA_PATH.'~datacall_module.php');
				$pattern = $datacall[$callcode];
				$parseStr = '{$list_'.$focus_id.'|getListByModule=\'Focuspic\','.intval($focus["fcount"]).',\'sort DESC, update_time DESC\', \'\', \'focuspic_'.$focus_id.'\','.intval($focus["cachetime"]).'}';
				
    		$parseStr = file_get_contents('./App/Tpl/Home/Default/Datacall/product_section.html');
				$parseStr = '<include file="Datacall:infobox_section" />';
    		return $parseStr;
    }
    
    public function _html($attr) {
    		$parseStr = file_get_contents('./App/Tpl/Home/Default/Datacall/product_section.html');
				$parseStr = '<include file="Datacall:infobox_section" />';
    		return $parseStr;
    }
    
    public function _sql($attr) {
    		$parseStr = file_get_contents('./App/Tpl/Home/Default/Datacall/product_section.html');
				$parseStr = '<include file="Datacall:infobox_section" />';
    		return $parseStr;
    }        
    
}