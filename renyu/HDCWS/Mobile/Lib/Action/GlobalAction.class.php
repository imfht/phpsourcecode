<?php

class GlobalAction extends Action {
	
	protected function listToTree($list, $pk = 'id', $pid = 'pid', $child = 'list', $root = 0){
	
	    // 创建Tree
	    $tree = array();
	
	    if(is_array($list)) {
	
	        // 创建基于主键的数组引用
	        $refer = array();
	
	        foreach ($list as $key => $data) {
	
	            $refer[$data[$pk]] =& $list[$key];
	
	        }
	
	        foreach ($list as $key => $data) {
	
	            // 判断是否存在parent
	            $parentId = $data[$pid];
	
	            if ($root == $parentId) {
	
	                $tree[] =& $list[$key];
	
	            }else{
	
	                if (isset($refer[$parentId])) {
	
	                    $parent =& $refer[$parentId];
	
	                    $parent[$child][] =& $list[$key];
	
	                }
	
	            }
	
	        }
	
	    }
	    
	    return $tree;
	
	}
	
	public function verify(){
		
		import('ORG.Util.Image');
		
		Image::buildImageVerify(4, 1, 'png', 50, 20);
		
	}	
	
}