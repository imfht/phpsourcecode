<?php

namespace DataComposer\Providers\mongo;

use Db;

/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class thinkphp extends _base
{
	protected $db;
	
	public function __construct($tableName,$connectstring=null){
		if($connectstring)$this->db =Db::connect($connectstring)->table($tableName);
		else $this->db = Db::table($tableName);
	}
	
	
	public function whereIn($k,$v){
		$this->db=$this->db->where($k,'in',$v);
		return $this;
	}

	
	public function select($fields){
		$this->db=$this->db->field($fields);
		return $this;
	}
	public function orderBy($k,$o='asc'){
		$this->db=$this->db->order($k,$o);
		return $this;
	}

	
	public function get(){
		$_data = $this->db->select();
		//$_data = json_decode(json_encode($_data), true);
		return $_data;
	}


}