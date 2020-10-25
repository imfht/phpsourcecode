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
namespace Home\Controller;

class IndexController extends CommonController {
    public function index(){
	 	
	   C('SITE_TITLE','');	  
	   
	   $this->title=C('SITE_NAME');
       $this->meta_keywords=C('META_KEYWORDS');
       $this->meta_description=C('META_DESCRIPTION');
       $this->display();
	 
    }
}