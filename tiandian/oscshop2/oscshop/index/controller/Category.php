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
use think\Db;
class Category extends HomeBase
{
    public function index()
    {    
		$param=input('param.');

		if(!$category=Db::name('category')->find((int)$param['id'])){
			$this->error('商品分类不存在！！');
		}

		$this->assign('SEO',['title'=>$category['name'].'-'.config('SITE_TITLE'),
		'keywords'=>$category['meta_keyword'],
		'description'=>$category['meta_description']]);
		
		if(isset($param['a'])){
			$this->assign('list',osc_goods()->get_attribute_goods_list(input('param.'),20));
		}else{
			$this->assign('list',osc_goods()->get_category_goods_list(input('param.'),20,'g.shipping,g.goods_id,g.name,g.image,g.price,gtc.category_id'));
		}			
		$this->assign('empty', '~~暂无数据');
		
		$this->assign('attribute', osc_goods()->get_category_attribute((int)input('param.id')));
		
		return $this->fetch();
   
    }
}
