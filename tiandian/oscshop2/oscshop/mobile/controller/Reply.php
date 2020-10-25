<?php
/**
 * oscshop2 B2C电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015-2016 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
 
namespace osc\mobile\controller;
use osc\common\controller\Base;
use think\Db;
use wechat\Wechat;
class Reply extends Base
{
 	public function reply(){    	
		 
		 $wechat =wechat();		
		 $wechat->valid();
 		 $type = $wechat->getRev()->getRevType();
		 $content=$wechat->getRev()->getRevContent();
		 
		 $_event_key=$wechat->getRev()->getRevEvent();
		 
		 	switch ($_event_key['key']) {
						case 'get_last_news':
							$reply=Db::name('wechat_news_reply')->where(array('status'=>1))->order('nr_id desc')->limit('10')->select();
							
							foreach ($reply as $k => $v) {
								$news[$k]['Title']=$v['title'];
								$news[$k]['Description']=$v['description'];
								$news[$k]['PicUrl']=request()->domain().'/uploads/'.$v['thumb'];
								$news[$k]['Url']=request()->domain().'/mobile/article/index/id/'.$v['nr_id'];
							}
							
							if($news){
								$wechat->news($news)->reply();
							}
						break;					
					
			}
		 
		 	switch ($type) {
            
            case Wechat::MSGTYPE_TEXT:	
				
				$key=Db::name('wechat_rule')->where(array('keyword'=>$content,'status'=>1))->find();
				
				if($key){					
					
					switch ($key['module']) {
						case 'text':
							$reply=Db::name('wechat_text_reply')->where(array('rid'=>$key['rid'],'status'=>1))->find();
							if($reply){
								$wechat->text($reply['content'])->reply();								
							}
						break;
						
						case 'news':
							$reply=Db::name('wechat_news_reply')->where(array('rid'=>$key['rid'],'status'=>1))->find();
							if($reply){
								$wechat->news(
								array(
									   	"0"=>array(
									   		'Title'=>$reply['title'],
									   		'Description'=>$reply['description'],									   		
									   		'PicUrl'=>request()->domain().'/uploads/'.$reply['thumb'],									   		
									   		'Url'=>request()->domain().'/mobile/article/index/id/'.$reply['nr_id']
											)
									)
								)->reply();
							}
						break;
						default:
							$wechat->text('您好，欢迎您关注'.config('SITE_NAME').'的公众平台！')->reply();      			 
          				 break;
						
					}
					
				}else{
					 $wechat->replyText('您好，欢迎您关注'.config('SITE_NAME').'的公众平台！');
				}
				
           		break;
           		
				default:
					$wechat->text('您好，欢迎您关注'.config('SITE_NAME').'的公众平台！')->reply();      			 
          		 break;
        
		 }
    }
	
}
