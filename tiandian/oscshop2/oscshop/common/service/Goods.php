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
 * 公共数据获取
 * 
 */
namespace osc\common\service;
use think\Db;
class Goods{
	
	/**
     * object 对象实例
     */
    private static $instance;
	
	//禁外部实例化
	private function __construct(){}
	
	//单例模式	
	public static function getInstance(){    
        if (!(self::$instance instanceof self))  
        {  
            self::$instance = new self();  
        }  
        return self::$instance;  
    }
	//禁克隆
	private function __clone(){}  
	
	//取得属性关联商品
	public function get_attribute_goods_list($filter,$page_num=10){	
		
		$attribute_value_id= explode_build_string($filter['a']);
		
		//名称筛选
		if(isset($filter['name'])){
			$map['Goods.name']=['like',"%".$filter['name']."%"];	
			$query['name']=urlencode($filter['name']);	
		}
		
		$query['a']=urlencode($attribute_value_id);	
		
		$map['GoodsToCategory.category_id']=['eq',(int)$filter['id']];
		$map['Goods.status']=['eq',1];	
		$map['GoodsAttribute.attribute_value_id']=['in',$attribute_value_id];
		
		return Db::view('Goods','goods_id,image,name,price,shipping')
		->view('GoodsAttribute','attribute_value_id','Goods.goods_id=GoodsAttribute.goods_id')
		->view('GoodsToCategory','category_id','GoodsToCategory.goods_id=Goods.goods_id')
		->where($map)
		->order('goods_id desc')
		->paginate($page_num,false,['query'=>$query]);
		
	}
		
	/**
	 * 根据条件取得商品列表(goods表连接goods_to_category表)
	 * @param array $filter 条件
	 * @param string $page_num 数据量
	 * @param string $field 取出字段
	 * @return object(think\paginator\Collection) 
	 */
	public function get_category_goods_list($filter,$page_num=10,$field='*'){
		
		$map=[];
		$query=[];
		
		if(isset($filter['type'])){
			$query['type']=urlencode($filter['type']);	
		}
		//名称筛选
		if(isset($filter['name'])){
			$map['g.name']=['like',"%".$filter['name']."%"];	
			$query['name']=urlencode($filter['name']);	
		}
		//后台台分类商品搜索
		if(isset($filter['category'])){
			$map['gtc.category_id']=['eq',(int)$filter['category']];	
			$query['category']=urlencode($filter['category']);		
		}
		//前台分类商品搜索
		if(isset($filter['id'])){
			$map['gtc.category_id']=['eq',(int)$filter['id']];	
		}
		//状态筛选
		if(isset($filter['status'])){	
			$map['g.status']=['eq',(int)$filter['status']];	
			$query['status']=urlencode($filter['status']);
		}else{
			$map['g.status']=['eq',1];	
		}		
		
		return Db::name('goods')->alias('g')->field($field)		
		->join('__GOODS_TO_CATEGORY__ gtc','g.goods_id = gtc.goods_id')
		->where($map)->order('g.goods_id desc')
		->paginate($page_num,false,['query'=>$query]);
		
	}

	/**
	 * 根据条件取得商品列表
	 * @param array $filter 条件
	 * @return object(think\paginator\Collection)  
	 */
	public function get_goods_list($filter,$page_num=10){
		
		$map=[];
		
		if(isset($filter['name'])){
			$map['name']=['like',$filter['name']];		
		}

		if(isset($filter['status'])){	
			$map['status']=['eq',$filter['status']];	
		}
		
		$map['goods_id']=['GT','0'];
		
		return Db::name('goods')
		->where($map)->order('goods_id desc')
		->paginate($page_num);
		
	}
	
