<?php
namespace root\base;
use ext\db, z\pdo;
class model{
	protected $db, $pdo, $cfg, $error;
	function __construct(array $cfg = null){
		$this->cfg = $cfg;
	}
	function pdo(){
		$this->pdo || $this->pdo = pdo::init($this->cfg);
		return $this->pdo;
	}
	function db(string $table = ''){
		$this->db ? ($table && $this->db->table($table)) : $this->db = db::init($table, $this->cfg);
		return $this->db;
	}
	function error(string $msg=''){
		$this->error = $msg;
		return false;
	}
	function getError(){
		return $this->error;
	}
}