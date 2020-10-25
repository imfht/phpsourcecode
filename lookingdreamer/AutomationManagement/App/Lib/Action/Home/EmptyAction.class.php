<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class EmptyAction extends Action {
    public function _empty($method) {
    	if(file_exists("404.html")){
    		$this->display("404.html");		
    	}else{
	        exit("the Page can't be found!");
	    }
    }
}
?>