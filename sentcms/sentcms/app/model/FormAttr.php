<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

use think\facade\Cache;
use think\facade\Config;

/**
 * 设置模型
 */
class FormAttr extends \think\Model{

	protected $type = array(
		'id' => 'integer',
	);

	/**
	 * @title 新增后事件
	 */
	protected static function onAfterInsert($data){
		$data = $data->toArray();
		if ($data['form_id']) {
			$db = new \com\Datatable();
			$name = Form::where('id', $data['form_id'])->value('name');
			$data['after'] = self::where('name', '<>', $data['name'])->where('form_id', $data['form_id'])->order('sort asc, id desc')->value('name');
			return $db->columField('form_' . strtolower($name), $data)->query();
		}
	}

	/**
	 * @title 更新后事件
	 */
	protected static function onAfterUpdate($data){
		$data = $data->toArray();
		if (isset($data['form_id']) && isset($data['name'])) {
			$tablename = Form::where('id', $data['form_id'])->value('name');
			//删除模型表中字段
			$db = new \com\Datatable();
			if ($db->CheckField(strtolower('form_' . $tablename), $data['name'])) {
				$data['action'] = 'CHANGE';
			}
			$data['after'] = self::where('name', '<>', $data['name'])->where('form_id', $data['form_id'])->order('sort asc, id asc')->value('name');
			$result = $db->columField(strtolower('form_' . $tablename), $data)->query();
			return $result;
		}else{
			return false;
		}
	}

	/**
	 * @title 删除后事件
	 */
	protected static function onAfterDelete($data){
		$data = $data->toArray();
		if ($data['form_id']) {
			$tablename = Form::where('id', $data['form_id'])->value('name');

			//删除模型表中字段
			$db = new \com\Datatable();
			if (!$db->CheckField('form_' . $tablename, $data['name'])) {
				$result = true;
			}else{
				$result = $db->delField('form_' . $tablename, $data['name'])->query();
			}
			return $result;
		}else{
			return false;
		}
	}

	protected function getTypeTextAttr($value, $data) {
		$config = Cache::get('system_config_data');
		$type = $config['config_type_list'];
		$type_text = "";
		foreach ($type as $value) {
			if ($value['key'] == $data['type']) {
				$type_text = $value['label'];
			}
		}
		return $type_text;
	}

	public function getFieldlist($map, $index = 'id') {
		$list = array();
		$row = $this->field('*,remark as help,type,extra as "option"')->where($map)->order('group_id asc, sort asc')->select();
		foreach ($row as $key => $value) {
			if (in_array($value['type'], array('checkbox', 'radio', 'select', 'bool'))) {
				$value['option'] = parse_field_attr($value['extra']);
			} elseif ($value['type'] == 'bind') {
				$extra = parse_field_bind($value['extra']);
				$option = array();
				foreach ($extra as $k => $v) {
					$option[$v['id']] = $v['title_show'];
				}
				$value['option'] = $option;
			}
			$list[$value['id']] = $value;
		}
		return $list;
	}

	public function del($id, $model_id) {
		$map['id'] = $id;
		$info = $this->find($id);
		$tablename = db('Form')->where(array('id' => $model_id))->value('name');

		//先删除字段表内的数据
		$result = $this->where($map)->delete();
		if ($result) {
			$tablename = strtolower($tablename);
			//删除模型表中字段
			$db = new \com\Datatable();
			if (!$db->CheckField($tablename, $info['name'])) {
				return true;
			}
			$result = $db->delField($tablename, $info['name'])->query();
			if ($result) {
				return true;
			} else {
				$this->error = "删除失败！";
				return false;
			}
		} else {
			$this->error = "删除失败！";
			return false;
		}
	}
}