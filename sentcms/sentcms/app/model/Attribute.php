<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------
namespace app\model;

use think\facade\Config;
use think\facade\Db;
use sent\tree\Tree;
use app\model\Model as Models;

/**
 * 设置模型
 */
class Attribute extends \think\Model {

	protected $type = array(
		'id' => 'integer',
	);

	/**
	 * @title 新增后事件
	 */
	protected static function onAfterInsert($data){
		$data = $data->toArray();
		if ($data['model_id']) {
			$db = new \com\Datatable();
			$name = Models::where('id', $data['model_id'])->value('name');
			$data['after'] = self::where('name', '<>', $data['name'])->where('model_id', $data['model_id'])->order('sort asc, id desc')->value('name');
			return $db->columField(strtolower($name), $data)->query();
		}
	}

	/**
	 * @title 更新后事件
	 */
	protected static function onAfterUpdate($data){
		$data = $data->toArray();
		if (isset($data['model_id']) && isset($data['name'])) {
			$tablename = Models::where('id', $data['model_id'])->value('name');
			//删除模型表中字段
			$db = new \com\Datatable();
			if ($db->CheckField($tablename, $data['name'])) {
				$data['action'] = 'CHANGE';
			}
			$data['after'] = self::where('name', '<>', $data['name'])->where('model_id', $data['model_id'])->order('sort asc, id asc')->value('name');
			$result = $db->columField(strtolower($tablename), $data)->query();
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
		if ($data['model_id']) {
			$tablename = Models::where('id', $data['model_id'])->value('name');

			//删除模型表中字段
			$db = new \com\Datatable();
			if (!$db->CheckField($tablename, $data['name'])) {
				$result = true;
			}else{
				$result = $db->delField($tablename, $data['name'])->query();
			}
			return $result;
		}else{
			return false;
		}
	}

	protected function getTypeTextAttr($value, $data) {
		$config_type_list = Config::get('config.config_type_list') ?? [];
		$type = [];
		foreach ($config_type_list as $key => $value) {
			$type[$value['key']] = $value['label'];
		}
		return isset($type[$data['type']]) ? $type[$data['type']] : '';
	}

	protected function getOptionAttr($value, $data){
		$list = [];
		if ($data == '') {
			return $list;
		}
		if (in_array($data['type'], ['checkbox', 'radio', 'select'])) {
			$row = explode(PHP_EOL, $data['extra']);
			foreach ($row as $k => $val) {
				if (strrpos($val, ":")) {
					list($key, $label) = explode(":", $val);
					$list[] = ['key' => $key, 'label' => $label];
				}else{
					$list[] = ['key' => $k, 'label' => $val];
				}
			}
		}elseif($data['type'] == 'bool'){
			$list = [['key'=>0,'label'=>'禁用'],['key'=>1,'label'=>'启用']];
		}elseif($data['type'] == 'bind'){
			$map = [];
			$db = new \com\Datatable();
			if (strrpos($data['extra'], ":")) {
				$extra = explode(":", $data['extra']);
				if ($db->CheckField($extra[0], 'model_id')) {
					$map[] = ['model_id', '=', $data['model_id']];
				}
				$row =  Db::name($extra[0])->where($map)->select()->toArray();
				if(empty($row)){
					return [];
				}
				if ($extra[1] == 'tree') {
					$row = (new Tree())->toFormatTree($row);
					foreach ($row as $val) {
						$list[] = ['key'=>$val['id'], 'label'=>$val['title_show']];
					}
				}else{
					foreach ($row as $val) {
						$list[] = ['key'=>$val['id'], 'label'=>$val['title']];
					}
				}
			}else{
				if ($db->CheckField($data['extra'], 'model_id')) {
					$map[] = ['model_id', '=', $data['model_id']];
				}
				$row =  Db::name($data['extra'])->select($map)->toArray();
				foreach ($row as $val) {
					$list[] = ['key'=>$val['id'], 'label'=>$val['title']];
				}
			}
		}
		return $list;
	}

	public static function getField($model, $ac = "add"){
		$list = [];
		$group = $model['attr_group'];

		$map = [];
		$map[] = ['model_id', '=', $model['id']];
		if ($ac == 'add') {
			$map[] = ['is_show', 'IN', [1, 2]];
		}else if ($ac == 'edit') {
			$map[] = ['is_show', 'IN', [1, 3]];
		}

		$row = self::where($map)->order('group_id asc, sort asc, id desc')
			->select()
			->append(['option'])
			->toArray();
		foreach ($row as $key => $value) {
			if (isset($group[$value['group_id']])) {
				$list[$group[$value['group_id']]['label']][] = $value;
			}else{
				$list[$value['group_id']][] = $value;
			}
		}

		return $list;
	}

	public static function getfieldList(){
		$config = \think\facade\Cache::get('system_config_data');
		$time = [['key'=>1, 'label'=>'新增'],['key'=>2, 'label'=>'编辑'],['key'=>3, 'label'=>'始终']];
		$auto_type = [['key'=>'function', 'label'=>'函数'],['key'=>'field', 'label'=>'字段'],['key'=>'string', 'label'=>'字符串']];
		$validate_type = [['key'=>'thinkphp', 'label'=>'thinkphp内置'],['key'=>'regex', 'label'=>'正则验证']];
		return [
			'基础' => [
				['name' => 'id', 'title' => 'id', 'help' => '', 'type' => 'hidden'],
				['name' => 'model_id', 'title' => 'model_id', 'help' => '', 'type' => 'hidden'],
				['name' => 'name', 'title' => '字段名', 'help' => '英文字母开头，长度不超过30', 'is_must'=> true, 'type' => 'text'],
				['name' => 'title', 'title' => '字段标题', 'help' => '请输入字段标题，用于表单显示', 'is_must'=> true, 'type' => 'text'],
				['name' => 'type', 'title' => '字段类型', 'help' => '用于表单中的展示方式', 'type' => 'select', 'option' => $config['config_type_list'], 'help' => ''],
				['name' => 'length', 'title' => '字段长度', 'help' => '字段的长度值', 'type' => 'text'],
				['name' => 'extra', 'title' => '参数', 'help' => '布尔、枚举、多选字段类型的定义数据', 'type' => 'textarea'],
				['name' => 'value', 'title' => '默认值', 'help' => '字段的默认值', 'type' => 'text'],
				['name' => 'remark', 'title' => '字段备注', 'help' => '用于表单中的提示', 'type' => 'text'],
				['name' => 'is_show', 'title' => '是否显示', 'help' => '是否显示在表单中', 'type' => 'select', 'option' => [
						['key'=>'1', 'label' => '始终显示'], ['key'=>'2', 'label'  => '新增显示'], ['key'=>'3', 'label'  => '编辑显示'], ['key'=>'0', 'label'  => '不显示']
					], 'value' => 1],
				['name' => 'is_must', 'title' => '是否必填', 'help' => '用于自动验证', 'type' => 'select', 'option' => [['key'=>'0', 'label' => '否'], ['key'=>'1', 'label' => '是']]],
			],
			'高级' => [
				// ['name' => 'validate_type', 'title' => '验证方式', 'type' => 'select', 'option' => $validate_type, 'help' => ''],
				['name' => 'validate_rule', 'title' => '验证规则', 'help' => '使用thinkphp内置验证规则，详情：https://www.kancloud.cn/manual/thinkphp6_0/1037629', 'type' => 'text'],
				['name' => 'error_info', 'title' => '出错提示', 'type' => 'text', 'help' => ''],
				['name' => 'validate_time', 'title' => '验证时间', 'help' => '英文字母开头，长度不超过30', 'type' => 'select', 'option' => $time, 'help' => ''],
				['name' => 'auto_type', 'title' => '自动完成方式', 'help' => '英文字母开头，长度不超过30', 'type' => 'select', 'option' => $auto_type, 'help' => ''],
				['name' => 'auto_rule', 'title' => '自动完成规则', 'help' => '根据完成方式订阅相关规则', 'type' => 'text'],
				['name' => 'auto_time', 'title' => '自动完成时间', 'help' => '英文字母开头，长度不超过30', 'type' => 'select', 'option' => $time],
			],
		];
	}
}