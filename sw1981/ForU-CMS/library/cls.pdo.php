<?php
class DataObject {
  protected $pdo;
  protected $res;
  private $lastID;

  public function __construct() {
    $this->connect(DATA_TYPE, DATA_HOST, DATA_NAME, DATA_USERNAME, DATA_PASSWORD);
  }

  public function connect($db_type, $db_host, $db_name, $db_user, $db_psw) {
    try {
      $this->pdo = new PDO("$db_type:host=$db_host;dbname=$db_name", $db_user, $db_psw);
      $this->pdo->query('set names utf8;');
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die ("Error!: " . $e->getMessage() . "<br/>");
    }
  }

  public function query($sql) {
    $sql = Sql::parse($sql);
    $res = $this->pdo->query($sql);
    $this->lastID = $this->pdo->lastInsertId();

    if ($res === false) {
      print_r($this->pdo->errorInfo());
      exit();
    }
    $this->res = $res;
    return $res;
  }

  public function getOne($sql, $limited = false) {
    if ($limited) {
      $sql = trim($sql . " LIMIT 1");
    }

    $res = $this->query($sql);

    if ($res !== false) {
      $row = !empty($res) ? $this->res->fetchColumn() : false;
      $this->res = $row;
      return $row!==false ? $row : '';
    } else {
      return false;
    }
  }

  public function getRow($sql, $limited = true) {
    if ($limited) {
      $sql = trim($sql . " LIMIT 1");
    }
    $res = $this->query($sql);
    if ($res !== false) {
      $this->res = $res->fetch(PDO::FETCH_ASSOC);
      return $this->res;
    } else {
      return false;
    }
  }

  public function getCol($sql) {
    $res = $this->query($sql);
    if ($res !== false) {
      $arr = array();
      while ($row = $res->fetchColumn()) {
        $arr[] = $row;
      }
      $this->res = $arr;
      return $arr;
    } else {
      return false;
    }
  }

  public function getAll($sql) {
    $res = $this->query($sql);
    if ($res !== false && $res !== null) {
      $this->res = $row = $res->fetchAll(PDO::FETCH_ASSOC);
      return $row;
    } else {
      return false;
    }
  }

  public function autoExecute($table, $field_values, $mode = 'INSERT', $where = '') {
    $field_names = $this->getCol('DESC ' . DATA_PREFIX . $table);
    $sql = '';
    if ($mode == 'INSERT') {
      $fields = $values = array();
      foreach ($field_names as $value) {
        if (array_key_exists($value, $field_values)) {
          $fields[] = $value;
          $values[] = '\'' . $field_values[$value] . '\'';
        }
      }
      if (!empty($fields)) {
        $sql = 'INSERT INTO ' . $table . ' (' . implode(', ', $fields) . ') VALUES (' . implode(', ', $values) . ')';
      }
    } else {
      $sets = array();
      foreach ($field_names as $value) {
        if (array_key_exists($value, $field_values)) {
          $sets[] = $value . ' = \'' . $field_values[$value] . '\'';
        }
      }
      if (!empty($sets)) {
        $sql = 'UPDATE ' . $table . ' SET ' . implode(', ', $sets) . ' WHERE ' . $where;
      }
    }
    if ($sql) {
      $this->res = $res = $this->query($sql);
      return $res;
    } else {
      return false;
    }
  }

  public function getLastID() {
    return  $this->lastID;
  }

  public function getMaxID($table, $field, $sql = '') {
    if ($sql != '') {
      $res = $this->getOne("SELECT MAX({$field}) FROM {$table} WHERE {$sql}");
    } else {
      $res = $this->getOne("SELECT MAX({$field}) FROM {$table}");
    }
    $this->res = $res = !empty($res) ? $res : 0;
    return $res;
  }

  public function close() {
    $this->pdo = null;
  }

  public function truncate($table) {
    return $this->query('TRUNCATE TABLE ' . $table);
  }

  public function drop($table) {
    return $this->query('DROP TABLE ' . $table);
  }

  public function dropDB($db) {
    return $this->query('DROP DATABASE ' . $db);
  }

  public function __destruct() {
    $this->close();
  }
}
