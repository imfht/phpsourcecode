<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace com;

/**
 * @title 自定义标签库
 * @description 自定义标签库
 */
class Sent extends \think\template\TagLib {

	// 标签定义
	protected $tags   =  array(
		// 标签定义： attr 属性列表 close 是否闭合（0 或者1 默认1） alias 标签别名 level 嵌套层次
		'nav'       => array('attr' => 'name,pid', 'close' => 1), //获取导航
		'doc'       => array('attr' => 'model,field,limit,id,field,key,name','level'=>3),
		'link'		=> array('attr' => 'type,limit' , 'close' => 1),//友情链接
	);

	public function tagnav($tag, $content){
		$type  = isset($tag['type']) ? $tag['type'] : '';
		$pid  = isset($tag['pid']) ? $tag['pid'] : '';
		$tree   =   isset($tag['tree']) ? $tag['tree'] : false;
		$parse  = '<?php ';
		$parse .= '$__NAV__ = \\app\\model\\Channel::getChannelList('.$type.', "'.$pid.'", '.$tree.');';
		$parse .= 'foreach ($__NAV__ as $key => $'.$tag['name'].') {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}

	public function tagdoc($tag, $content){
		$model     = !empty($tag['model']) ? $tag['model']:'';
		$cid     = isset($tag['cid']) ? (int) $tag['cid'] : 0;
		$field     = empty($tag['field']) ? '*' : $tag['field'];
		$limit        = isset($tag['limit']) ? (int) $tag['limit'] : 20;
		$order        = empty($tag['order']) ? 'id desc' : $tag['order'];
		$name = isset($tag['name']) ? $tag['name'] : 'item';

		$parse  = $parse   = '<?php ';
		$parse .= '$__LIST__ = \\app\\model\\Document::getDocumentList("'.$model.'", '.$cid.', '.$limit.', "'.$order.'", "'.$field.'");';
		$parse .= 'foreach ($__LIST__ as $key => $'.$name.') {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}

	public function taglink($tag, $content){
		$type     = !empty($tag['type']) ? $tag['type'] : '';
		$limit     = !empty($tag['limit']) ? $tag['limit'] : '';
		$field     = empty($tag['field']) ? '*' : $tag['field'];
		$limit        = empty($tag['limit']) ? 20 : $tag['limit'];
		$order        = empty($tag['order']) ? "id desc" : $tag['order'];

		$where[] = "status > 0";
		if ($type) {
			$where[] = "ftype = " . $type;
		}
		$map = implode(" and ", $where);

		$parse  = $parse   = '<?php ';
		$parse .= '$__LIST__ = \\app\\model\\Link::where(\''.$map.'\')->field(\''.$field.'\')->limit(\''.$limit.'\')->order(\''.$order.'\')->select();';
		$parse .= 'foreach ($__LIST__ as $key => $'.$tag['name'].') {';
		$parse .= '?>';
		$parse .= $content;
		$parse .= '<?php } ?>';
		return $parse;
	}
}
