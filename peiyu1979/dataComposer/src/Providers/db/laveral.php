<?php

namespace DataComposer\Providers\db;

use DataComposer\comm;
use DB;

/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-1-4
 * Time: 13:22
 */
class laveral extends  _base
{

	
	public function __construct($tableName,$connectstring=null){
		if($connectstring)$this->db =DB::connection($connectstring)->table($tableName);
		else $this->db = DB::table($tableName);
	}

	
	public function whereIn($k,$v){
		$this->db=$this->db->whereIn($k,$v);
		return $this;
	}

	
	public function select($fields){
		$this->db=$this->db->select($fields);
		return $this;
	}
	public function orderBy($k,$o='asc'){
		$this->db=$this->db->orderBy($k,$o);
		return $this;
	}

	
	public function get(){
		$_data = $this->db->get();
		//$_data = json_decode(json_encode($_data), true);
		return comm::get_object_vars($_data) ;
	}


}