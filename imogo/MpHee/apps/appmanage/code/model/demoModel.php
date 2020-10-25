<?php
class demoModel extends baseModel{
	protected $table = 'demo'; //设置表名
	
	public function demolist($condition) 
    {
        //return $this->model->table('demo')->where($condition)->order("id desc")->select();
		return array(
			array('id'=>1,'title'=>'标题一','categoryname'=>'分类一','createtime'=>1416896497),
			array('id'=>2,'title'=>'标题二','categoryname'=>'分类二','createtime'=>1416896297),
			array('id'=>3,'title'=>'标题三','categoryname'=>'分类一','createtime'=>1416896197),
		);
    }
	
	public function demoinfo($condition)
    {
        //return $this->model->table('demo')->where($condition)->find();
		return array('id'=>1,'title'=>'标题一','categoryname'=>'分类一','createtime'=>1416896497);
    }
	
	public function demoadd($data) 
    {
    	return $this->model->table('demo')->data($data)->insert();
    }
	
	public function demodelete($condition) 
    {
		return $this->model->table('demo')->where($condition)->delete();
    }
	
	public function demoupdate($condition,$data) 
    {
        return $this->model->table('demo')->data($data)->where($condition)->update();
    }
	
}