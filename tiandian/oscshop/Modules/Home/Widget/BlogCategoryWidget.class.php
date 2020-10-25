<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Widget;
use Think\Controller;
/**
 * 博客分类
 */
class BlogCategoryWidget extends Controller{
	
	function blog_category_show(){
		if (!$blog_category = S('blog_category')) {
			
			$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
			$list=M('BlogCategory')->select();
			$category=list_to_tree($list);	
			
			foreach ($category as $k => $v) {
				$category[$k]['id']=$hashids->encode($v['id']);
				if(isset($v['children']))
				foreach ($v['children'] as $k1 => $v1) {
					$category[$k]['children'][$k1]['id']=$hashids->encode($v1['id']);
				}
			}
			
			S('blog_category', $category);	
			$blog_category=$category;
		}
		
		$this->blog_category=$blog_category;		
		
		$this->display('Widget:blog_category');
	}
	
}
