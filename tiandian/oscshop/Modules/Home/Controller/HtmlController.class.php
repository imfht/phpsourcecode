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

class HtmlController extends CommonController {
	
    public function about(){
       $this->title='关于我们-';		   	
       $this->meta_keywords=C('SITE_KEYWORDS');
       $this->meta_description=C('SITE_DESCRIPTION');
       $this->display();
    }
	
    public function contact(){
       $this->title='联系我们-';		   	
       $this->meta_keywords=C('SITE_KEYWORDS');
       $this->meta_description=C('SITE_DESCRIPTION');
       $this->display();
    }
	
		
	
}