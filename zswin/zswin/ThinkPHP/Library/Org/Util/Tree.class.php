<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

namespace Org\Util;

class tree {

    static public function unlimitCategoryFormat($cate,$name='child',$pid = 0){
		$arr = array();
		foreach($cate as $_v){
			if($_v['pid'] == $pid){
				$_v[$name] = self::unlimitCategoryFormat($cate,$name,$_v['id']);
				$arr[] = $_v;
			}
		}
		return $arr;
	}
	
	static public function treeFormat($arr){
		$html = '';
		foreach($arr as $_v){
			
			if(!empty($_v['child'])){
				
					$html .= '<li id="cate'.$_v['id'].'" class="dropdown"><a href="'.ZSU('/artlist/'.$_v['id'],'Index/artlist',array('cid'=>$_v['id'])).'">'.$_v['name'].'<span class="caret"></span></a><div class="grid-container3"><ul>'.self::treeFormat($_v['child']).'</ul></div></li>';
				
			}else{
				$html .= '<li  id="cate'.$_v['id'].'"><a href="'.ZSU('/artlist/'.$_v['id'],'Index/artlist',array('cid'=>$_v['id'])).'">'.$_v['name'].'</a></li>';
			}
		}
		$html .= '';
		
		
		
		
		return $html;
	}

}
