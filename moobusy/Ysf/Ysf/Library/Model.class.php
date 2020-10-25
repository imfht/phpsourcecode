<?php
namespace Ysf;
/**
 * model class 
 */
class Model
{
	public $conn;

	public function init($database_flag)
	{
		if (isset($this->conn[$database_flag])) {
			return $this->conn[$database_flag];
		}else{
			$db = new \db_mysql(config("db/{$database_flag}"));
			$db->connect();
			return $this->conn[$database_flag] = $db;
		}
	}

	public function choose($db)
	{
		$this->db = $this->conn[$db];
		return $this;
	}

	public function fetch($sql, $args=[])
	{
		return $this->db->fetch_first($sql, $args);
	}
	
	public function fetch_all($sql, $args=[])
	{
		return $this->db->fetch_all($sql, $args);
	}
	
	public function result($sql, $args=[])
	{
		return $this->db->result_first($sql, $args);
	}

	public function query($sql, $args=[])
	{
		return $this->db->execute($sql, $args);
	}

	public function update($table, $data, $condition, $condition_args)
	{
		return $this->db->update($table, $data, $condition, $condition_args);
	}
	
	public function delete($table, $condition, $condition_args)
	{
		return $this->db->delete($table, $condition, $condition_args);	
	}
	
	public function insert($table, $data)
	{
		return $this->db->insert($table, $data);
	}
	
	public function insert_all($table, $data)
	{
		return $this->db->insert($table, $data, true);
	}
	
	public function replace($table, $data)
	{
		return $this->db->insert($table, $data, false, true);
	}
	
	public function replace_all($table, $data)
	{
		return $this->db->insert($table, $data, true, true);
	}
	
	public function begin()
	{
		return $this->db->beginTransaction();
	}
	
	public function commit()
	{
		return $this->db->commit();
	}
	
	public function rollback()
	{
		return $this->db->rollBack();
	}

	public function last_sql()
	{
		return $this->db->last_sql();
	}
}