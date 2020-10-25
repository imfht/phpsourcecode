<?php
class Channel {
  private $db;
  public function __construct() {
    $this->db = $GLOBALS['db'];
  }
  // 获取频道数据
  public function getChannel($id) {
    return $this->db->getRow("SELECT * FROM channel WHERE id = $id");
  }
  //获取频道数据
  public function getCol($col, $id) {
    return $this->db->getOne("SELECT $col FROM channel WHERE id = $id");
  }
  // 获取频道子级
  public function getSon($id, $nav=1, $order="c_order ASC,id ASC") {
    if ($nav) {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = $id AND c_navigation = 1 ORDER BY $order");
    }  else {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = $id ORDER BY $order");
    }
    return $res;
  }
  // 获取频道同级
  public function getLevel($id, $nav=1, $order="c_order ASC,id ASC") {
    $cpid = $this->getCol('c_parent', $id);
    if ($nav) {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = $cpid AND c_navigation = 1 ORDER BY $order");
    } else {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = $cpid ORDER BY $order");
    }
    return $res;
  }
  // 获取上级频道
  public function getParent($id, $nav=1) {
    $cpid = $this->getCol('c_parent', $id);
    if ($nav) {
      $res = $this->db->getRow("SELECT * FROM channel WHERE id = $cpid AND c_navigation = 1");
    } else {
      $res = $this->db->getRow("SELECT * FROM channel WHERE id = $cpid");
    }
    return $res;
  }
  // 获取顶级频道
  public function getMain($id, $nav=1) {
    $cmid = $this->getCol('c_main', $id);
    if ($nav) {
      $res = $this->db->getRow("SELECT * FROM channel WHERE id = $cmid AND c_navigation = 1");
    }  else {
      $res = $this->db->getRow("SELECT * FROM channel WHERE id = $cmid");
    }
    return $res;
  }
  // 获取所有顶级频道
  public function getAllMain($nav=1, $order="c_order ASC,id ASC") {
    if ($nav) {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = 0 AND c_navigation = 1 ORDER BY $order");
    } else {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_parent = 0 ORDER BY $order");
    }
    return $res;
  }
  // 获取所有频道
  public function getAll($nav=1, $order="c_order ASC,id ASC") {
    if ($nav) {
      $res = $this->db->getAll("SELECT * FROM channel WHERE c_navigation = 1 ORDER BY $order");
    } else {
      $res = $this->db->getAll("SELECT * FROM channel ORDER BY $order");
    }
    return $res;
  }
  public function __destruct() {
  }
}
?>
