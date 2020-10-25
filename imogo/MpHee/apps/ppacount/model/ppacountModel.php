<?php
class ppacountModel extends baseModel{
	protected $table = 'ppacount'; //设置表名
	
	public function adminlist($condition) 
    {
        return $this->model->table('admin')->where($condition)->order("id desc")->select();
    }
	
	public function admininfo($condition)
    {
        return $this->model->table('admin')->where($condition)->find();
    }
	
	public function adminadd($data)
    {
    	return $this->model->table('admin')->data($data)->insert();
    }
	
	public function admindel($condition) 
    {
		return $this->model->table('admin')->where($condition)->delete();
    }
	
	public function adminupdate($condition,$data) 
    {
        return $this->model->table('admin')->data($data)->where($condition)->update();
    }
	
}