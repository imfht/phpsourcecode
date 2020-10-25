<?php
/**
 * limitUserLogin
 * 限制同一用户的多终端登陆
 * 该函数依赖lib.base.php
 */
function limitUserLogin($u_id) {
  $u_id = intval($u_id);

  $arr['session_id'] = session_id();
  if ($session_id = get_col("user_session", "session_id", "u_id = " . $u_id)) {
    if ($session_id != $arr['session_id']) {
      return $GLOBALS['db']->query("UPDATE user_session SET session_id = '" . $arr['session_id'] . "' WHERE u_id = $u_id");
    }
  } else {
    $arr['u_id'] = $u_id;
    return $GLOBALS['db']->autoExecute("user_session", $arr, "INSERT");
  }
}

/**
 * 检测用户登录状态
 */
function checkUser($url='') {
  if (!empty($_COOKIE['token_user']) && !empty($_SESSION['token_user'])) {
    if ($_COOKIE['token_user'] == $_SESSION['token_user']) {
      if (session_id() == get_col("user_session", "session_id", "u_id = " . getUserToken('id'))) {
        setcookie('token_user', str_safe($_COOKIE['token_user']), time() + COOKIE_TIMEOUT);
        return ;
      } else {
        alert_href($GLOBALS['lang']['uc']['check']['conflict'], $url);
      }
    } else {
      alert_href($GLOBALS['lang']['uc']['check']['failed'], $url);
    }
  } else {
    alert_href($GLOBALS['lang']['uc']['check']['failed'], $url);
  }
}

/**
 * 设置用户标示便于进一步安全判断
 * 用户id
 * 用户名
 */
function setUserToken($u_id, $u_name) {
  $u_id = intval($u_id);

  $user_token = bin2hex($u_name . '+' . $u_id);
  setcookie('token_user', $user_token, time() + COOKIE_TIMEOUT);
  $_SESSION['token_user'] = $user_token;
}

/**
 * 用户退出
 */
function userLogout($url='') {
  $url = !empty($url) ? $url : 'cms_login.php';
  setcookie('token_user', '', time()-1);
  unset($_SESSION['token_user']);
  href($url);
  return ;
}

function getUserToken($t=0) {
  $arr = explode('+', hex_bin($_SESSION['token_user']));
  if ($t) {
    return $arr[1];
  } else {
    return $arr[0];
  }
}

// 短时间内次数限制
function times_limit($login_count, $var, $times=5) {
  $login_count = intval($login_count);
  $var = str_safe($var);
  $times = intval($times);
  $time = time();

  if (!isset($_SESSION[$login_count])) {
    $_SESSION[$login_count] = 0;
  }
  if (!isset($_SESSION[$var])) {
    $_SESSION[$var] = $time;
  }
  $_SESSION[$login_count]++;
  if (($_SESSION[$login_count] > $times) && (($time - $_SESSION[$var]) <= COOKIE_TIMEOUT)) {
    $_SESSION[$var] = $time;
    alert_back("短时间内请不要重复操作!");
  } elseif ($time - $_SESSION[$var] > COOKIE_TIMEOUT) {
    $_SESSION[$login_count] = 0;
  }
}

function getCartInfo($cart) {
  $exclude = array('rowid','options');
  $str = '';
  foreach ($cart as $val) {
    foreach ($val as $k => $v) {
      if (in_array($k, $exclude)!==false) {
        continue;
      }
      $str.= $v . ',';
    }
    $str = rtrm($str,",") . '|';
  }
  $str = rtrim($str,"|");
  return array(
    'u_id'=>getUserToken('id'),
    'c_info'=>$str,
    'c_qty'=>$cart['total_items'],
    'c_cost'=>$cart['cart_total'],
    'c_state'=>1,
    'c_date'=>gmtime()
    );
}

function getCartItem($t=1) {
  $str = $GLOBALS['db']->getOne("SELECT c_info FROM cart WHERE u_id = " . getUserToken('id'));
  $arr = explode("|", $str);
  foreach ($arr as $key => $value) {
    $arr_info = str_array($value);
    $res[$key]['id'] = $arr_info[0];
    $res[$key]['qty'] = $arr_info[1];
    $res[$key]['price'] = $arr_info[2];
    $res[$key]['name'] = $arr_info[3];
    if ($t) {
      $res[$key]['subtotal'] = $arr_info[4];
    }
  }
  return $res;
}

function getOrderInfo($cart) {
  $exclude = array('rowid','options');
  $str = '';
  foreach ($cart as $val) {
    foreach ($val as $k => $v) {
      if (in_array($k, $exclude)) {
        continue;
      }
      $str.= $v . ',';
    }
    $str = rtrm($str,",") . '|';
  }
  $str = rtrim($str,"|");
  return array(
    'u_id' => getUserToken('id'),
    'o_sn' => microtime_float(),
    'o_info' => $str,
    'o_qty' => $cart['total_items'],
    'o_cost' => $cart['cart_total'],
    'o_state' => 1,
    'o_date' => gmtime()
  );
}

function getOrderItem($id, $t=1) {
  $id = intval($id);

  $str = $GLOBALS['db']->getOne("SELECT o_info FROM order WHERE id = $id");
  $arr = explode("|", $str);
  foreach ($arr as $key => $value) {
    $arr_info = str_array($value);
    $res[$key]['id'] = $arr_info[0];
    $res[$key]['qty'] = $arr_info[1];
    $res[$key]['price'] = $arr_info[2];
    $res[$key]['name'] = $arr_info[3];
    if ($t) {
      $res[$key]['subtotal'] = $arr_info[4];
    }
  }
  return $res;
}

function getOrderState($id) {
  $id = intval($id);

  return $GLOBALS['db']->getOne("SELECT o_state FROM order WHERE id = $id");
}
