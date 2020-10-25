<?php
class usercenterModel extends baseModel{
	protected $table = 'usercenter'; //设置表名
	
	public function userlist($condition,$limit) 
    {
        return $this->model->table('userinfo')->where($condition)->order("id desc")->limit($limit)->select();
    }
	
	public function userinfo($condition)
    {
        return $this->model->table('userinfo')->where($condition)->find();
    }
	
	public function grouplist($condition) 
    {
        return $this->model->table('usergroup')->where($condition)->order("groupid desc")->select();
    }
	
	public function groupinfo($condition)
    {
        return $this->model->table('usergroup')->where($condition)->find();
    }
	
	public function groupadd($data) 
    {
    	return $this->model->table('usergroup')->data($data)->insert();
    }
	
	public function groupdel($condition) 
    {
		return $this->model->table('usergroup')->where($condition)->delete();
    }
	
	public function groupupdate($condition,$data) 
    {
        return $this->model->table('usergroup')->data($data)->where($condition)->update();
    }
	
	public function cardlist($condition) 
    {
        return $this->model->table('usercard')->where($condition)->order("id desc")->select();
    }
	
	public function cardinfo($condition)
    {
        return $this->model->table('usercard')->where($condition)->find();
    }
	
	public function cardadd($data) 
    {
    	return $this->model->table('usercard')->data($data)->insert();
    }
	
	public function carddel($condition) 
    {
		return $this->model->table('usercard')->where($condition)->delete();
    }
	
	public function cardupdate($condition,$data) 
    {
        return $this->model->table('usercard')->data($data)->where($condition)->update();
    }
	
}