<?php
class Model{
	protected $db;
	protected $res;
	public $TableName;
	public $TablePrefix;
	protected $TrueTableName;
	public static function M($table_name, $params = []) {
		//add cache
		$params['db_dsn'] = (!empty($params['db_dsn']))?$params['db_dsn']:Conf::get('db_dsn');
		$params['db_name'] = (!empty($params['db_name']))?$params['db_name']:Conf::get('db_name');
		$params['db_user'] = (!empty($params['db_user']))?$params['db_user']:Conf::get('db_user');
		$params['db_pass'] = (!empty($params['db_pass']))?$params['db_pass']:Conf::get('db_pass');
		$params['db_charset'] = (!empty($params['db_charset']))?$params['db_charset']:Conf::get('db_charset');
		//check
		if (empty($params['db_name']) || empty($params['db_dsn']) || empty($params['db_user']) || empty($table_name)) {
			return false;
		}
		if (!empty($params['db_charset'])) {
			$params['db_dsn'] .= 'charset='. $params['db_charset'] .';';
		}
		if (!class_exists(@App::$Objects[$table_name . 'Model'], false)) {
			App::$Objects[$table_name . 'Model'] = new Model();
			//build the connectdb_name
			try {
				App::$Objects[$table_name . 'Model']->db = New PDO($params['db_dsn'] . 'dbname=' . $params['db_name'], $params['db_user'], $params['db_pass']);
			} catch(Exception $e) {
				return false;
			}
			App::$Objects[$table_name . 'Model']->TableName = $table_name;
			App::$Objects[$table_name . 'Model']->TablePrefix = (!empty($params['table_prefix']))?$params['table_prefix']:Conf::get('table_prefix');
			App::$Objects[$table_name . 'Model']->TrueTableName = App::$Objects[$table_name . 'Model']->TablePrefix . App::$Objects[$table_name . 'Model']->TableName;
		}

		return App::$Objects[$table_name . 'Model'];
	}
	public function query($sql){
        $res = $this->db->query($sql);
        if($res){
            $this->res = $res;
        }
    }

    public function exec($sql){
        $res = $this->db->exec($sql);
        if($res){
            $this->res = $res;
        }
    }

    public function fetchAll(){
        return $this->res->fetchAll();
    }

    public function fetch(){
        return $this->res->fetch();
    }

    public function fetchColumn(){
        return $this->res->fetchColumn();
    }

    public function lastInsertId(){
        return $this->res->lastInsertId();
    }

	public function insert($vars) {
		if(is_array($vars)){
			foreach ($vars as $key => $value) {
				$keys[] = $key;
				$vals[] = '\'' . $value . '\'';
			}
			$key = implode(', ', $keys);
            $val = implode(', ', $vals);
        }
        $sql = "INSERT into ". $this->TrueTableName ." ($key) VALUES ($val)";
        $this->exec($sql);
        if ($this->res > 0) {
        	return true;
        }
        return false;
	}
	public function delete($condition) {
		$c = '';
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				//exit($v);
				if (is_string($v)) {
					$c .= " AND $k=\"$v\"";
				} elseif (is_array($v)) {
					//TODO
				}
			}
		} elseif (is_string($condition)) {
			$c = " AND " . $condition;
		}
		$sql = "delete FROM ". $this->TrueTableName ." WHERE 1=1 $c";
		$this->exec($sql);
		if ($this->res > 0) {
			return true;
		}
        return false;
	}
	public function update($condition, $vars) {
		$c = '';
		$var = '';
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				//exit($v);
				if (is_string($v)) {
					$c .= " AND $k=\"$v\"";
				} elseif (is_array($v)) {
					//TODO
				}
			}
		} elseif (is_string($condition)) {
			$c = " AND " . $condition;
		}
		if(is_array($vars)){
			foreach ($vars as $key => $value) {
				if ($value==end($vars)) {
					$var .= $key . '=' . '\'' . $value . '\'';
				} else {
					$var .= $key . '=' . '\'' . $value . '\' , ';
				}
			}
        }
        $sql = "UPDATE ". $this->TrueTableName ." SET $var WHERE 1=1 $c";
        $this->query($sql);
        if ($this->res->rowCount() > 0) {
        	return true;
        }
        return false;
	}
	public function count($condition = "") {
		$c = '';
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				//exit($v);
				if (is_string($v)) {
					$c .= " AND $k=\"$v\"";
				} elseif (is_array($v)) {
					//TODO
				}
			}
		} elseif (is_string($condition) && !empty($condition)) {
			$c = " AND " . $condition;
		}
		$sql = "SELECT COUNT(*) FROM ". $this->TrueTableName ." WHERE 1=1 $c";
		$this->query($sql);
		return $this->fetchColumn();
	}
	public function find($condition, $fields="*") {
		$c = '';
		if(is_array($fields)){
            $fields = implode(', ', $fields);
        }
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				//exit($v);
				if (is_string($v)) {
					$c .= " AND $k=\"$v\"";
				} elseif (is_array($v)) {
					//TODO
				}
			}
		} elseif (is_string($condition) && !empty($condition)) {
			$c = " AND " . $condition;
		}
		$sql = "SELECT $fields FROM ". $this->TrueTableName ." WHERE 1=1 $c limit 0, 1";
		//exit($sql);
		$this->query($sql);
		if ($this->res == null) {
			return false;
		}
		$return = $this->fetch();
		return $return;
	}
	public function select($condition, $order = '', $limit = '', $fields = "*") {
		$c = '';
		if(is_array($fields)){
            $fields = implode(', ', $fields);
        }
		if (is_array($condition)) {
			foreach ($condition as $k => $v) {
				//exit($v);
				if (is_string($v)) {
					$c .= " AND $k=\"$v\"";
				} elseif (is_array($v)) {
					//TODO
				}
			}
		} elseif (is_string($condition) && !empty($condition)) {
			$c = " AND " . $condition;
		}
		$sql = "SELECT $fields FROM ". $this->TrueTableName ." WHERE 1=1 $c";
		if (!empty($order)) {
			$sql .= " ORDER BY ". $order;
		}
		if (!empty($limit)) {
			$sql .= ' LIMIT ' . $limit;
		}
		$this->query($sql);
		if ($this->res == null) {
			return false;
		}
		$return = $this->fetchAll();
		return $return;
	}
}