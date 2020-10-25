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
class AdPlace extends \think\Model{

	protected $auto = array('update_time');
	protected $insert = array('create_time');
	protected $type = array(
		'start_time' => 'integer',
		'end_time' => 'integer',
	);

	public $show_type = array(
		'1' => '幻灯片',
		'2' => '对联',
		'3' => '图片列表',
		'4' => '图文列表',
		'5' => '文字列表',
		'6' => '代码广告',
	);

	public static $keyList = [
		['name' => 'id', 'title' => 'ID', 'type' => 'hidden', 'help' => '', 'option' => ''],
		['name' => 'title', 'title' => '广告位名称', 'type' => 'text', 'is_must'=> true, 'help' => '', 'option' => ''],
		['name' => 'name', 'title' => '广告位标识', 'type' => 'text', 'is_must'=> true, 'help' => '调用使用{:ad("广告位标识",参数)}', 'option' => ''],
		['name' => 'show_type', 'title' => '类型', 'type' => 'select', 'help' => '', 'option' => ''],
		['name' => 'show_num', 'title' => '显示条数', 'type' => 'num', 'help' => '', 'option' => ''],
		['name' => 'start_time', 'title' => '开始时间', 'type' => 'datetime', 'is_must'=> true, 'help' => '', 'option' => ''],
		['name' => 'end_time', 'title' => '结束时间', 'type' => 'datetime', 'is_must'=> true, 'help' => '', 'option' => ''],
		['name' => 'template', 'title' => '广告模版', 'type' => 'text', 'help' => '', 'option' => ''],
		['name' => 'status', 'title' => '状态', 'type' => 'select', 'help' => '', 'option' => [['key' => '0', 'label'=>'禁用'],['key' => '1', 'label'=>'启用']]],
	];

	public function initialize() {
		parent::initialize();
		foreach ($this->keyList as $key => $value) {
			if ($value['name'] == 'show_type') {
				$value['option'] = $this->show_type;
			}
			$this->keyList[$key] = $value;
		}
	}
}