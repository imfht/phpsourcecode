<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class IndexAction extends BaseAction{
    public function index()
    {
        $CacheFile = DATA_PATH.'~category_blog.php';
        if(file_exists($CacheFile)){
        	$category = include_once($CacheFile);
        	$this->catlist = $category;
					$category = list_to_tree($category);
					$this->category = $category;
        }    	
    		C("PER_PAGE", 20);
    		$this->_list("Blog","status=1");
        $this->assign("WEBTITLE", C("BLOG_TITLE"));
        $this->assign("KEYWORDS", C("BLOG_KEYWORDS"));
        $this->assign("DESCRIPTION", C("BLOG_DESCRIPTION"));
        $this->assign("SEOKEY", explode(',',C("SEOKEY")));
        $this->display();
    }

}
?>