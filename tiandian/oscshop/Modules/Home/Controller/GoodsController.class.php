<?php
/**
 * oscshop 电子商务系统
 *
 * ==========================================================================
 * @link      http://www.oscshop.cn/
 * @copyright Copyright (c) 2015 oscshop.cn. 
 * @license   http://www.oscshop.cn/license.html License
 * ==========================================================================
 *
 * @author    李梓钿
 *
 */
namespace Home\Controller;

class GoodsController extends CommonController {
	
	//显示全部产品
	public function all(){
		
		$count=M('goods')->where(array('status'=>1))->count();
		
		$Page = new \Think\Page($count,C('FRONT_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql='SELECT goods_id,image,name,price FROM '.C('DB_PREFIX').'goods WHERE status=1 order by goods_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;	
		
		$list=M()->query($sql);
		
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		
			foreach ($list as $k => $v) {
				$list[$k]['goods_id']=$hashids->encode($v['goods_id']);				
				$list[$k]['image']=resize($v['image'], C('common_image_thumb_width'), C('common_image_thumb_height'));
		}
		
		$this->title='全部产品-';
		$this->category='全部产品';
		$this->meta_keywords=C('SITE_DESCRIPTION');
	    $this->meta_description='艺品瓷全部产品';
		
		
		$show=str_replace("/goods/all/p/","/products/", $show);
		
		$this->assign('empty','没有数据');// 赋值数据集
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出	
		
		$this->display();
	}
	
	//按分类显示产品
	public function category(){	
		
		$id=get_url_id('id');
		
		$sql='SELECT p.goods_id,p.image,p.name,p.price FROM '.C('DB_PREFIX').'goods p,'.		
		C('DB_PREFIX').'goods_to_category ptc '.
		' WHERE p.goods_id=ptc.goods_id AND p.status=1 AND ptc.category_id='.$id;
		
		$count=count(M()->query($sql));
		
		$Page = new \Think\Page($count,C('FRONT_PAGE_NUM'));
		
		$show  = $Page->show();// 分页显示输出	
		
		$sql.=' order by p.goods_id desc LIMIT '.$Page->firstRow.','.$Page->listRows;	
		
		$list=M()->query($sql);
		$hashids = new \Lib\Hashids(C('PWD_KEY'), C('URL_ID'));
		
		foreach ($list as $k => $v) {
				$list[$k]['goods_id']=$hashids->encode($v['goods_id']);
				$list[$k]['image']=resize($v['image'], C('common_image_thumb_width'), C('common_image_thumb_height'));
		}
		
		$category=M('goods_category')->find($id);		
		
		$this->title=$category['name'].'-';
		$this->category=$category['name'];
		$this->meta_keywords=$category['meta_keyword'];
	    $this->meta_description=$category['meta_description'];		
		
		$show=str_replace("/goods/category/id/","/category/", $show);
		
		$this->assign('empty','没有数据');// 赋值数据集
		$this->assign('list',$list);// 赋值数据集
		$this->assign('page',$show);// 赋值分页输出	
		
		$this->display('all');
	}
	

	
	//产品详情
    public function gshow(){    	

		$id=get_url_id('id');					
		
		$sql="select g.minimum,g.weight,g.weight_class_id,g.length_class_id,g.length,g.width,g.height,g.goods_id,g.model,g.location,g.image,g.price,g.name,gd.description,gd.meta_description,gd.meta_keyword from ".
		C('DB_PREFIX')."goods g,".C('DB_PREFIX')."goods_description gd where g.goods_id=gd.goods_id and g.goods_id=".$id;
		
		$goods=M()->query($sql);
		
		if(isset($goods)){		
			foreach ($goods as $k => $v) {			
				$goods[$k]['image_thumb']=resize($v['image'],C('goods_thumb_width'), C('goods_thumb_height'));
			}			
		}
		
		$sql="select image from ".C('DB_PREFIX')."goods_image where goods_id=".$id;
		$goods_image=M()->query($sql);
		if(isset($goods_image)){		
			foreach ($goods_image as $k => $v) {
				$goods_image[$k]['image_'.C('goods_thumb_width').'_'.C('goods_thumb_height')]=resize($v['image'], C('goods_thumb_width'), C('goods_thumb_height'));			
				$goods_image[$k]['thumb']=resize($v['image'], C('goods_gallery_thumb_width'), C('goods_gallery_thumb_height'));
			}			
		}
		
		$this->goods=$goods[0];
		$this->goods_image=$goods_image;
		
		$this->options=$this->get_goods_options($id);
		
		$this->discount=M('goods_discount')->where(array('goods_id'=>$id))->order('quantity ASC')->select();
		
		M()->execute("UPDATE " . C('DB_PREFIX') . "goods SET viewed = (viewed + 1) WHERE goods_id =".$id);	
		
		$this->title=$goods[0]['name'].'-';
		$this->meta_keywords=$goods[0]['meta_keyword'];
        $this->meta_description=$goods[0]['meta_description'];
		
	    $this->display();
	}

    public function get_goods_options($goods_id) {
		$goods_option_data = array();		
		$goods_option_query = M()->query("SELECT * FROM " . C('DB_PREFIX') . "goods_option po LEFT JOIN " 
		. C('DB_PREFIX') . "option o ON po.option_id = o.option_id WHERE po.goods_id =".(int)$goods_id);				
		foreach ($goods_option_query as $goods_option) {
			$goods_option_value_data = array();					
			$goods_option_value_query = M()->query("SELECT pov.*,ov.value_name FROM " . C('DB_PREFIX') 
			. "goods_option_value pov LEFT JOIN ". C('DB_PREFIX') 
			."option_value ov ON pov.option_value_id=ov.option_value_id"
			." WHERE pov.goods_option_id = '" 
			. (int)$goods_option['goods_option_id'] . "'");			
			
			foreach ($goods_option_value_query as $goods_option_value) {
				$goods_option_value_data[] = array(
					'goods_option_value_id' => $goods_option_value['goods_option_value_id'],
					'option_value_id'         => $goods_option_value['option_value_id'],
					'name'					  =>$goods_option_value['value_name'],
					'image'					  =>isset($goods_option_value['image'])?$goods_option_value['image']:'',
					'price'                   =>'￥'.$goods_option_value['price'],
					'price_prefix'            => $goods_option_value['price_prefix'],
			
				);
			}
				
			$goods_option_data[] = array(
				'goods_option_id'    => $goods_option['goods_option_id'],
				'option_id'            => $goods_option['option_id'],
				'name'                 => $goods_option['name'],
				'type'                 => $goods_option['type'],
				'option_value'         => $goods_option_value_data,
				'required'             => $goods_option['required']				
			);
		}
	
		return $goods_option_data;
	}
}