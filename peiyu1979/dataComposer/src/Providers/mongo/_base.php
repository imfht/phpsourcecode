<?php
/**
 * Created by PhpStorm.
 * User: peiyu
 * Date: 2018-2-9
 * Time: 11:42
 */

namespace DataComposer\Providers\mongo;


abstract class _base
{
	protected $db;

	public function getDB(){
		return $this->db;
	}
	public function setDB($db){
		$this->db=$db;
	}

	public function where($w){
		$this->db=$this->db->where($w);
		return $this;
	}
	public function limit($c){
		$this->db=$this->db->limit($c);
		return $this;
	}
}