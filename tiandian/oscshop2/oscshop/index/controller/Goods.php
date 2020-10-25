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
 
namespace osc\index\controller;
use osc\common\controller\HomeBase;
class Goods extends HomeBase
{
    public function index()
    {    		
		
		if(!$list=osc_goods()->get_goods_info((int)input('param.id'))){
			$this->error('商品不存在！！');
		}
		
		$this->assign('SEO',['title'=>$list['goods']['name'].'-'.config('SITE_TITLE'),
		'keywords'=>$list['goods']['meta_keyword'],
		'description'=>$list['goods']['meta_description']]);
		
		osc_goods()->update_goods_viewed((int)input('param.id'));
		
		$this->assign('goods',$list['goods']);
		$this->assign('image',$list['image']);
		$this->assign('options',$list['options']);
		$this->assign('discount',$list['discount']);
		$this->assign('mobile_description',$list['mobile_description']);
		
		return $this->fetch();
   
    }
}
