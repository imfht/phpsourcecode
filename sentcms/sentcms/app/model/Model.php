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
 * 设置模型
 */
class Model extends \think\Model {

	protected $auto = ['update_time'];
	protected $insert = ['name', 'create_time', 'status' => 1, 'list_grid' => "id:ID\r\ntitle:标题\r\ncreate_time:添加时间\r\nupdate_time:更新时间"];
	protected $type = array(
		'id' => 'integer',
	);

	protected static function onBeforeInsert($data) {
		if ($data['name'] && $data['title']) {
			$db = new \com\Datatable();
			//检查表是否存在并创建
			if (!$db->CheckTable($data['name'])) {
				//创建新表
				return $db->initTable($data['name'], $data['title'], 'id')->query();
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	protected static function onAfterInsert($data) {
		$data = $data->toArray();
		$fields = [
			'title' => ['name' => 'title', 'title' => '标题', 'type' => 'text', 'length' => 200, 'extra' => '', 'remark' => '标题', 'is_show' => 1, 'is_must' => 1, 'value' => ''],
			'category_id' => ['name' => 'category_id', 'title' => '栏目', 'type' => 'bind', 'length' => 10, 'extra' => 'category:tree', 'remark' => '栏目', 'is_show' => 1, 'is_must' => 1, 'value' => '0'],
			'uid' => ['name' => 'uid', 'title' => '用户UID', 'type' => 'num', 'length' => 11, 'extra' => '', 'remark' => '用户UID', 'is_show' => 0, 'is_must' => 1, 'value' => '0'],
			'cover_id' => ['name' => 'cover_id', 'title' => '内容封面', 'type' => 'image', 'length' => 10, 'extra' => '', 'remark' => '内容封面', 'is_show' => 1, 'is_must' => 0, 'value' => '0'],
			'description' => ['name' => 'description', 'title' => '内容描述', 'type' => 'textarea', 'length' => '', 'extra' => '', 'remark' => '内容描述', 'is_show' => 1, 'is_must' => 0, 'value' => ''],
			'content' => ['name' => 'content', 'title' => '内容', 'type' => 'editor', 'length' => '', 'extra' => '', 'remark' => '内容', 'is_show' => 1, 'is_must' => 0, 'value' => ''],
			'status' => ['name' => 'status', 'title' => '数据状态', 'type' => 'select', 'length' => 2, 'extra' => "-1:删除\r\n0:禁用\r\n1:正常\r\n2:待审核\r\n3:草稿", 'remark' => '数据状态', 'is_show' => 1, 'is_must' => 1, 'value' => '1'],
			'is_top' => ['name' => 'is_top', 'title' => '是否置顶', 'type' => 'bool', 'length' => 2, 'extra' => '', 'remark' => '是否置顶', 'is_show' => 0, 'is_must' => 1, 'value' => '0'],
			'view' => ['name' => 'view', 'title' => '浏览数量', 'type' => 'num', 'length' => 11, 'extra' => '', 'remark' => '浏览数量', 'is_show' => 0, 'is_must' => 1, 'value' => '0'],
			'update_time' => ['name' => 'update_time', 'title' => '更新时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '更新时间', 'is_show' => 0, 'is_must' => 1, 'value' => '0'],
			'create_time' => ['name' => 'create_time', 'title' => '添加时间', 'type' => 'datetime', 'length' => 11, 'extra' => '', 'remark' => '添加时间', 'is_show' => 0, 'is_must' => 1, 'value' => '0'],
		];
		$result = false;
		if (!empty($fields)) {
			foreach ($fields as $key => $value) {
				if ($data['is_doc']) {
					$fields[$key]['model_id'] = $data['id'];
				} else {
					if (in_array($key, ['uid', 'title', 'status', 'view', 'create_time', 'update_time'])) {
						$fields[$key]['model_id'] = $data['id'];
					} else {
						unset($fields[$key]);
					}
				}
			}
			$result = (new Attribute())->saveAll($fields);
		}
		return $result;
	}

	protected static function onAfterUpdate($data) {
		$data = $data->toArray();
		if (isset($data['attribute_sort']) && $data['attribute_sort']) {
			$attribute_sort = json_decode($data['attribute_sort'], true);

			$attr = [];
			if (!empty($attribute_sort)) {
				foreach ($attribute_sort as $key => $value) {
					$attr[$key] = Attribute::where('id', 'IN', $value)->column('*', 'id');
					foreach ($value as $k => $v) {
						$attr[$key][$v]['group_id'] = $key;
						$attr[$key][$v]['sort'] = $k;
					}
				}
			}
			$save = [];
			foreach ($attr as $value) {
				if (!empty($value)) {
					$save = array_merge($save, $value);
				}
			}
			if (!empty($attr)) {
				(new Attribute())->saveAll($save);
			}
		}
		return true;
	}

	protected static function onAfterDelete($data) {
		$data = $data->toArray();
		(new Attribute())->where('model_id', $data['id'])->delete();
		$db = new \com\Datatable();
		if ($db->CheckTable($data['name'])) {
			$db->delTable($data['name'])->query();
		}
	}

	protected function setAttributeSortAttr($value) {
		return $value ? json_encode($value) : '';
	}

	public function setNameAttr($value) {
		return strtolower($value);
	}

	public function getGridListAttr($value, $data) {
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

	public function getAttrGroupAttr($value, $data) {
		$list = [];
		if ($data['attribute_group']) {
			$row = explode(";", $data['attribute_group']);
			foreach ($row as $r) {
				list($key, $label) = explode(":", $r);
				$list[$key] = ['key' => $key, 'label' => $label];
			}
		}
		return $list;
	}

	public function getStatusTextAttr($value, $data) {
		$status = [0 => '禁用', 1 => '启用'];
		return $status[$data['status']];
	}

	public function del() {
		$id = input('id', '', 'trim,intval');
		$tablename = $this->where('id', $id)->value('name');

		//删除数据表
		$db = new \com\Datatable();
		if ($db->CheckTable($tablename)) {
			//检测表是否存在
			$result = $db->delTable($tablename)->query();
			if (!$result) {
				return false;
				$this->error = "数据表删除失败！";
			}
		}
		db('Attribute')->where('model_id', $id)->delete(); //删除字段信息
		$result = $this->where('id', $id)->delete();
		if ($result) {
			return true;
		} else {
			$this->error = "模型删除失败！";
			return false;
		}
	}

	public function attribute() {
		return $this->hasMany('Attribute');
	}
}