<?php

/*
 *      This is NOT a freeware, use is subject to license terms
 *      [SEOPHP] (C) 2012-2015 QQ:224505576  SITE: http://seophp.taobao.com/
*/

class BlogAction extends BaseAction{
	
    // 博客首页
    public function index() {
    		
        $this->_list("Blog","status=1");
        $this->display();
    }
    
    public function guestbook() {
        $this->_list('Comment',"modid=0 AND status=1 AND module='Blog'");
        $this->display();  
    }
    
    public function aboutme() {
        $this->display();
    }      
    
    public function read($id, $model) {
	    	$model  = empty($model)?$this->getActionName():$model;
				// 查看具体的信息内容
				$mo = M($model);
				$vo   =  $mo->find($id);
				if(!$vo  || $vo['status']   ==0 ) {
						$this->error('访问的信息不存在或已经删除！');
				}
				$PrevTitle   =  $mo->where('id<'.$id)->order('id DESC')->find();
				$NextTitle   =  $mo->where('id>'.$id)->order('id ASC')->find();
				$this->PrevTitle  =  $PrevTitle;
				$this->NextTitle  =  $NextTitle;
        $catlist   =  include DATA_PATH.'~category_'.strtolower($model).'.php';
        $CurModule = $catlist[$model];
        $this->CurModule = $CurModule;
				$KEYWORDS = array();
				$DESCRIPTION = array();
				$TITLE[] = $vo['title'];
				if($vo['description'])$DESCRIPTION[] = $vo['description'];
				if($vo['keywords']){
					$KEYWORDS = explode(',', $vo['keywords']);
					foreach($KEYWORDS as $keyword){
						if($keyword && $vo['keywords_in_title'] && !in_array($keyword, $TITLE))$TITLE[] = $keyword;
						if($keyword && $vo['keywords_in_description'] && !in_array($keyword, $DESCRIPTION))$DESCRIPTION[] = $keyword;
					}
				}
				if($vo['urlwords']){
					$vo['urlwords'] = explode(',', $vo['urlwords']);
					foreach($vo['urlwords'] as $urlword){
						if($urlword && $vo['urlwords_in_title'] && !in_array($urlword, $TITLE))$TITLE[] = $urlword;
						if($urlword && $vo['urlwords_in_description'] && !in_array($urlword, $DESCRIPTION))$DESCRIPTION[] = $urlword;
						if($urlword && $vo['urlwords_in_keywords'] && !in_array($urlword, $KEYWORDS))$KEYWORDS[] = $urlword;
					}
				}
				if($vo['seokey']){
					if($vo['seokey'] != $vo['title']){
						if($vo['seokey_in_title'] && !in_array($vo['seokey'], $TITLE))$TITLE[] = $vo['seokey'];
						if($vo['seokey_in_description'] && !in_array($vo['seokey'], $DESCRIPTION))$DESCRIPTION[] = $vo['seokey'];
						if($vo['seokey_in_keywords'] && !in_array($vo['seokey'], $KEYWORDS))$KEYWORDS[] = $vo['seokey'];
					}
					$this->FOOTSEOKEY  =  array(
						array("link" => getReadUrl($vo["id"], $vo, $model, 1), 'name' => $vo['seokey'])
					);
				}
				if($vo['title_in_keywords'] && !in_array($vo['title'], $KEYWORDS))$KEYWORDS[] = $vo['title'];
				if($vo['title_in_description'] && !in_array($vo['title'], $DESCRIPTION))$DESCRIPTION[] = $vo['title'];
				$this->WEBTITLE  =  implode(C('TITLE_PATHINFO_DEPR'),$TITLE);
				$this->KEYWORDS  =  implode(',',$KEYWORDS);
				$this->DESCRIPTION  =  implode(',',$DESCRIPTION);
				if(($vo['seokey'] || $KEYWORDS) && $vo["content"]){
						import("ORG.Util.Seokey");
						$seokey = array();
						if($vo['seokey']){
							$seokey[] = array('Key' => '<a href="'.getReadUrl($vo["id"], $vo, $model, 1).'">'.$vo['seokey'].'</a>', 'Href' => '<STRONG>'.$vo['seokey'].'</STRONG>', 'ReplaceNumber' => 1);
							$seokey[] = array('Key' => $vo['seokey'], 'Href' => '<STRONG>'.$vo['seokey'].'</STRONG>', 'ReplaceNumber' => 1);
						}
						if(!in_array($vo['title'],$KEYWORDS) && $vo['seokey'] != $vo['title'])$seokey[] = array('Key' => $vo['title'], 'Href' => '<STRONG>'.$vo['title'].'</STRONG>', 'ReplaceNumber' => 1);
						foreach($KEYWORDS as $val){
							if($val != $vo['seokey'])$seokey[] = array('Key' => $val, 'Href' => '<STRONG>'.$val.'</STRONG>', 'ReplaceNumber' => 1);
						}
						if($seokey){
							$Rep = new Seokey($seokey,$vo["content"]);
							$Rep->KeyOrderBy();
							$Rep->Replaces();
							$vo["content"] = $Rep->HtmlString;
						}
				}
				// 获取评论内容
				$CommentModel = M("comment");
				$recordcount = $CommentModel -> where("status=1 AND module='".$model."' AND modid=".$id) -> count("id");
				$list = $CommentModel -> where("status=1 AND module='".$model."' AND modid=".$id) -> limit(20) -> order("create_time DESC") -> select();
				// 获取最新动态
				//$lastestlist   =  include DATA_PATH.'~'.strtolower($model).'.php';
				$this->assign('list',$list);
				$this->assign('recordcount',$recordcount);
				//$this->assign('lastestlist',$lastestlist);
				$this->assign('data',$vo);
				//$this->display('./App/Tpl/Home/Default/New/read.html');
				$this->display('read');
    }
    
    public function rss(){
    		$module = 'Blog';
    		$module_group = 'Blog';
    		C("PER_PAGE", C("SITEMAP_COUNT"));
    		$this->_list($module,"status=1", 'update_time', false);
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