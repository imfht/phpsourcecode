<?php
// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

namespace Common\Model;
use Think\Model;

/**
* 属性模型
* @author huajie <banhuajie@163.com>
*/

class AttributeModel extends Model {

	/* 操作的表名 */
	protected $table_name = null;

	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '字段名必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', '/^[a-zA-Z][\w_]{1,29}$/', '字段名不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('model_id', 'require', '所属模型不能为空', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('length','checkFieldlength','字段长度必须',self::MUST_VALIDATE,'callback', self::MODEL_BOTH),
		array('length', 'require', '字段长度必须', self::VALUE_VALIDATE, 'regex', self::MODEL_BOTH),
		array('length', '1,100', '注释长度不能超过100个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
		array('title', '1,100', '注释长度不能超过100个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
		array('remark', '1,100', '备注不能超过100个字符', self::VALUE_VALIDATE, 'length', self::MODEL_BOTH),
	);

	/* 自动完成规则 */
	protected $_auto = array(
		array('status', 1, self::MODEL_INSERT, 'string'),
		array('create_time', 'time', self::MODEL_INSERT, 'function'),
		array('update_time', 'time', self::MODEL_BOTH, 'function'),
	);

	protected function checkFieldlength($field){
		$type = I('post.type');
		if (!in_array($type, array('textarea','editor')) && $field == '') {
			return false;
		}
	}

	public function update(){
		$data = $this->create();
		if ($data) {
			//在数据库内添加字段
			$result = $this->checkTableField($data);
			if (!$result) {
				$this->error = "字段创建失败！";
				return false;
			}
			if ($data['id']) {
				$result = $this->save();
			}else{
				$result = $this->add();
			}
		}else{
			return false;
		}

		return $result;
	}

	public function del($id){
		$map['id'] = $id;
		$info = $this->find($id);
		$model = D('Model')->where(array('id'=>$info['model_id']))->find();

		//先删除字段表内的数据
		$result = $this->where($map)->delete();
		if ($result) {
			if ($model['extend'] == 1) {
				$tablename = 'document_'.$model['name'];
			}else{
				$tablename = $model['name'];
			}

			//删除模型表中字段
			$db = new \OT\Datatable();
			$result = $db->del_field($tablename,$info['name'])->query();
			if ($result) {
				return true;
			}else{
				$this->error = "删除失败！";
				return false;
			}
		}else{
			$this->error = "删除失败！";
			return false;
		}
	}

	protected function checkTableField($field){
		$model = M('Model')->find($field['model_id']);
		if ($model['extend'] == 1) {
			$tablename = 'document_'.$model['name'];
		}else{
			$tablename = $model['name'];
		}

		//实例化一个数据库操作类
		$db = new \OT\Datatable();
		//检查表是否存在并创建
		if (!$db->CheckTable($tablename)) {
			//创建新表
			$db->start_table($tablename)->create_id()->create_key()->end_table()->query();
		};
		$oldname = "";
		if ($field['id']) {
			$oldname = $this->where(array('id'=>$field['id']))->getField('name');
		}
		$attribute_type = get_attribute_type();
		$field['field'] = $field['name'];
		$field['type'] = $attribute_type[$field['type']][1];  
		$field['is_null'] = $field['is_must'];  //是否为null
		$field['default'] = $field['value'];    //字段默认值
		$field['comment'] = $field['remark'];   //字段注释
		if($db->CheckField($tablename,$oldname) && $oldname){
			$field['action'] = 'CHANGE';
			$field['oldname'] = $oldname;
			$field['newname'] = $field['name'];
			$db->colum_field($tablename,$field);
		}else{
			$field['action'] = 'ADD';
			$db->colum_field($tablename,$field);
		}
		//dump($db->sql);exit();
		$result = $db->create();
		return $result;
	}

	public function getFields($map,$index='id'){
		$list = $this->field('*,remark as help,type,extra as opt')->where($map)->index($index)->select();
		foreach ($list as $key => $value) {
			if (array($value['type'],array('checkbox','radio','select'))) {
				$value['opt'] = parse_field_attr($value['extra']);
			}
			if ($value['type'] == 'bind') {
				$extra = parse_field_bind($value['extra']);
				foreach ($extra as $k => $v) {
					$option[$v['id']] = $v['title_show'];
				}
				$value['opt'] = $option;
			}
			$list[$key] = $value;
		}
		return $list;
	}
}
