<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class SitemapAction extends BaseAction{
    
    // 网站地图首页
    public function index() {
    		$modules = array('new', 'down', 'product', 'case', 'job', 'blog', 'article', 'pages');
    		$modelName  = empty($modelName)?$this->getActionName():$modelName;
        $catlist   =  include DATA_PATH.'~category_'.strtolower($modelName).'.php';
        $CurModule = $catlist[$modelName];
        if(!$_GET['catid'] || !is_numeric($_GET['catid'])){
        	$_GET['catid'] = 0;
        	$cat = $CurModule;
        }else{
	        $cat = $catlist[$_GET['catid']];
	      }
				$KEYWORDS = array();
				$DESCRIPTION = array();
				$TITLE[] = $cat['title'];
				if($cat['description'])$DESCRIPTION[] = $cat['description'];
				if($cat['keywords']){
					$KEYWORDS = explode(',', $cat['keywords']);
					foreach($KEYWORDS as $keyword){
						if($keyword && $cat['keywords_in_title'] && !in_array($keyword, $TITLE))$TITLE[] = $keyword;
						if($keyword && $cat['keywords_in_description'] && !in_array($keyword, $DESCRIPTION))$DESCRIPTION[] = $keyword;
					}
				}
				if($cat['urlwords']){
					$cat['urlwords'] = explode(',', $cat['urlwords']);
					foreach($cat['urlwords'] as $urlword){
						if($urlword && $cat['urlwords_in_title'] && !in_array($urlword, $TITLE))$TITLE[] = $urlword;
						if($urlword && $cat['urlwords_in_description'] && !in_array($urlword, $DESCRIPTION))$DESCRIPTION[] = $urlword;
						if($urlword && $cat['urlwords_in_keywords'] && !in_array($urlword, $KEYWORDS))$KEYWORDS[] = $urlword;
					}
				}
				if($cat['title_in_keywords'] && !in_array($cat['title'], $KEYWORDS))$KEYWORDS[] = $cat['title'];
				if($cat['title_in_description'] && !in_array($cat['title'], $DESCRIPTION))$DESCRIPTION[] = $cat['title'];
				$this->WEBTITLE  =  implode(C('TITLE_PATHINFO_DEPR'),$TITLE);
				$this->KEYWORDS  =  implode(',',$KEYWORDS);
				$this->DESCRIPTION  =  implode(',',$DESCRIPTION); 
        $this->CurModule = $CurModule;
            	
    		$Cachefile = DATA_PATH.'~category.php';
    		$list = include($Cachefile);
    		$catlist = array();
    		foreach($list as $val){
	    			if(!in_array(strtolower($val['module']), $modules))continue;
	    			if(!$val['level']){
	    				$catlist[$val['module']]['name'] = $val;
	    			}else{
		    			$catlist[$val['module']]['list'][] = $val;
		    		}
    		}
    		$this->assign("catlist",$catlist);
        $this->display();
    }
    
    public function maphtml(){
    		$module = $_GET['mod'];
    		if(!$module)$this->error(L('Sitemap XML不存在！'));
    		$catid = intval($_GET['catid']);
    		C("PER_PAGE", C("SITEMAP_COUNT"));
    		$this->_list($module,"status=1 AND (catid='".$catid."' OR catstr LIKE '%,".$catid.",%')", 'update_time', false);
    		$title = $this->WEBTITLE;
    		$this->WEBTITLE = $title.C('TITLE_PATHINFO_DEPR')."网站地图";
    		$this->assign("CurModule",$module);
        $this->display();
    }
    
    public function rss(){
    		$module = $_GET['mod'];
    		if(!$module)$this->error(L('Sitemap XML不存在！'));
    		$module_group = modulegroup($module);
    		$catid = intval($_GET['catid']);
    		$cat = getCategory($catid);
    		C("PER_PAGE", C("SITEMAP_COUNT"));
    		$this->_list($module,"status=1 AND (catid='".$catid."')", 'update_time', false);
    		$list = $this->list;
    		$channel = array(
					'title' => $cat['title'],
					'description' => $cat['description'],
					'link' => getUrl($cat, $module_group.'/'.$module.'/index?catid='.$cat['id'], 1),
					'generator' => $_SERVER['SERVER_NAME'],
					'ttl' => count($list),
    		);
    		foreach($list as $val){
    				$channel[] = array(
    					'title' => $val['title'],
    					'link' => getUrl($val, $module_group.'/'.$module.'/read?id='.$val['id'], 1),
    					'pubDate' => '<![CDATA['.gmdate(DATE_RSS, $val['create_time']).']]>',
    					'lastBuildDate' => '<![CDATA['.gmdate(DATE_RSS, $val['update_time']).']]>',
    					'source' => '<![CDATA['.$_SERVER['SERVER_NAME'].']]>',
    					'author' => '<![CDATA['.C('WEB_NAME').']]>',
    					'description' => '<![CDATA['.cutstr(HtmlTrim($val['content'], '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17'), 150).']]>'
    				);
    		}
    		header("Content-type: text/xml");
		    $xml    = '<?xml version="1.0" encoding="UTF-8"?>';
		    $xml   .= '<rss version="2.0">';
		    $xml   .= '<channel>';
		    $xml   .= $this->_toxml($channel);
		    $xml   .= '</channel>';
		    $xml   .= '</rss>';
    		echo $xml;
    }
    
		function _toxml($data) {
		    $xml = '';
		    foreach ($data as $key => $val) {
		        is_numeric($key) && $key = "item";
		        $xml    .=  "<$key>";
		        $xml    .=  ( is_array($val) || is_object($val)) ? $this->_toxml($val) : $val;
		        list($key, ) = explode(' ', $key);
		        $xml    .=  "</$key>";
		    }
		    return $xml;
		}    

}
?>