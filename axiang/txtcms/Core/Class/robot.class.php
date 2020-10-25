<?php
/**
 * 搜索引擎爬行记录类
 */
class robot {
	function __construct(){
		$this->onnotes=true;
		$this->ban_robot=false;
		$this->file=config('ROBOT_FILE');
		$this->ip=$_SERVER['REMOTE_ADDR'];
		$this->url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$this->user_agent=$_SERVER ["HTTP_USER_AGENT"];
		$this->robotlist=array(
			"bot"=>"不明蜘蛛",
			"google"=>"Google",
			"Googlebot-Image"=>"Google图片",
			"mediapartners-google"=>"Google Adsense",
			"Adsbot-Google"=>"Google AdWords",
			"Baiduspider"=>"百度",
			"Baiduspider-image"=>"百度图片",
			"Baiduspider-video"=>"百度视频",
			"Baiduspider-news"=>"百度新闻",
			"Baiduspider-favo"=>"百度搜藏",
			"Baiduspider-cpro"=>"百度联盟",
			"Baiduspider-ads"=>"百度商务",
			"360Spider"=>"360搜素",
			"360Spider-Image"=>"360图片",
			"360Spider-Video"=>"360视频",
			"sosospider"=>"soso",
			"Sosoimagespider"=>"soso图片",
			"sogou"=>"Sogou",
			"yahoo"=>"Yahoo!",
			"MSNBot"=>"MSN",
			"MSNBot-Media"=>"MSN图片/媒体",
			"MSNBot-NewsBlogs"=>"MSN新闻/Blog",
			"ia_archiver"=>"Alexa",
			"iaarchiver"=>"Alexa",
			"sohu"=>"Sohu",
			"sqworm"=>"AOL",
			"yodaoBot"=>"Yodao",
			"iaskspider"=>"新浪爱问",
			"Scooter"=>"Altavista",
			"Lycos_Spider"=>"Lycos",
			"FAST-WebCrawler"=>"Alltheweb",
			"Slurp"=>"INKTOMI",
			"Gigabot"=>"gigablast.com",
			"BSpider"=>"日本蜘蛛",
		);
	}
	public function check(){
		foreach( $this->robotlist as $k =>$v ){
			if(stripos ($this->user_agent,$k)>-1) return $k;
		}
		return false;
	}
	public function ban($bot=array()){
		if($this->ban_robot && isset($bot[0]) && trim($bot[0])!='' && $this->check()){
			foreach( $bot as $v ){
				if($v==$this->check()){
					header('HTTP/1.1 403 Forbidden');
					exit;
				}
			}
		}
	}
	public function notes(){
		if($this->check() && $this->onnotes) {
			$newdata=implode('||',array($this->ip,$this->robotlist[$this->check()],$this->url,date("Y-m-d H:i:s")));
			if(!is_file($this->file) || filesize($this->file)==0 ){
				$data=$newdata."\r\n";
			}else{
				$arr=file($this->file);
				$arr=str_replace(array("\r\n","\r","\n",'###page###'),"",$arr);
				array_unshift($arr,$newdata);
				foreach($arr as $k=>$v){
					if(trim($arr[$k])=='') unset($arr[$k]);
				}
				$data='';
				$i=1;
				foreach( $arr as $k => $v ){
					if(trim($arr[$k])=='') continue;
					$data.=$arr[$k]."\r\n";
					$i++;
				}
			}
			write($this->file,$data);
		}
	}
}