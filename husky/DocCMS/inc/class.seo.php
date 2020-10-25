<?php
/**
 * SEO标签
 * @copyright DOCCMS
 */
class seo{ 
	public $title='';
	public $keywords=''; 
	public $description='';

	public $channelTitle='';
	public $channelKeywords=''; 
	public $channelDescription='';

	//public $siteTitle='';
	//public $siteKeywords=''; 
	//public $siteDescription='';
	
	static $_instance=null;
	private function __construct(){
		global $params,$menu_arr;
		//rss模块等无表模块 需要设计标准
		if($params['args']>0 && $params['action']!='get_rss'){
			global $db,$menu_arr;
			$result=$db->get_row("SELECT * FROM ".TB_PREFIX.$menu_arr['type']." WHERE id=".$params['args'] );
			
			$this->title		= $result->title;
			$this->keywords 	= $result->keywords;
			$this->description  = $result->description;
			if($menu_arr['type']== 'guestbook' && (GUESTBOOKAUDITING && $result->isPublic== '1'))
			{
				$this->title		= $result->content;
				$this->description  = $result->content1;
			}
			
			$this->channelTitle		   = $menu_arr['title'];
			$this->channelKeywords 	   = $menu_arr['keywords'];
			$this->channelDescription  = $menu_arr['description'];
			
		}else if($params['id']>0 && $params['model']=='article' ){
			global $db,$menu_arr;
			$sql = "SELECT * FROM ".TB_PREFIX.$menu_arr['type']." WHERE channelId=".$params['id'];
			$sb = new sqlbuilder('mdt',$sql,'pageId ASC',$db,1,true,URLREWRITE ? '/' : './');

			$this->title		= $sb->results[0]['title'];
			$this->keywords 	= $sb->results[0]['keywords'];
			$this->description  = $sb->results[0]['description'];
			
			$this->channelTitle		   = $menu_arr['title'];
			$this->channelKeywords 	   = $menu_arr['keywords'];
			$this->channelDescription  = $menu_arr['description'];
		}else{
			$this->channelTitle		   = $menu_arr['title'];
			$this->channelKeywords 	   = $menu_arr['keywords'];
			$this->channelDescription  = $menu_arr['description'];			
		}
	}
	public static function join($var,$join='-'){//功能同 /inc/function.php中的函数 string_join($var,$join='-')
		return $var?$var.' '.$join.' ':'';
	}
	private function __clone(){
		
	}
	public static function getInstance()
	{
		if(self::$_instance == null)
		{
			self::$_instance = new seo();
		}
		return self::$_instance;
	}
	static function getTitle(){
		return self::getInstance()->title;
	}
	static function getKeywords(){
		return self::getInstance()->keywords;
	}
	static function getDescription(){
		return self::getInstance()->description;
	}
	
	static function getChannelTitle(){
		return self::getInstance()->channelTitle;
	}
	static function getChannelKeywords(){
		return self::getInstance()->channelKeywords;
	}
	static function getChannelDescription(){
		return self::getInstance()->channelDescription;
	}
	
 }