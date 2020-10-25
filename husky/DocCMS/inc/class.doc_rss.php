<?php
// +------------------------------------------------+
// | SHL_RSS                                        |
// +------------------------------------------------+
// | Copyright (c) 2010~2012 SHL                    |
// +------------------------------------------------+
// |												|
// | 为功能模块，提供相应的RSS订阅.						|
// |												|
// +------------------------------------------------+
// |                                          		|
// | Authors:YRFGP;QQ:348681066;					|
// | Time：2010.04.13                              	|
// |                                          		|
// +------------------------------------------------+
class doc_rss
{
	private $db;
	private $cid;
	private $model;
	private $tb_pix;
	private $rewrite;
	private $http;
	private $url;
	private $num;
	
	private $dbdatas;
	private $xml;
	
	function doc_rss($db,$cid,$num=9)
	{
		$this->db=$db;
		$this->cid = $cid;
		
			$this->menu_arr=!$this->cid 
							? array('id'=>'0','title'=>'','description'=>'','type'=>'index','related_common'=>'','orderby'=>'0','level'=>0)
							:($this->db->get_row("SELECT * FROM ".TB_PREFIX."menu WHERE id=$this->cid",ARRAY_A));
		$this->model = $this->menu_arr['type'];
		$this->tb_pix = TB_PREFIX;
		$this->rewrite = URLREWRITE;
		$this->http = 'http://'.$_SERVER['HTTP_HOST'];
		$this->url=$this->http.str_replace('&','&amp;',$_SERVER['REQUEST_URI']);
		$this->num = $num<1?30:$num;
	}
	public function get_title()
	{
		return !$this->menu_arr['title']? SITENAME : ($this->menu_arr['title'].' '.SITENAME);
	}
	public function get_description()
	{
		return !$this->menu_arr['description']? SITESUMMARY : ($this->menu_arr['description'].' '.SITESUMMARY) ;
		
	}
	private function get_menuName($id='')
	{
		return '/'.( !$id ? ($this->menu_arr['menuName']) : ($this->db->get_var("SELECT menuName FROM ".TB_PREFIX."menu WHERE id=$id")) ).'/';
	}
	private function format_time($time)
	{		
		return date('r', mktime((int)substr($time,11,2), (int)substr($time,14,2), (int)substr($time,17,2), (int)substr($time,5,2),(int)substr($time,8,2), (int)substr($time,0,4)));
	}
	private function rss_header()
	{
		$this->xml='<?xml version="1.0" encoding="UTF-8"?>';
		$this->xml.='<rss version="2.0"';
		$this->xml.='	xmlns:content="http://purl.org/rss/1.0/modules/content/"';
		//$this->xml.='	xmlns:wfw="http://wellformedweb.org/CommentAPI/"';
		//$this->xml.='	xmlns:dc="http://purl.org/dc/elements/1.1/"';
		$this->xml.='	xmlns:atom="http://www.w3.org/2005/Atom"';
		//$this->xml.='	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"';
		//$this->xml.='	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"';
		$this->xml.='>';
		$this->xml.='<channel>';
	    $this->xml.='  <title>'.$this->get_title().'</title>';
	    $this->xml.='  <atom:link href="'.$this->url.'" rel="self" type="application/rss+xml" />';
	    $this->xml.='  <link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid) : $this->get_menuName() ).'</link>';
	    $this->xml.='  <description><![CDATA['.$this->get_description().']]></description>';
		$this->xml.='  <generator>http://doccms.com/?v=x1.0</generator>';
		$this->xml.='  <language>zh-cn</language>';
		$this->xml.='  <xhtml:meta xmlns:xhtml="http://www.w3.org/1999/xhtml" name="robots" content="noindex" />';//告诉蜘蛛不索引当前页 但可以沿着该页面上的链接继续抓取下去
		//$this->xml.='  <sy:updatePeriod>hourly</sy:updatePeriod>';
		//$this->xml.='  <sy:updateFrequency>1</sy:updateFrequency>';
	}
	private function rss_footer()
	{
		$this->xml.='</channel>';
		$this->xml.='</rss>';
	}
	private function rss_items()
	{
		$this->dbdatas=$this->db->get_results($this->info[0]);
		if(!empty($this->dbdatas)){
			$tmp=$this->info[1];
			foreach ($this->dbdatas as $this->o){
				$this->xml.=$this->$tmp();
			}
		}
	}
	public function get_rss()
	{
		$this->model_case_info();
		if(empty($this->info)) header("Location:index.php");
		$this->rss_header();
		$this->rss_items();
		$this->rss_footer();
		return $this->xml;
	}
	private function filtered_chars($str)
	{	
		$str=strip_tags($str);
		$pattern=array ( "'&(quot|#34);'i",   "'&(amp|#38);'i",   "'&(lt|#60);'i",   "'&(gt|#62);'i",   "'&(nbsp|#160);'i",   "'&ldquo;'",   "'&rdquo;'" );
    	$replace=array ("\"",  "&",  "<",  ">",  "",  "“",  "”" );
      	return preg_replace ($pattern, $replace, $str);
	}
	private function model_case_info()
	{
		$arr=array(
			'article'=>array(
				'SELECT * FROM '.$this->tb_pix.'article WHERE channelId='.$this->cid.' ORDER BY id ASC LIMIT '.$this->num,
				'article_rss_format'
			),
			'mapshow'=>array(
				'SELECT * FROM '.$this->tb_pix.'mapshow WHERE channelId='.$this->cid.' ORDER BY id ASC LIMIT '.$this->num,
				'mapshow_rss_format'
			),
			'product'=>array(
				'SELECT a.*,b.title FROM '.$this->tb_pix.'product a LEFT JOIN  '.$this->tb_pix.'menu b ON a.categoryId=b.id  WHERE a.channelId='.$this->cid.' ORDER BY a.id DESC LIMIT '.$this->num,
				'product_rss_format'
			),
			'download'=>array(
				'SELECT * FROM '.$this->tb_pix.'download WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'download_rss_format'
			),
			'jobs'=>array(
				'SELECT * FROM '.$this->tb_pix.'jobs WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'jobs_rss_format'
			),
			'video'=>array(
				'SELECT * FROM '.$this->tb_pix.'video WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'video_rss_format'
			),
			'guestbook'=>array(
				'SELECT * FROM '.$this->tb_pix.'guestbook WHERE auditing=1 AND channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'guestbook_rss_format'
			),
			'poll'=>array(
				'SELECT * FROM '.$this->tb_pix.'poll_category WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'poll_rss_format'
			),
			'linkers'=>array(
				'SELECT * FROM '.$this->tb_pix.'linkers WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
				'linkers_rss_format'
			),
			'list'=>array(
				'SELECT id,title,content,author,dtTime FROM '.$this->tb_pix.'list  WHERE channelId='.$this->cid.' ORDER BY id DESC LIMIT '.$this->num,
			'list_rss_format'
			),
			'calllist'=>array(
				'SELECT * FROM '.$this->tb_pix.'menu a WHERE INSTR((SELECT callId FROM '.$this->tb_pix.'calllist b WHERE b.id='.$this->cid.'),a.id)>0',
				'calllist_rss_format'
			)
		);
		$this->info=$arr[$this->model]?$arr[$this->model]:'';
	}
	private function article_rss_format()
	{	
		$item='<item>';
		$item.='	<title>'.$this->o->title.'</title>';
		$item.='	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;mdtp='.(++$this->o->pageId)) :($this->get_menuName().(++$this->o->pageId))).'/</link>';
		$item.='	<description><![CDATA['.$this->filtered_chars($this->o->description?$this->o->description:$this->o->content).']]></description>';
		$item.='	<content:encoded><![CDATA['.$this->o->content.']]></content:encoded>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function product_rss_format()
	{
		$item='<item>';
		$item.='	<title>'.$this->o->title.'</title>';
		$item.='	<category domain="'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;c='.$this->o->categoryId) : ($this->get_menuName().'category_'.$this->o->categoryId.'.html') ).'">'.$this->o->title.'</category>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;a=view&amp;r='.$this->o->id) : ($this->get_menuName().'product_'.$this->o->id.'.html') ).'</link>';
		$item.='	<description><![CDATA['.$this->filtered_chars($this->o->description?$this->o->description:$this->o->content).']]></description>';
		$item.='	<content:encoded><![CDATA['.$this->o->content.']]></content:encoded>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function download_rss_format()
	{
		$item='<item>';
		$item.='	<title>'.$this->o->title.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;a=view&amp;r='.$this->o->id) : ($this->get_menuName().'d'.$this->o->id.'.html') ).'</link>';
		$item.='	<description><![CDATA['.$this->filtered_chars($this->o->description).']]></description>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function jobs_rss_format()
	{
		$item='<item>';
		$item.='	<title>'.$this->o->title.'</title>';
		$item.='	<link>'.$this->http.'/?p='.$this->cid.'</link>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid) : $this->get_menuName() ).'</link>';
		$item.='	<description><![CDATA['.$this->filtered_chars($this->o->description).']]></description>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function video_rss_format()
	{
		$item='<item>';
		$item.='	<title>'.$this->o->title.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;a=view&amp;r='.$this->o->id) : ($this->get_menuName().'v'.$this->o->id.'.html') ).'</link>';
		$item.='	<description><![CDATA['.$this->filtered_chars($this->o->description).']]></description>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function guestbook_rss_format()//考虑删除sql限制 调用1条
	{
		$item='<item>';
		$item.='	<title>'.$this->o->name.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid) : $this->get_menuName() ).'</link>';
		$item.='	<description><![CDATA[主题：'.$this->filtered_chars($this->o->content).']]></description>';
		$item.='	<content:encoded><![CDATA[回复：'.$this->o->content1.']]></content:encoded>';
		$item.='	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.='</item>';
		return $item;
	}
	private function poll_rss_format()
	{
		$item='<item>';
		$item.= '	<title>'.$this->o->title.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;a=view&amp;r='.$this->o->id) : ($this->get_menuName().'poll_'.$this->o->id.'.html') ).'</link>';
		$item.= '	<description><![CDATA['.$this->filtered_chars($this->o->title).']]></description>';
		$item.= '	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.= '</item>';
		return $item;
	}
	private function linkers_rss_format()//是否删除,不支持重写
	{
		$item='<item>
				 <title>'.$this->o->title.'</title>
					<link>'.$this->http.'/?p='.$this->cid.'&amp;r='.$this->o->id.'</link>
					<description><![CDATA['.$this->filtered_chars($this->o->description).']]></description>
					<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>
			</item>';
		return $item;
	}
	private function list_rss_format()
	{
		$item='<item>';
		$item.= '	<title>'.$this->o->title.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->cid.'&amp;a=view&amp;r='.$this->o->id) : ($this->get_menuName().'n'.$this->o->id.'.html') ).'</link>';
		$item.= '	<description><![CDATA['.$this->filtered_chars($this->o->content).']]></description>';
		$item.='	<content:encoded><![CDATA['.$this->o->content.']]></content:encoded>';
		$item.= '	<pubDate>'.$this->format_time($this->o->dtTime).'</pubDate>';
		$item.= '</item>';
		return $item;
	}
	private function calllist_rss_format()
	{
		$item='<item>';
		$item.= '	<title>'.$this->o->title.'</title>';
		$item.= '	<link>'.$this->http.( !$this->rewrite ? ('/?p='.$this->o->id) : $this->get_menuName($this->o->id) ).'</link>';
		$item.= '	<description><![CDATA['.$this->filtered_chars($this->o->description).']]></description>';
		$item.= '	</item>';
		return $item;
	}
}