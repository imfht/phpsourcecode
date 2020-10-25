<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\model;

/**
 * 分类模型
 */
class Ad extends \think\Model{

	protected $auto = array('update_time');
	protected $insert = array('create_time');
	protected $type = [
		'id' => 'integer',
		'cover_id' => 'integer',
	];

	public static $keyList = [
		['name' => 'id', 'title' => 'ID', 'type' => 'hidden', 'help' => '', 'option' => ''],
		['name' => 'place_id', 'title' => 'PLACE_ID', 'type' => 'hidden', 'help' => '', 'option' => ''],
		['name' => 'title', 'title' => '广告名称', 'type' => 'text', 'help' => '', 'option' => ''],
		['name' => 'cover_id', 'title' => '广告图片', 'type' => 'image', 'help' => '', 'option' => ''],
		['name' => 'url', 'title' => '广告链接', 'type' => 'text', 'help' => '', 'option' => ''],
		['name' => 'photolist', 'title' => '辅助图片', 'type' => 'images', 'help' => '', 'option' => ''],
		['name' => 'listurl', 'title' => '辅助链接', 'type' => 'textarea', 'help' => '对应辅助图片的排序，一行一个', 'option' => ''],
		['name' => 'background', 'title' => '广告背景颜色', 'type' => 'text', 'help' => '', 'option' => ''],
		['name' => 'content', 'title' => '广告描述', 'type' => 'textarea', 'help' => '', 'option' => ''],
		['name' => 'status', 'title' => '状态', 'type' => 'select', 'help' => '', 'option' => [['key' => '0', 'label'=>'禁用'],['key' => '1', 'label'=>'启用']]]
	];
}
