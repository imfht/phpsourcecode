<?php

namespace Muushop\Controller;

use Think\Controller;
use Com\TPWechat;
use Com\WechatAuth;

class IndexController extends PublicController {
	protected $product_cats_model;
	protected $product_model;
	protected $coupon_model;
	protected $coupon_logic;
	protected $message_model;
	protected $user_coupon_model;
	protected $product_comment_model;
	protected $list_num;


	function _initialize()
	{
		parent::_initialize();
		$this->product_cats_model = D('Muushop/MuushopProductCats');
		$this->product_model      = D('Muushop/MuushopProduct');
		$this->coupon_model       = D('Muushop/MuushopCoupon');
		$this->message_model      = D('Muushop/MuushopMessage');
		$this->user_coupon_model  = D('Muushop/MuushopUserCoupon');
		$this->coupon_logic       = D('Muushop/MuushopCoupon', 'Logic');
		$this->order_model        = D('Muushop/MuushopOrder');
		$this->product_comment_model = D('Muushop/MuushopProductComment');
		//列表页每页显示商品数
		$this->list_num = modC('MUUSHOP_LIST_NUM',16,'Muushop');

		//分类
		$map['status'] = 1;
		$items = $this->product_cats_model->getList($map,$order='sort asc');
		foreach($items as &$v){
			$v['link'] = U('Muushop/Index/cats',array('id'=>$v['id']));
		}
		unset($v);
		$items = list_to_tree($items,$pk = 'id', 'parent_id', $child = 'items');
		$this->assign('cats',$items);
	}

	public function index()
	{
		$this->display();
	}

	public function cats()
	{
		//排序方式：
		//价格 price_desc,price_asc格式
		//销量 sell_cnt_desc,sell_cnt_asc
		//评论数 comment_cnt_desc,comment_cnt_asc
		//上架时间 create_time_desc create_time_asc
		$sort = I('get.sort','','text');
		//严谨点做个判断，这种写法有点醉了~~
		if( $sort == 'price_desc'     || 
			$sort=='price_asc'        || 
			$sort=='sell_cnt_desc'    || 
			$sort=='sell_cnt_asc'     ||
			$sort=='comment_cnt_desc' || 
			$sort=='comment_cnt_asc'  ||
			$sort=='create_time_desc' || 
			$sort=='create_time_asc'
		){
			$sort_arr = explode('_',$sort);
			if($sort_arr[0]==='price'){
				$order = $sort_arr[0].' '.$sort_arr[1];
			}else{
				$order = $sort_arr[0].'_'.$sort_arr[1].' '.$sort_arr[2];
			}
		}else{
			$sort='all';
			$order = 'id desc,create_time desc';
		}

		$sort_url = $this->product_cats_model->sort_url($sort);//获取排序完整URL

		$page = I('page',1,'intval');
		$r = $this->list_num;
		$id = I('id',0,'intval');
		$all_son_id = $this->product_cats_model->get_all_cat_id_by_pid($id);
		$map['cat_id'] = array('in',$all_son_id);
		$map['status']=1;
        /* 获取当前分类下列表 */
        list($list,$totalCount) = $this->product_model->getListByPage($map,$page,$order,'*',$r);
        foreach($list as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
    		$val['ori_price'] = price_convert('yuan',$val['ori_price']);
        }
        unset($val);

        $this->assign('sort',$sort);//把当前的排序规则给前端
        $this->assign('sort_url',$sort_url);//排序的URL地址
		$this->assign('list', $list);
        $this->assign('totalCount',$totalCount);
        $this->assign('r',$r);
		$this->display();
	}


	/**
	 * 商品搜索
	 * @param  integer $page [description]
	 * @param  integer $r    [description]
	 * @return [type]        [description]
	 */
	public function search($page = 1, $r = 24){

		$keyword = I('post.keyword','','text');

		$map['title'] = array('like',$keyword.'%');
		$map['status']=1;

		/* 获取当前分类下列表 */
        list($list,$totalCount) = $this->product_model->getListByPage($map,$page,'id desc,create_time desc','*',$r);

        foreach($list as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
        }
        unset($val);
        $this->assign('keyword',$keyword);
        $this->assign('list', $list);
        $this->assign('totalCount',$totalCount);
		$this->display();
	}


	public function product()
	{
		$id = I('id', '', 'intval');
		$product = $this->product_model->get_product_by_id($id);
		$sharedata = array(
			'title'=>$product['title'],
			'imgUrl'=>'http://'.$_SERVER['HTTP_HOST'].pic($product['main_img']),
		);

		if($product['sku_table']){
			$minPrice= intval($product['price']);
			$maxPrice= intval($product['price']);

			foreach($product['sku_table']['info'] as $val){
				if($val['price']==''){
					$val['price']= intval($product['price']);
				}
				if($val['price']<=$minPrice){
					$minPrice = $val['price'];
				}
				if($val['price']>=$maxPrice){
					$maxPrice = $val['price'];
				}
			}
			unset($val);
			if($minPrice==$maxPrice){
					$product['price']=$minPrice;
					$product['price'] = $product['price'];
			}else{
					$minPrice = sprintf("%.2f",$minPrice/100);
					$maxPrice = sprintf("%.2f",$maxPrice/100);
					$product['price']=$minPrice.'-'.$maxPrice;
			}

			foreach($product['sku_table']['info'] as &$val){
				$val['price'] = sprintf("%.2f",$val['price']/100);
				$val['ori_price'] = sprintf("%.2f",$val['ori_price']/100);
			}
			unset($val);

			$product['ori_price'] = sprintf("%.2f",$product['ori_price']/100);
		}
		$product['price'] = sprintf("%.2f",$product['price']/100);
		$product['ori_price'] = sprintf("%.2f",$product['ori_price']/100);


		//处理商品图片
		$product['main_pic'] = getThumbImageById($product['main_img'],800,800);
		//处理预览图
		if($product['images']){
			$thumbArr = explode(',',$product['images']);
			$product['thumb'] = array();
			$product['thumb100'] = array();
			$product['thumb500'] = array();
			foreach($thumbArr as &$v){
				$product['thumb100'][] = getThumbImageById($v,100,100);
				$product['thumb500'][] = getThumbImageById($v,500,500);
			}
			unset($v);
		}
		$product_sku = json_encode($product['sku_table']);
		//售后保障
		$service = modC('MUUSHOP_SHOW_SERVICE','','Muushop');
		
		$this->assign('product',$product);
		$this->assign('product_sku',$product_sku);
		$this->assign('service',$service);
		$this->assign('sharedata', $sharedata);
		$this->display();
	}

}
