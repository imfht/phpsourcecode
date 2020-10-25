<?php
class User {
  private $db;
  private $login_url;
  private $user_id;

  public function __construct() {
    $this->db = $GLOBALS['db'];
  }

  // 检测用户状态
  public function check($url='') {
    $this->login_url = $url ? $url : 'user.php?act=login';
    checkUser($url);
  }

  // 用户注册
  // hash密码处理需要php5.5及以上
  public function reg($t='') {
    $data['u_psw'] = isset($_POST['u_psw']) ? pswHash($_POST['u_psw']) : '';
    if (check($data['u_email'], 'email')) {
      $data['u_email'] = isset($_POST['u_email']) ? str_safe($_POST['u_email']) : '';
    } else {
      $data['u_name'] = isset($_POST['u_email']) ? str_safe($_POST['u_email']) : '';
    }
    $res = $this->db->autoExecute("user", $data, "INSERT");

    if ($t=='ajax') {
      if ($res) {
        $res['err'] = 'n';
        $res['msg'] = $GLOBALS['lang']['login']['success'];
      } else {
        $res['err'] = 'y';
        $res['msg'] = $GLOBALS['lang']['login']['not_match'];
        $GLOBALS["cms[{TOKEN_NAME}]"]->getToken();
      }
      die(json_encode($res, JSON_NUMERIC_CHECK));
    } else {
      if ($res) {
        return true;
      } else {
        return false;
      }
    }
  }

  // 用户登陆
  public function login($t='') {
    $u_email = isset($_POST['u_email']) ? str_safe($_POST['u_email']) : '';
    $u_psw = isset($_POST['u_psw']) ? str_safe($_POST['u_psw']) : '';
    if (check($u_email, 'email')) {
      $res = $this->db->getRow("SELECT * FROM user WHERE u_email = '$u_email'");
    } else {
      $res = $this->db->getRow("SELECT * FROM user WHERE u_mobile = '$u_email'");
    }

    if ($t=='ajax') {
      if (pswVerify($u_psw, $res['u_psw'])) {
        $res['err'] = 'n';
        $res['msg'] = $GLOBALS['lang']['login']['success'];
      } else {
        $res['err'] = 'y';
        $res['msg'] = $GLOBALS['lang']['login']['not_match'];
        $GLOBALS["cms[{TOKEN_NAME}]"]->getToken();
      }
      die(json_encode($res, JSON_NUMERIC_CHECK));
    } else {
      if (pswVerify($u_psw, $res['u_psw'])) {
        limitUserLogin($res['id']); // 用户多终端登陆限制
        setUserToken($res['id'], iif($res['u_name'], $res['u_email'])); // 设置用户标示
        $this->updateLastLogin($res['id']);
        return true;
      }
      return false;
    }
  }

  // 用户注销
  public function logout() {
    userLogout();
  }

  // 获取用户信息
  public function getUser() {
    $res = $this->db->getRow("SELECT * FROM user WHERE id = '" . $this->user_id . "'");
    $res['u_name'] = stripslashes($res['u_name']);
    $res['u_tname'] = stripslashes($res['u_tname']);
    $res['u_question'] = stripslashes($res['u_question']);
    $res['u_answer'] = stripslashes($res['u_answer']);
    return $res;
  }

  // 地址簿
  public function addAddr($arr) {
    $arr['u_id'] = $this->user_id;
    if ($this->db->autoExecute('user_address', $arr, 'INSERT')) {
      $new_id = $this->db->getLastID();
      if ($this->db->getOne("SELECT id FROM user WHERE id = " . $this->user_id)===false) {
        setDefaultAddr($new_id);
      }
      return true;
    } else {
      return false;
    }
  }
  public function editAddr($arr, $id) {
    $arr['u_id'] = $this->user_id;
    return $this->db->autoExecute('user_address', $arr, 'UPDATE', "u_id = " . $this->user_id . " AND id = $id");
  }
  public function getAddr($order="ORDER BY ua_active DESC,id ASC") {
    return $this->db->getAll("SELECT * FROM user_address WHERE u_id = " . $this->user_id . " " . $order);
  }
  public function removeAddr($id) {
    return $this->db->query("DELETE FROM user_address WHERE u_id = " . $this->user_id . " AND id = $id");
  }
  public function setDefaultAddr($id) {
    return $this->db->query("UPDATE user SET ua_active = 1 WHERE id = " . $this->user_id . " AND id = $id");
  }

  // 订单
  // 订单状态 0未付款 1已付款 2等待收货 3确认收货 4交易完成 6撤销 7退款中 8退款完成 9已撤销
  public function addOrder($arr) {
    $res = getOrderInfo($arr);
    return $this->db->autoExecute('order', $res, 'INSERT');
  }
  public function cancelOrder($id) {
    return $this->db->query("UPDATE order SET o_state = 6 WHERE u_id = " . $this->user_id . " AND id = $id");
  }
  public function cancelMsg($arr) {
    // feedback数据表内f_type=999
    $arr['f_uid'] = $this->user_id;
    $arr['f_type'] = 999;
    return $this->db->autoExecute('feedback', $arr, 'INSERT');
  }
  public function removeOrder($id) {
    $state = getOrderState($id);
    if ($state>=7 || $state==0) {
      return $this->db->query("DELETE FROM order WHERE u_id = " . $this->user_id . " AND id = $id");
    } else {
      alert_back($GLOBALS['lang']['order']['remove_failed']);
    }
  }
  public function addRate($arr) {
    // f_id订单号;f_star评星字串;f_content留言内容;f_answer留言回复;f_date留言期;f_adate回复日期;f_ok是否回复
    $arr['f_uid'] = $this->user_id;
    $arr['f_type'] = 88;
    return $this->db->autoExecute('feedback', $arr, 'INSERT');
  }

  // 增加积分函数
  public function addPoint($point) {
    non_numeric_back($point, $GLOBALS['lang']['uc']['point']['error']);
    //计算总积分
    if ($this->db->query("UPDATE user SET u_point = u_point + ".intval($point)." WHERE id = " . $this->user_id)) {
      url_back();
    }
  }

  // 扣除积分函数
  public function costPoint($point, $u_point) {
    non_numeric_back($point, $GLOBALS['lang']['uc']['point']['error']);
    //判断是否数字
    if ($point > $u_point) {
      url_back($GLOBALS['lang']['uc']['point']['cost_faild']);
    } else {
      $user_point = intval($u_point) - intval($point);
      //计算总积分
      if ($this->db->query("UPDATE user SET u_point = '$user_point' WHERE id = " . $this->user_id)) {
        url_back();
      }
    }
  }

  // 更新登录时间
  public function updateLastLogin() {
    $this->db->query("UPDATE user SET last_login = '" . gmtime() . "' WHERE id = " . $this->user_id);
  }

  public function __destruct() {
    unset($this->user_id);
    unset($this->db);
    unset($this->login_url);
  }
}
