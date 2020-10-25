<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace app\model;

use think\facade\Config;

/**
 * 表单
 */
class Form extends \think\Model {

	protected $auto = ['update_time'];
	protected $insert = ['name', 'create_time', 'status' => 1, 'list_grid' => "id:ID\r\ntitle:标题\r\ncreate_time:添加时间|time_format\r\nupdate_time:更新时间|time_format"];
	protected $type = [
		'id' => 'integer'
	];

	public $addField = [
		['name' => 'name', 'title' => '标识', 'type' => 'text', 'is_must'=> true, 'help' => ''],
		['name' => 'title', 'title' => '标题', 'type' => 'text', 'is_must'=> true, 'help' => ''],
		['name' => 'logo', 'title' => '显示Logo', 'type' => 'image', 'help' => ''],
		['name' => 'cover', 'title' => 'banner图片', 'type' => 'image', 'help' => ''],
		['name' => 'content', 'title' => '内容', 'type' => 'editor', 'help' => ''],
		['name' => 'list_grid', 'title' => '列表定义', 'type' => 'textarea', 'is_must'=> true, 'help' => ''],
	];

	public $editField = [
		['name' => 'id', 'title' => 'ID', 'type' => 'hidden', 'help' => ''],
		// ['name' => 'name', 'title' => '标识', 'type' => 'text', 'help' => ''],
		['name' => 'title', 'title' => '标题', 'type' => 'text', 'help' => ''],
		['name' => 'logo', 'title' => '显示Logo', 'type' => 'image', 'help' => ''],
		['name' => 'cover', 'title' => 'banner图片', 'type' => 'image', 'help' => ''],
		['name' => 'content', 'title' => '内容', 'type' => 'editor', 'help' => ''],
		['name' => 'list_grid', 'title' => '列表定义', 'type' => 'textarea', 'help' => ''],
	];

	protected static function onBeforeInsert($data){
		if ($data['name'] && $data['title']) {
			$db = new \com\Datatable();
			//检查表是否存在并创建
			if (!$db->CheckTable('form_' . $data['name'])) {
				//创建新表
				return $db->initTable('form_' . $data['name'], $data['title'], 'id')->query();
			}else{
				return false;
			}
		}else{
			return false;
		}
	}

	public static function onAfterInsert($data){
		$data = $data->toArray();
		$fields = [
			'uid'    => ['name' => 'uid', 'title' => '用户UID', 'type' => 'num', 'length' => 11, 'extra' => '', 'remark' => '用户UID', 'is_show' => 0, 'is_must' => 1, 'value'=>'0'],
			'status'      => ['name' => 'status', 'title' => '数据状态', 'type' => 'select', 'length' => 2, 'extra' => "0:禁用\r\n1:正常", 'remark' => '数据状态', 'is_show' => 1, 'is_must' => 1, 'value'=>'1'],
			'update_time' => ['name' => 'update_time', 'title' => '更新时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '更新时间', 'is_show' => 0, 'is_must' => 1, 'value'=>'0'],
			'create_time' => ['name' => 'create_time', 'title' => '添加时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '添加时间', 'is_show' => 0, 'is_must' => 1, 'value'=>'0'],
		];
		$result = false;
		if (!empty($fields)) {
			foreach ($fields as $key => $value) {
				$fields[$key]['form_id'] = $data['id'];
			}
			$result = (new FormAttr())->saveAll($fields);
		}
		return $result;
	}

	protected static function onAfterDelete($data){
		$data = $data->toArray();
		(new FormAttr())->where('form_id', $data['id'])->delete();
		$db = new \com\Datatable();
		$result = false;
		if ($db->CheckTable('form_' . $data['name'])) {
			$result = $db->delTable('form_' . $data['name'])->query();
		}
		return $result;
	}

	public function getGridListAttr($value, $data){
		$list = [];
		if ($data['list_grid'] !== '') {
			$row = explode(PHP_EOL, $data['list_grid']);
			foreach ($row as $r) {
				list($field, $title) = explode(":", $r);
				$list[$field] = ['field' => $field, 'title' => $title];
				if (strrpos($title, "|")) {
					$title = explode("|", $title);
					$list[$field] = ['field' => $field, 'title' => $title[0], 'format' => trim($title[1])];
				}
			}
		}
		return $list;
	}

	public function getStatusTextAttr($value, $data) {
		$status = array(
			0 => '禁用',
			1 => '启用',
		);
		return $status[$data['status']];
	}

}