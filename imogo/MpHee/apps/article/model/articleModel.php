<?php
class articleModel extends baseModel{
	protected $table = 'article'; //设置表名
	
	//图文显示使用
	public function ppacountinfo($ppid) 
    {
        return $this->model->table('ppacount')->where("id = '$ppid'")->find();
    }
	
	//图文使用
	public function getlist($ppid, $pid, $start, $limit)
    {
    	return $this->model
	    	->field('A.*')
	    	->table('sucai_article A')
	    	->where("ppid = '$ppid' and pid = '$pid'")
	    	->order("id desc")
	    	->limit("$start, $limit")
	    	->select();
    }
    
    public function articlecount($ppid)
    {
    	return $this->model->table('sucai_article A')->where("ppid = '$ppid' and pid = 0")->count();
    }
    
    public function getsublist($ppid, $id) 
    {
        return $this->model->table('sucai_article')->where("ppid = '$ppid' and pid = '$id'")->order("id asc")->select();
    }
    
    public function info($filterKey, $filterValue) 
    {
        return $this->model->table('sucai_article')->where("$filterKey='$filterValue'")->find();
    }
    
    public function add($data)
    {
    	$data["createtime"] = time();
    	return $this->model->table('sucai_article')->data($data)->insert();
    }
    
    public function edit($data)
    {
		$condition['id']=intval($data['id']);
        $this->model->table('sucai_article')->data($data)->where($condition)->update();
        return intval($data['id']);
    }
    
    public function delete($id)
    {
		return $this->model->table('sucai_article')->where("id = $id or pid = $id")->delete();
    }
    
     public function getlistById($ppid, $id, $pid = 0)
    {
    	return $this->model
	    	->field('A.*')
	    	->table('sucai_article A')
	    	->where("ppid = '$ppid' and id = '$id' and pid = '$pid'")
	    	->order("id asc")
	    	->select();
    }
    
    public function getsublistById($ppid, $pid)
    {
    	return $this->model
	    	->field('A.*')
	    	->table('sucai_article A')
	    	->where("ppid = '$ppid' and pid = '$pid'")
	    	->order("id asc")
	    	->select();
    }
	
	//keyword使用
	public function getkeywordlist($ppid) 
    {
        return $this->model->table('sucai_keyword')->where("ppid = '$ppid'")->order("id asc")->select();
    }
	
	public function getkeyword($id) 
    {
        return $this->model->table('sucai_keyword')->where("id = '$id'")->find();
    }
	
	public function keywordadd($data) 
    {
        $data["createtime"] = date("Y-m-d H:i:s");
    	return $this->model->table('sucai_keyword')->data($data)->insert();
    }
	
	public function keywordupdate($data) 
    {
		$condition['id']=intval($data['id']);
        return $this->model->table('sucai_keyword')->data($data)->where($condition)->update();
    }
	
	public function keyworddelete($id) 
    {
		return $this->model->table('sucai_keyword')->where("id = $id")->delete();
    }
	
	//首次关注使用
	public function getguanzhu($ppid) 
    {
        return $this->model->table('sucai_guanzhu')->where("ppid = '$ppid'")->find();
    }
	
	public function guanzhuadd($data)
    {
        return $this->model->table('sucai_guanzhu')->data($data)->insert();
    }
	
	public function guanzhuupdate($ppid,$data)
    {
        return $this->model->table('sucai_guanzhu')->data($data)->where("ppid = $ppid")->update();
    }
	
	//回复使用
	public function replyguanzhu($condition) 
    {
        return $this->model->table('sucai_guanzhu')->where($condition)->find();
    }
	
	public function replykeyword($condition) 
    {
        return $this->model->table('sucai_keyword')->where($condition)->find();
    }
	
	public function replyarticle($id) 
    {
        return $this->model->table('sucai_article')->where("id = '$id'")->find();
    }
	
	public function replyarticlemul($id) 
    {
        return $this->model->table('sucai_article')->where("pid = '$id'")->select();
    }
	
}