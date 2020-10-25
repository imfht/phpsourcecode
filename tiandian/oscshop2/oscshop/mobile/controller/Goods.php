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
class Goods extends MobileBase{
	
	//商品详情
    function detail(){
    			
		cookie('jump_url',request()->url(true));
					
		if(!$list=osc_goods()->get_goods_info((int)input('param.id'))){
			$this->error('商品不存在！！');
		}
		
		$this->assign('SEO',['title'=>$list['goods']['name'].'-'.config('SITE_TITLE'),
		'keywords'=>$list['goods']['meta_keyword'],
		'description'=>$list['goods']['meta_description']]);
		
		osc_goods()->update_goods_viewed((int)input('param.id'));		
		
		$this->assign('top_title',$list['goods']['name']);
		$this->assign('goods',$list['goods']);
		$this->assign('image',$list['image']);
		$this->assign('options',$list['options']);
		$this->assign('discount',$list['discount']);
		$this->assign('mobile_description',$list['mobile_description']);		
		
		//数据量大的时候，此处会有性能问题，视情况进行修改
		$this->assign('related_goods',Db::name('goods')->where('status',1)->field('goods_id,image,name')->order('viewed desc')->limit('6')->select());	
			
		if(in_wechat())
		$this->assign('signPackage',wechat()->getJsSign(request()->url(true)));		
			
        return $this->fetch('detail');
    }
	//取得商品描述
	function get_description(){
		
		$this->assign('description',Db::name('goods_mobile_description_image')->where('goods_id',(int)input('param.id'))->order('sort_order asc')->select());
		
		exit($this->fetch());
	}
	//加入收藏
	function add_wish(){
		
		$goods_id=(int)input('post.id');
		
		if(!Db::name('goods')->where(array('goods_id'=>$goods_id,'status'=>1))->find()){
			return ['error'=>'产品不存在'];
		}
		
		$uid=user('uid');
		
		if(!$uid){
			return ['error'=>'请先登录'];
		}
		
		if(!Db::name('member_wishlist')->where(array('uid'=>$uid,'goods_id'=>$goods_id))->find()){
			Db::name('member_wishlist')->insert(array('uid'=>$uid,'goods_id'=>$goods_id,'date_added'=>date('Y-m-d H:i:s',time())));	
			Db::name('member')->where('uid',$uid)->setInc('wish',1);	
		}				
		
		return ['success'=>'收藏成功'];
	}

   function agent_share(){
		
		deal_agent_share();
	
		return $this->detail();		
	}
}
?>