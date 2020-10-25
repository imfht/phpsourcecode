<?php
class adminModel extends baseModel{
	protected $table = 'admin';
	
	public function getUserInfo( $condition ){
		return $this->model->field('id,username,password,manage,pid')->table('admin')->where($condition)->find();
	}

}