	public function ajax_get_goods($page_num,$limit_num){		
		//页码
		$page=$page_num;
		//数据量
		$limit = ((int)$limit_num * (int)$page) . ",".(int)$limit_num;
					
		$sql='SELECT goods_id,image,price,name FROM '.config('database.prefix').'goods WHERE status=1 ORDER BY goods_id LIMIT '.$limit;
		
		$list=Db::query($sql);				
		
		return $list;			
	}
	//取得商品选项
	public function get_goods_options($goods_id) {
		
		$goods_option_data = [];
		
		$goods_option_query = Db::query("SELECT * FROM " . config('database.prefix') . "goods_option go LEFT JOIN " 
		. config('database.prefix') . "option o ON go.option_id = o.option_id WHERE go.goods_id =".(int)$goods_id);
		
		foreach ($goods_option_query as $goods_option) {
			$goods_option_value_data = array();	
			
			$goods_option_value_query = Db::query("SELECT gov.*,ov.value_name FROM " .config('database.prefix') 
			. "goods_option_value gov LEFT JOIN ". config('database.prefix') 
			."option_value ov ON gov.option_value_id=ov.option_value_id"
			." WHERE gov.goods_option_id =" 
			. (int)$goods_option['goods_option_id']);	
			
			foreach ($goods_option_value_query as $goods_option_value) {
				$goods_option_value_data[] = array(
					'goods_option_value_id'   => $goods_option_value['goods_option_value_id'],
					'option_value_id'         => $goods_option_value['option_value_id'],
					'name'					  => $goods_option_value['value_name'],
					'quantity'                => $goods_option_value['quantity'],
					'subtract'                => $goods_option_value['subtract'],
					'price'                   => $goods_option_value['price'],
					'goods_price'             => $goods_option_value['price'],
					'price_prefix'            => $goods_option_value['price_prefix'],
					'image'			  		  => $goods_option_value['image'],
                    'option_value_image'      => $goods_option_value['image'],//复制时候需要
					'weight'                  => $goods_option_value['weight'],
					'weight_prefix'           => $goods_option_value['weight_prefix']					
				);
			}
				
			$goods_option_data[] = array(
				'goods_option_id'      => $goods_option['goods_option_id'],
				'option_id'            => $goods_option['option_id'],
				'name'                 => $goods_option['name'],
				'type'                 => $goods_option['type'],					
				'option_value'         => $goods_option['name'],
				'required'             => $goods_option['required'],
				'goods_option_value'   =>  $goods_option_value_data,				
			);
		}
	
		return $goods_option_data;
	}

	public function get_option_values($option_id) {
		$option_value_data = [];
		
		$option_value_query = Db::query("SELECT * FROM " 
		. config('database.prefix') . "option_value ov LEFT JOIN " 
		. config('database.prefix') . "option o ON (ov.option_id = o.option_id) WHERE ov.option_id =" 
		. (int)$option_id);
				
		foreach ($option_value_query as $option_value) {
			$option_value_data[] = array(
				'option_value_id' => $option_value['option_value_id'],
				'name'            => $option_value['name'],
				'value'           => $option_value['value_name'],				
				'sort_order'      => $option_value['value_sort_order']
			);
		}
		
		return $option_value_data;
	}	
	
	//取得分类key值
	public function get_category_info($id,$key){	 	
		if (!$category = cache('category_info')) {		
			$list=Db::name('category')->select();			
			$cat=[];			
			foreach ($list as $k => $v) {
				$cat[$v['id']]=$v;
			}			
			cache('category_info', $cat);				
			$category=$cat;
		}
		return $category[$id][$key];		
	}
	
	//取得商品分类树形结构
	public function get_category_tree(){	
		$tree=new \oscshop\Tree();	
		return $tree->toFormatTree(Db::name('category')->field('id,pid,name')->select(),'name');
	}

	//取得商品分类
	public function get_goods_category(){
			
		if(!$home_goods_category= cache('home_goods_category')){
			$home_goods_category=list_to_tree(Db::name('category')->field('id,pid,name')->order('sort_order asc')->select());
			cache('home_goods_category', $home_goods_category);
		}	
			
		return $home_goods_category;
	}
	//取得首页展示商品
	public function get_home_goods_list(){
		
		if (!$home_goods_list= cache('home_goods_list')) {
		
			$home_goods_list=Db::name('goods')->where('status',1)->order('goods_id desc')->limit(20)->select();
		
			cache('home_goods_list', $home_goods_list);
			
		}
		return $home_goods_list;
	}
	//取得商品分类属性
	public function get_category_attribute($cid){
		
		$attribute=Db::query('select * from '.config('database.prefix').'category_to_attribute cta,'.config('database.prefix').'attribute_value av where cta.attribute_id=av.attribute_id and cta.cid='.$cid);
		$attribute1=[];
		foreach ($attribute as $key => $value) {
			$attribute1[$value['name']][]=$value;
		}
		
		return $attribute1;
	}
	//取得商品分类品牌
	public function get_category_brand($cid){
		
		return Db::query('select * from '.config('database.prefix').'category_to_brand ctb,'.config('database.prefix').'brand b where ctb.brand_id=b.brand_id and ctb.cid='.$cid);
		
	}
	//商品详情信息
	public function get_goods_info($goods_id){
		
		if(!$goods=Db::name('goods')->alias('g')->join('__GOODS_DESCRIPTION__ gd','g.goods_id = gd.goods_id')->where('g.goods_id',$goods_id)->find()){
			return false;
		}
		return [
			'goods'=>$goods,
			'image'=>Db::name('goods_image')->where('goods_id',$goods_id)->select(),
			'options'=>$this->get_goods_options($goods_id),
			'discount'=>Db::name('goods_discount')->where('goods_id',$goods_id)->order('quantity ASC')->select(),
			'mobile_description'=>Db::name('goods_mobile_description_image')->where('goods_id',$goods_id)->order('sort_order asc')->select()
		];
	}
	//更新商品点击量
	public function update_goods_viewed($goods_id){
		Db::execute("UPDATE ".config('database.prefix')."goods SET viewed = (viewed + 1) WHERE goods_id =".$goods_id);	
	}
}
