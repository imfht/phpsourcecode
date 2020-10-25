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
use think\Db;
class Index extends MobileBase
{
 	function index(){
 		
		cookie('jump_url',request()->url(true));
		
		$this->assign('SEO',['title'=>config('SITE_TITLE'),'keywords'=>config('SITE_KEYWORDS'),'description'=>config('SITE_DESCRIPTION')]);
		
		$this->assign('flag','index');
		
		if(in_wechat())
		$this->assign('signPackage',wechat()->getJsSign(request()->url(true)));	
		
        return $this->fetch('index');
    }
    public function ajax_goods_list(){

        $page=input('param.page');//页码

        $limit =6;
		
        $list= osc_goods()->ajax_get_goods($page,$limit);
		
		if(isset($list)&&is_array($list)){
				foreach ($list as $k => $v) {				
					$list[$k]['image']=resize($v['image'], 250, 250);		
				}
		}
		
        return  $list;
    }
	//点击代理商分享的二维码后的操作
	//测试链接 http://域名/mobile/index/agent_share/osc_aid/2938rl8w0m
	function agent_share(){
		
		deal_agent_share();
	
		return $this->index();		
	}
	
	function add_share(){
		if(request()->isPost()){
			
			$data=input('post.');
			if(user('uid')){			
				Db::name('wechat_share')->insert(['uid'=>$data['uid'],'url'=>$data['url'],'type'=>$data['type'],'create_time'=>time()]);			
				storage_user_action(user('uid'),user('nickname'),config('FRONTEND_USER'),'分享了链接');
			}
		}
	}
	
}
