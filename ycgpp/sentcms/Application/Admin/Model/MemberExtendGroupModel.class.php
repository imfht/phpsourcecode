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
 * 扩展组
 * @author yangweijie <yangweijiester@gmail.com>
 */

class MemberExtendGroupModel extends Model {
	protected $_auto = array(
		array('createTime' , 'time' , self::MODEL_INSERT , 'function'),
		array('name' , 'member_extend' , self::MODEL_INSERT , 'string'),
	);

	public function update($data = array()){
		if(empty($data['id'])){
			return $this->add($data);
		}else{
			return $this->save($data);
		}
	}

	public function selectone($id = null){
		$find = $this->where(array('id' => $id))->find();
		if(empty($find)){
			return null;
		}
		return $find;
	}

	public function remove($id = null){
		return $this->delete($id);
	}
}