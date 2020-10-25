<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class SpiderAction extends CommonAction {

		public function index()
	  {  	
	      //列表过滤器，生成查询Map对象
	      $map = $this->_search();
	      if(method_exists($this,'_filter')) {
	          $this->_filter($map);
	      }
	      if($_REQUEST['filter']=='user'){
	      	$map = "spider_code = 'user'";
	      }else{
		      $map = "spider_code <> 'user'";
		    }
		    if($_REQUEST['spider'])$map .= " AND spider_code = '".$_REQUEST['spider']."'";
				$model = M('Viewlog');
	      if(!empty($model)) {
	        	$this->_list($model, $map, 'request_time',false);
	      }
				$this->display();
	  }	
    
}
?>