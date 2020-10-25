<?php
/** ***********************
 * 作者：卢逸 www.61php.com
 * 日期：2015/5/21
 * 作用：视图模型
 ** ***********************/
class coreViewHome extends coreFrameworkView
{
	function index(){
		
		$temp=_lang_61php;
		
		$get=$this->GVar->fget;
		$post=$this->GVar->fpost;
		
		
		$this->dp("index");
	}

}

?>