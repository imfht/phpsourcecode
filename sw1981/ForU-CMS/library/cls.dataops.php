<?php
// 依赖于 lib.base.php
class DataOps {
  private $_db;
  public function __construct() {
    $this->_db = $GLOBALS['db'];
  }

  // $sta[0/1] 0不显示信息 1为显示信息($msg为空时显示默认成功信息)
  public function ops($sql, $code='', $sta=0, $msg='', $prm='') {
    if ($this->_db->query($sql)) {
      if ($code) {
        admin_log($code);
      }
      if (strpos($sql, 'channel')!==false) {
        update_channel();
      }
      $this->href($sta, $msg, $prm);
    } else {
      alert_back($GLOBALS['lang']['msg_failed']);
    }
  }

  public function href($sta=0, $msg='', $prm='') {
    $arr = explode('/', $_SERVER['PHP_SELF']);
    $str = end($arr);
    if (substr_count($str, '_') > 1) {
      list($pre, $main) = explode('_', $str);
      $url = $pre . '_' . $main . ".php?$prm";
    } else {
      $url = $str . "?$prm";
    }
    $sta ? alert_href($sta && $msg ? $msg : $GLOBALS['lang']['msg_success'], $url) : href($url);
  }
}
