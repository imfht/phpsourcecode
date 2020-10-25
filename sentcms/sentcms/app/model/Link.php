<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

/**
 * 友情链接类
 * @author molong <molong@tensent.cn>
 */
class Link extends \think\Model {

	protected $auto = ['update_time'];
	protected $insert = ['create_time'];

	public static $keyList = [
		['name' => 'id', 'title' => 'ID', 'type' => 'hidden'],
		['name' => 'title', 'title' => '友链标题', 'type' => 'text', 'is_must' => 1, 'help' => ''],
		['name' => 'url', 'title' => 'URL链接', 'type' => 'text', 'is_must' => 1, 'help' => '连接格式如：https://www.tensent.cn'],
		['name' => 'ftype', 'title' => '友链类别', 'type' => 'select', 'option' => [
			['key'=>'1', 'label' => '常用链接'],
			['key'=>'2', 'label' => '网站导读'],
			['key'=>'3', 'label' => '对公服务'],
			['key'=>'4', 'label' => '校内服务'],
		], 'help' => ''],
		['name' => 'cover_id', 'title' => '网站LOGO', 'type' => 'image', 'help' => ''],
		['name' => 'status', 'title' => '状态', 'type' => 'select', 'option' => [['key' => '0', 'label'=>'禁用'],['key' => '1', 'label'=>'启用']], 'help' => ''],
		['name' => 'sort', 'title' => '链接排序', 'type' => 'text', 'help' => ''],
		['name' => 'descrip', 'title' => '描述', 'type' => 'textarea', 'help' => ''],
	];

	protected $type = [
		'cover_id' => 'integer',
		'sort' => 'integer'
	];
}