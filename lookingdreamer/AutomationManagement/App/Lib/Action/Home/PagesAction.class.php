<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class PagesAction extends BaseAction{

    // 帮助中心首页
    public function index() {
    		if(!$_GET['catid'])$_GET['catid'] = C("ABOUT_CATID");
    		$category = $this->category;
    		if(!$category['childids'] && $_GET['catid']){
    			$PageModel = M("Pages");
    			$vo = $PageModel -> where("status = 1 AND catid='".$_GET['catid']."'") -> order("sort DESC, id ASC") -> find();
    			if($vo){
	    			$url = getReadUrl($vo['id'], $vo, MODULE_NAME, 1);
	    			header("Location: ".$url);
	    			exit;
	    		}
    		}
	      $this->_list('Pages',"status=1 AND catid<>'".C("ABOUT_CATID")."'");
	      $this->display();
    }
}
?>