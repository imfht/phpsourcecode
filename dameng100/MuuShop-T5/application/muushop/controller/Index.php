<?php

namespace app\muushop\controller;

use think\Controller;
use app\muushop\controller\Base;

class Index extends Base {

	protected $product_cats_model;
	protected $product_model;
	protected $coupon_model;
	protected $coupon_logic;
	protected $order_model;
	protected $order_logic;
	protected $user_coupon_model;
	protected $cart_model;
	protected $user_address_model;
	protected $list_num;


	function _initialize()
	{
		parent::_initialize();
		$this->product_cats_model    = model('muushop/MuushopProductCats');
		$this->product_model         = model('muushop/MuushopProduct');
		$this->coupon_model          = model('muushop/MuushopCoupon');
		$this->user_coupon_model     = model('muushop/MuushopUserCoupon');
		$this->coupon_logic          = model('muushop/MuushopCoupon', 'logic');
		$this->order_model           = model('muushop/MuushopOrder');
		$this->order_logic           = model('muushop/MuushopOrder', 'logic');
		$this->cart_model            = model('muushop/MuushopCart');
		$this->user_address_model    = model('muushop/MuushopUserAddress');
		//列表页每页显示商品数,通过后台设置
		$this->list_num = modC('MUUSHOP_LIST_NUM',16,'Muushop');

		//分类
		$map['status'] = 1;
		$items = $this->product_cats_model->getList($map,$order='sort asc');
		foreach($items as &$v){
			$v['link'] = url('muushop/Index/cats',['id'=>$v['id']]);
		}
		unset($v);
		$items = list_to_tree($items,$pk = 'id', 'parent_id', $child = 'items');
		
		$this->assign('cats',$items);
	}

	public function index()
	{
		return $this->fetch();
	}
	/**
	 * 商品列表
	 * @return [type] [description]
	 */
	public function cats()
	{
		//排序方式：
		//价格 price_desc,price_asc格式
		//销量 sell_cnt_desc,sell_cnt_asc
		//评论数 comment_cnt_desc,comment_cnt_asc
		//上架时间 create_time_desc create_time_asc
		$sort = input('get.sort','','text');
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
		
		$r = $this->list_num;
		$id = input('id',0,'intval');
		$all_son_id = $this->product_cats_model->getAllIdByPid($id);
		$map['cat_id'] = ['in',$all_son_id];
		$map['status']=1;
        /* 获取当前分类下列表 */
        $list = $this->product_model->getListByPage($map,$order,'*',$this->list_num);
        foreach($list as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
    		$val['ori_price'] = price_convert('yuan',$val['ori_price']);
        }
        unset($val);

        $this->assign('sort',$sort);//把当前的排序规则给前端
        $this->assign('sort_url',$sort_url);//排序的URL地址
		$this->assign('list', $list);
		// 渲染模板输出
		return $this->fetch();
	}


	/**
	 * 商品搜索
	 * @return [type]        [description]
	 */
	public function search(){

		$keyword = input('post.keyword','','text');

		$map['title'] = array('like',$keyword.'%');
		$map['status']=1;

		/* 获取列表 */
        $list = $this->product_model->getListByPage($map,'id desc,create_time desc','*',$this->list_num);

        foreach($list as &$val){
        	$val['price'] = price_convert('yuan',$val['price']);
        }
        unset($val);
        $this->assign('keyword',$keyword);
        $this->assign('list', $list);

		return $this->fetch();
	}

	/**
	 * 商品详情页
	 * @return [type] [description]
	 */
	public function product()
	{
		$id = input('id', '', 'intval');
		$product = $this->product_model->getDataById($id)->toArray();

		$product['price'] = sprintf("%.2f",$product['price']/100);
		$product['ori_price'] = sprintf("%.2f",$product['ori_price']/100);

		if($product['sku_table']){
			$minPrice= intval($product['price']);
			$maxPrice= intval($product['price']);

			foreach($product['sku_table']['info'] as &$val){
				$val['price'] = sprintf("%.2f",$val['price']/100);
				$val['ori_price'] = sprintf("%.2f",$val['price']/100);

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
				$product['price'] = $minPrice;
				$product['price'] = $product['price'];
			}else{
				$product['price']=$minPrice.'-'.$maxPrice;
			}
		}

		//处理商品图片
		$product['main_pic'] = getThumbImageById($product['main_img'],800,800);
		//处理预览图
		if($product['images']){
			$thumbArr = explode(',',$product['images']);
			$tmpArr = [];
			foreach($thumbArr as $v){
				$tmpArr['thumb100'][] = getThumbImageById($v,100,100);
				$tmpArr['thumb500'][] = getThumbImageById($v,500,500);
			}
			unset($v);
			$product['thumb'] = $tmpArr;
		}
		$product_sku = json_encode($product['sku_table']);
		
		//售后保障
		$service = modC('MUUSHOP_SHOW_SERVICE','','Muushop');
		//判断是否安装评价晒图插件
		if (class_exists('\addons\evaluate\Evaluate') && modC('MUUSHOP_COMMENT_ADDON_ABLE',0,'muushop')) {
        	$class= new \addons\evaluate\Evaluate;
            $evaluate = $class->info['title'];
            
        }else{
        	$evaluate = 0;
        }
        $this->assign('evaluate',$evaluate);
        
        //传递到前端
		$this->assign('product',$product);
		$this->assign('product_sku',$product_sku);
		$this->assign('service',$service);

		return $this->fetch();
	}
	
	/**
	 * 全部分类及子分类显示
	 * @return [type] [description]
	 */
	public function topcats(){
		//分类
		$map['status'] = 1;
		$items = $this->product_cats_model->getList($map,$order='sort asc');
		foreach($items as &$v){
			$v['link'] = url('muushop/Index/cats',array('id'=>$v['id']));
		}
		unset($v);
		$items = list_to_tree($items,$pk = 'id', 'parent_id', $child = 'items');
		
		$this->assign('cats',$items);

		return $this->fetch('_topcats');
	}

}
