<?php
/**
 * Tree 树型类(无限分类)
 * 
 * @example $tree= new Tree($result);
 * $arr=$tree->leaf(0);
 * $nav=$tree->navi(15);
 */
class Tree {
	private $result;
	private $tmp;
	private $arr;
	private $already = array();
	public $icon = array('&nbsp;', '├', '└', '　 ');
	/**
	 * 构造函数
	 * 
	 * @param array $result 树型数据表结果集
	 * @param array $fields 树型数据表字段，array(分类id,父id)
	 * @param integer $root 顶级分类的父id
	 */
	public function __construct($result, $fields = array('id', 'pid' ,'children'), $root = 0) {
		$this->result = $result;
		$this->fields = $fields;
		$this->root = $root;
		$this->handler();
	} 
	/**
	 * 树型数据表结果集处理
	 */
	private function handler() {
		foreach ($this->result as $node) {
			$tmp[$node[$this->fields[1]]][] = $node;
		} 
		krsort($tmp);
		for ($i = count($tmp); $i > 0; $i--) {
			foreach ($tmp as $k => $v) {
				if (!in_array($k, $this->already)) {
					if (!$this->tmp) {
						$this->tmp = array($k, $v);
						$this->already[] = $k;
						continue;
					} else {
						foreach ($v as $key => $value) {
							if ($value[$this->fields[0]] == $this->tmp[0]) {
								$tmp[$k][$key][$this->fields[2]] = $this->tmp[1];
								$this->tmp = array($k, $tmp[$k]);
							} 
						} 
					} 
				} 
			} 
			$this->tmp = null;
		} 
		$this->tmp = $tmp;
	} 
	/**
	 * 反向递归
	 */
	private function recur_n($arr, $id) {
		foreach ($arr as $v) {
			if ($v[$this->fields[0]] == $id) {
				$this->arr[] = $v;
				if ($v[$this->fields[1]] != $this->root) $this->recur_n($arr, $v[$this->fields[1]]);
			} 
		} 
	} 
	/**
	 * 正向递归
	 */
	private function recur_p($arr) {
		foreach ($arr as $v) {
			$this->arr[] = $v[$this->fields[0]];
			if ($v[$this->fields[2]]) $this->recur_p($v[$this->fields[2]]);
		} 
	} 
	/**
	 * 菜单 多维数组
	 * 
	 * @param integer $id 分类id
	 * @return array 返回分支，默认返回整个树
	 */
	public function leaf($id = null) {
		$id = ($id == null) ? $this->root : $id;
		return $this->tmp[$id];
	} 
	/**
	 * 导航 一维数组
	 * 
	 * @param integer $id 分类id
	 * @return array 返回单线分类直到顶级分类
	 */
	public function navi($id) {
		$this->arr = null;
		$this->recur_n($this->result, $id);
		krsort($this->arr);
		return $this->arr;
	} 
	/**
	 * 散落 一维数组
	 * 
	 * @param integer $id 分类id
	 * @return array 返回leaf下所有分类id
	 */
	public function leafid($id) {
		$this->arr = null;
		$this->arr[] = $id;
		$this->recur_p($this->leaf($id));
		return $this->arr;
	}
	/**
	 * 转为一维数组
	*/
	public function to1tree($id,$count=0){
		$child = $this->leaf($id);
		if(is_array($child)){
			foreach($child as $k=>$vo){
				$son=$vo[$this->fields[2]];
				unset($vo[$this->fields[2]]);
				$vo['level']=$count;
				$vo['space']=str_repeat($this->icon[3],$count).$this->icon[1];
				$arr[]=$vo;
				if(isset($son)){
					$arr=array_merge($arr,$this->to1tree($vo[$this->fields[0]],$count+1));
				}
			}
		}
		return $arr;
	}
	/**
	 * 获取select表单
	*/
	public function get_option($id, $selectId = 0,$spacer_addon = ''){
		$return = '';
		$child = $this->leaf($id);
		if(is_array($child)){
			$number = 1;
			$count = count($child);
			foreach($child as $v){
				if($number == $count){
					$icon = $this->icon[2];
					$spacer = $spacer_addon.$icon;
				}else{
					$icon = $this->icon[1];
					$spacer = $spacer_addon.$icon;
				}

				@extract($v);
				if(!is_array($selectId)){
					$selectId = explode(',', $selectId);
				}
				in_array($v[$this->fields[0]], $selectId) ? $_t_str ="<option value=\"{$id}\" selected=\"selected\">{$spacer}{$title}</option> " : $_t_str = "<option value=\"{$id}\">{$spacer}{$title}</option> ";
				$return .= $_t_str;

				if(isset($v[$this->fields[2]])){
					if($number == $count){
						$addon = $spacer_addon.$this->icon[3];
					}else{
						$addon = $spacer_addon.$this->icon[0];
					}
					$return .= $this->get_option($v[$this->fields[0]], $selectId, $addon);
				}
				$number++;
			}
		}
		return $return;
	}
} 