<?php
namespace app\common\model;

use think\Model;

/**
* 
*/
class Category extends Model
{
	public $categorys;

	function initialize()
	{
		parent::initialize();
		if(!cache('categorys')){$this->cache_category();}
		$this->categorys = cache('categorys');
	}

	//添加栏目
	function add($params){
		$result = $this->isUpdate(false)->allowField(true)->save($params);
		if($result){
			$this->cache_category();
			return true;
		}else{
			return false;
		}
	}
	//修改栏目
	function edit($params){
		$result = $this->isUpdate(true)->allowField(true)->save($params);
		if($result){
			$this->cache_category();
			return true;
		}else{
			return false;
		}
	}

	
	//栏目排序
	function sort($params){
		$list = [];
		foreach ($params as $k => $v) {
			$list[] = ['id'=>$k, 'sort'=>$v];
		}
		$result = $this->saveAll($list);
		if($result){
			$this->cache_category();
			return true;
		}else{
			return false;
		}
	}


	

	//获取select分类┌├└──  │   ┣┏┗━━━┃
	function get_category_select($parent_id = 0,$is_root=true, $i = -1){
		$i++;
		if($i==0&&$is_root==true){
			$category_select = array(0 => '≡ 作为一级栏目 ≡');
		}else{
			$category_select = array();
		}
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			$category_select[$category_id] = str_repeat('│&nbsp;&nbsp;', $i-1).($i>0?'├─':'').$this->categorys[$category_id]['name'];
			$category_select = $category_select + $this->get_category_select($category_id,$is_root=true, $i);
		}	
		return $category_select;
	}

	//获取分类列表
	function get_category_list($parent_id = 0, $is_sep = false, $i = -1){
		$i++;
		$category_list = array();
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			if($is_sep){
				$this->categorys[$category_id]['sep_name'] = str_repeat('│&nbsp;&nbsp;', $i-1).($i>0?'├─':'').$this->categorys[$category_id]['name'];
			}
			$category_list[$category_id] = $this->categorys[$category_id];
			$category_list = $category_list + $this->get_category_list($category_id, $is_sep, $i);
		}	
		return $category_list;
	}
	
	//获取内容管理搜索用的最末级别的分类列表
	function get_last_category_list($parent_id = 0){
		$category_list = $this->categorys;
		unset($category_list[0]);
		foreach ($category_list as $k=>$v) {
			if($v['parent_id']){
				$category_list[$v['parent_id']]['is_search'] = 0;
			}
			if($v['model_id'] == 0){
				$category_list[$k]['is_search'] = 0;
			}
		}
		foreach ($category_list as $k=>$v) {
			if($v['is_search'] === 0){
				unset($category_list[$k]);
			}else{
				$category_list[$k]['href'] = ($v["model_id"] == 1)?url($v["model_code"].'/edit','category_id='.$v['id']):url($v["model_code"].'/index','category_id='.$v['id']);
			}
		}	
		return $category_list;
	}
	//修改内容分类用的分类列表（最末级别可选）
	function get_model_category_select_no_option($category_id){
		$model_id = $this->categorys[$category_id]['model_id'];
		$model_category_select = $this->get_model_category_select(0,$model_id,true);
		$option_str = '<option value="">请选择栏目</option>';
		foreach ($model_category_select as $k => $v) {
			$selected = ($category_id == $v['id']) ? true : false;
			if($v["disabled"]){
				$select_str .= '<option disabled>'.$v['sep_name'].'</option>';
			}else{
				$select_str .= '<option value="'.$v['id'].'" '.($selected ? ' selected="" ' : '').' >'.$v['sep_name'].'</option>';
			}
		}
		return $select_str;
	}

	//获取分类树
	function get_category_tree($parent_id = 0){
		$category_tree = array();
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			$this->categorys[$category_id]['children'] = $this->get_category_tree($category_id);
			$category_tree[$category_id] = $this->categorys[$category_id];	
		}	
		return $category_tree;
	}

	//根据分类id获取同模型选项列表
	function get_model_category_select($parent_id = 0,$model_id, $is_sep = false, $i = -1){
		$i++;
		$category_list = array();
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			if($is_sep){
				$this->categorys[$category_id]['sep_name'] = str_repeat('│&nbsp;&nbsp;', $i-1).($i>0?'├─':'').$this->categorys[$category_id]['name'];
			}
			$children = $this->get_model_category_select($category_id,$model_id, $is_sep, $i);
			if($children){
				$this->categorys[$category_id]['disabled'] = true;
				$category_list[$category_id] = $this->categorys[$category_id];
				$category_list = $category_list + $children;
			}elseif($model_id == $this->categorys[$category_id]['model_id']){
				$category_list[$category_id] = $this->categorys[$category_id];
				$category_list = $category_list + $children;
			}
		}	
		return $category_list;
	}

	//获取json分类树用于后台内容管理树
	/**
	* $parent_id 上级ID
	* $spread 是否展开
	*/
	function get_manage_tree($parent_id = 0, $spread = true, $target='main'){
		$category_tree = array();
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			if($this->categorys[$category_id]['model_id'] == 0){ continue; }
			$children['id'] = $category_id;
			$children['name'] = $this->categorys[$category_id]['name'];
			$children['children'] = $this->get_manage_tree($category_id, $spread, $target);
			if($children['children']){
				$children['spread'] = $spread;
			}else{
				$children['href'] = ($this->categorys[$category_id]["model_id"] == 1)?url($this->categorys[$category_id]["model_code"].'/edit','category_id='.$category_id):url($this->categorys[$category_id]["model_code"].'/index','category_id='.$category_id);
			}
			$category_tree[] = $children;	
		}	
		return $category_tree;
	}
	//根据id获取栏目url
	function get_category_url_by_id($category_id,$categorys=array()){
		if(!$categorys){ $categorys = $this->categorys;}
		if($categorys[$category_id]['url']){
			return $categorys[$category_id]['url'];
		}
		if($categorys[$category_id]['is_cover'] == 0 && isset($categorys[0][$category_id])){
			return $this->get_category_url_by_id($categorys[0][$category_id][0],$categorys);
		}
		if(!isset($categorys[0][$category_id]) && $categorys[$category_id]['model_id'] != 1){
			return url('index/'.$categorys[$category_id]["model_code"].'/lists','category_id='.$category_id);
		}
		return url('index/'.$categorys[$category_id]["model_code"].'/index','category_id='.$category_id);
	}
	//获取所有子分类id
	function get_category_ids($parent_id = 0){
		foreach ($this->categorys[0][$parent_id] as $category_id) {
			$category_ids[] = $category_id;
			$child_ids = $this->get_category_ids($category_id);
			if($child_ids){
				$category_ids = array_merge($category_ids,$child_ids);
			}
		}
		return $category_ids; 
	}
	//获取二级分类信息
	function get_second_categorys($parent_id = 0){
		if($this->categorys[0][$parent_id]){
			$category_ids = $this->categorys[0][$parent_id];
		}else{
			$parent_id = $this->categorys[$parent_id]['parent_id'];
			$category_ids = $this->categorys[0][$parent_id];
		}
		foreach ($category_ids as $category_id) {
			if($this->categorys[$category_id]['is_menu'] == 1){
				$second_categorys[$category_id] = $this->categorys[$category_id];
			}
		}
		return $second_categorys; 
	}
	//获取父分类ids
	function get_parent_ids($category_id){
		$parent_ids = array();
		if($this->categorys[$category_id]['parent_id']){
			$parent_id = $this->categorys[$category_id]['parent_id'];
			$parent_ids = array_merge($parent_ids,$this->get_parent_ids($parent_id));
			$parent_ids[] = $parent_id;
		}
		return $parent_ids;
	}
	//获取面包屑导航
	function breadcrumb($category_id){
		$breadcrumb_str = '<a href="/">首页</a>';
		$category_ids = $this->get_parent_ids($category_id);
		foreach ($category_ids as $id) {
			$breadcrumb_str .= '<a href="'.$this->get_category_url_by_id($id).'">'.$this->categorys[$id]['name'].'</a>';
		}
		$breadcrumb_str .= '<a href="'.$this->get_category_url_by_id($category_id).'">'.$this->categorys[$category_id]['name'].'</a>';
		return $breadcrumb_str;
	}

	//获取模版
	//$type 1 封面模版   2 列表页模版  3详情页模版
	function get_template($category_id,$type){
		$model_id = $this->categorys[$category_id]['model_id'];
		$model_info = cache('models')[$model_id];
		if($type == 1){
			$template = $this->categorys[$category_id]['index_template'];
			return $template = $template ? $template : $model_info['index_template'];
		}elseif ($type == 2) {
			$template = $this->categorys[$category_id]['list_template'];
			return $template = $template ? $template : $model_info['list_template'];
		}elseif ($type == 3) {
			$template = $this->categorys[$category_id]['show_template'];
			return $template = $template ? $template : $model_info['show_template'];
		}
	}

	//获取模型select
	function get_model_select(){
		$models = db('model')->select();
		$model_select = array();
		foreach ($models as $value) {
			$model_select[$value['id']] = $value['name'];
		}
		return $model_select;
	}

	//获取模型数组
	function get_models($model_id){
		$result = db('model')->select();
		$models = array();
		foreach ($result as $value) {
			$models[$value['id']] = $value;
		}
		if($model_id){
			return $models[$model_id];
		}
		return $models;
	}
	//更新模型缓存
	function cache_models(){
		$models = $this->get_models(false);
		return	cache('models', $models);
	}
	//更新栏目缓存
	function cache_category(){
		$models = $this->get_models(false);
		$result = db('category')->order('sort asc, id asc')->select();
		$categorys = array();
		foreach ($result as $k => $v) {
			if($v['model_id'] != 0){
				$v['model_name'] = $models[$v['model_id']]['name'];
				$v['model_code'] = $models[$v['model_id']]['tablename'];
			}
			$categorys[$v['id']] = $v;
		}
		$categorys_ids_arr = array();
		foreach ($categorys as $k => $v) {
			if($v['parent_id'] == 0){
				$categorys_ids_arr[0][] = $k;
			}
			foreach ($categorys as $k1 => $v1) {
				if($v1['parent_id'] == $k){
					$categorys_ids_arr[$k][] = $k1;
				}
			}
		}
		$categorys[0] = $categorys_ids_arr;
		foreach ($categorys as $k => $v) {
			if($k == 0){ continue; }
			$categorys[$k]['url'] = $this->get_category_url_by_id($k,$categorys);
		}
		
		return	cache('categorys', $categorys);
	}
}