<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;

/**
 * 扩展字段
 * @author yangweijie <yangweijiester@gmail.com>
 */

class MemberExtendSettingModel extends Model {
	protected $tablename = null;

	/* 自动验证规则 */
	protected $_validate = array(
		array('name', 'require', '字段名必须', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
		array('name', '/^[a-zA-Z][\w_]{1,29}$/', '字段名不合法', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
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

	public function update($data){
		$this->tablename = 'member_extend';
		if(empty($data['id'])){
			$id = $this->add($data);
			if($id){
				return $this->add_field($data);
			}
		}else{
			$status = $this->save($data);
			if($status){
				return $this->edit_field($data);
			}
		}
	}

	public function add_field($field = array()){
		return $this->colum_field($field);
	}

	public function edit_field($field = array()){
		return $this->colum_field($field , 'CHANGE');
	}

	public function colum_field($field = array() , $action = 'ADD'){
		$db = new \OT\Datatable();
		$fields = array(
			'field' => $field['name'],
			'type' => $field['type_string'],
			'length' => $field['length'],
			'is_null' => $field['is_must'],
			'default' => $field['default'],
			'comment' => $field['remark'],
			'action' => $action,
		);
		if(!empty($field['oldname'])){
			$fields['oldname'] = $field['oldname'];
		}
		if($db->checkTable($this->tablename)){
			return $db->colum_field($this->tablename , $fields)->query();
		}
	}

	public function del_field(string $field){
		if($this->where(array('name' => $field))->delete()){
			$db = new \OT\Datatable();
			return $db->del_field('member_extend' , $field)->query();
		}
	}
}