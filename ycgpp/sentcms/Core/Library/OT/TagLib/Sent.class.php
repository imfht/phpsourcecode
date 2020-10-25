<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace OT\TagLib;
use Think\Template\TagLib;
/**
 * Sent系统标签库
 */
class Sent extends TagLib{

	// 标签定义
	protected $tags   =  array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'nav'       =>  array('attr' => 'field,name', 'close' => 1), //获取导航
		'list'      =>  array('attr' => 'table,where,order,limit,id,sql,field,key','level'=>3),//列表
		'document'  =>  array('attr' => 'model,field,limit,id,field,key','level'=>3),
		'page'		=>	array('attr' => 'table,limit' , 'close' => 0),//分页
	);

	/* 导航列表 */
	public function _nav($tag, $content){
		$field  = empty($tag['field']) ? 'true' : $tag['field'];
		$tree   =   empty($tag['tree'])? true : false;
		$parse  = $parse   = '<?php ';
		$parse .= '$__NAV__ = M(\'Channel\')->field('.$field.')->where("status=1")->order("sort")->select();';
		if($tree){
			$parse .= '$__NAV__ = list_to_tree($__NAV__, "id", "pid");';
		}
		$parse .= '?>{volist name="__NAV__" id="'. $tag['name'] .'"}';
		$parse .= $content;
		$parse .= '{/volist}';
		return $parse;
	}

	public function _list($tag, $content){
		$table     = !empty($tag['table']) ? $tag['table']:'';
		$field     = empty($tag['field']) ? 'true' : $tag['field'];
		$key       = empty($tag['key']) ? 'key' : $tag['key'];
		$id        = empty($tag['id']) ? 'item' : $tag['id'];
		$limit        = empty($tag['limit']) ? 20 : $tag['limit'];
		if (!$table) {
			return $content;
		}
		$parse  = $parse   = '<?php ';
		$parse .= '$__LIST__ = D(\''.$table.'\')->field('.$field.')->limit('.$limit.')->select();';
		$parse .= '?>{volist name="__LIST__" id="'. $id .'"}';
		$parse .= $content;
		$parse .= '{/volist}';
		return $parse;
	}

	public function _document($tag, $content){
		$model     = !empty($tag['model']) ? $tag['model']:'';
		$field     = empty($tag['field']) ? 'true' : $tag['field'];
		$key       = empty($tag['key']) ? 'key' : $tag['key'];
		$id        = empty($tag['id']) ? 'item' : $tag['id'];
		$limit        = empty($tag['limit']) ? 20 : $tag['limit'];

		$where = 'model_id='.$model;

		$parse  = $parse   = '<?php ';
		$parse .= '$__LIST__ = D(\'document\')->where(\''.$where.'\')->field('.$field.')->limit('.$limit.')->order(\'id desc\')->select();';
		$parse .= '?>{volist name="__LIST__" id="'. $id .'"}';
		$parse .= $content;
		$parse .= '{/volist}';
		return $parse;
	}

	/*分页*/
	public function _page($tag , $content){
		$table = empty($tag['table']) ? '' : $tag['table'];
		$limit = empty($tag['limit']) ? 10 : $tag['limit'];
		$count = D($table)->count();
		$page = new \Think\Page($count , $limit);
		$parse  = $parse   = '<?php ';
		$parse .= 'echo $page->show()';
		$parse .= '?>';
		return $parse;
	}
}