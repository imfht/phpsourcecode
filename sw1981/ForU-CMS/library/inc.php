<?php
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
set_time_limit(20);
@session_start();
ini_set("session.cookie_httponly", 1);
header('Content-type: text/html;charset=UTF-8');
define('LIB_PATH', str_replace("\\", '/', dirname(__FILE__) . '/'));
define('ROOT_PATH', str_replace("library/", '', LIB_PATH));
define('ADDON_PATH', str_replace("library/", 'addon/', LIB_PATH));

// 引入文件
include ROOT_PATH . 'config/config.php';
include ROOT_PATH . 'config/data.php';
include ROOT_PATH . 'config/smtp.php';
include ROOT_PATH . 'config/map.php';

include_once LIB_PATH . 'cls.smtp.php';
include_once LIB_PATH . 'cls.hook.php';
include_once LIB_PATH . 'lib.time.php';

include_once LIB_PATH . 'lib.file.php';
include_once LIB_PATH . 'lib.url.php';
include_once LIB_PATH . 'lib.base.php';
include_once LIB_PATH . 'library.php';
include_once LIB_PATH . 'lib.user.php';

include_once LIB_PATH . 'cls.sql.php';
include_once LIB_PATH . 'cls.pdo.php';

// engine
$db = new DataObject();
$GLOBALS['db'] = &$db;

//cms_system
$GLOBALS['cms'] = $cms = $db->getRow("SELECT * FROM system WHERE id = 1");

// 语言
$lang = LANG_DIR . '/' . (!empty($_COOKIE['cms']['lang']) ? $_COOKIE['cms']['lang'] : $cms['s_lang']) . '/';

// 加载语言
include ROOT_PATH . $lang . 'common.php';
include ROOT_PATH . $lang . 'admin.php';
include ROOT_PATH . $lang . 'priv.php';
$GLOBALS['lang'] = &$_lang;

// 闭站判断
if ($cms['s_state'] && !is_admin()) {
  die($_lang['sys']['site_close']);
}

// token
if (TOKEN_ON && !is_admin()) {
  include_once LIB_PATH . 'cls.token.php';
  $token = new Token();
  $token->getToken();
}

// 模板路径
$t_path = TPL_DIR . '/' . (!empty($_COOKIE['cms']['template_id']) ? $_COOKIE['cms']['template_id'] : $cms['s_template']) . '/';
$GLOBALS['t_path'] = &$t_path;

// xxs
//if (!check_domain($cms['s_domain'])) {
//  die($_lang['illegal']);
//}

// 购物车
if (CART) {
  include_once LIB_PATH . 'cls.cart.php';
  if (!isset($cart) && getUserToken('id')) {
    $cart = new Cart();
  }
}

// common
$_COOKIE['cms']['user_id'] = isset($_COOKIE['cms']['user_id']) ? $_COOKIE['cms']['user_id'] : 0;
$_COOKIE['cms']['user_name'] = isset($_COOKIE['cms']['user_name']) ? $_COOKIE['cms']['user_name'] : 0;
$_COOKIE['cms']['remember'] = isset($_COOKIE['cms']['remember']) ? $_COOKIE['cms']['remember'] : 0;

$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$act = !empty($_REQUEST['act']) ? str_safe($_REQUEST['act']) : '';

// addon
$arr_addons = $db->getAll("SELECT DISTINCT a_func FROM addon");
foreach ($arr_addons as $val) {
  $arr_addon = $db->getAll("SELECT * FROM addon WHERE a_func = '" . $val['a_func'] . "' AND a_enable = 1 ORDER BY a_order ASC,id ASC");
  foreach ($arr_addon as $v) {
    $data_addon[$val['a_func']][] = $v['a_name'];
  }
}
Hook::import($data_addon);
unset($arr_addons);
unset($arr_addon);
unset($data_addon);

// admin
if (is_admin()) {
  include_once LIB_PATH . 'cls.dataops.php';
  include_once LIB_PATH . 'lib.admin.php';
  if (in_array(self_name(), json_decode(ADMIN_EXCLUDE, true))===false) {
    include ROOT_PATH . ADMIN_DIR . '/cms_check.php';
  }
}
// head
elseif (is_detail()) {
  non_numeric_href($id, $_lang['illegal'], './');
  include_once LIB_PATH . 'cls.detail.php';
  include_once LIB_PATH . 'cls.channel.php';
  $objDetail = new Detail();
  $detail = $objDetail->getDetail($id);
  $objChannel = new Channel();
  $channel = $objChannel->getChannel($detail['d_parent']);

  $title = $detail['d_name'] . '-' . $channel['c_name'] . '-' . $cms['s_name'];
  $keywords = !empty($detail['d_keywords']) ? $detail['d_keywords'] : $detail['d_name'];
  $description = !empty($detail['d_description']) ? str_cut(str_text($detail['d_description'], 1), 220) : str_cut(str_text($cms['s_description'], 1), 220);
}
elseif (is_channel()) {
  non_numeric_href($id, $_lang['illegal'], './');
  include_once LIB_PATH . 'cls.channel.php';
  $objChannel = new Channel();
  $channel = $objChannel->getChannel($id);

  $title = $channel['c_name'] . '-' . $cms['s_name'];
  $keywords = !empty($channel['c_keywords']) ? $channel['c_keywords'] : $channel['c_name'];
  $description = !empty($channel['c_description']) ? $str_cut(str_text($channel['c_description'], 1), 220) : str_cut(str_text($cms['s_description'], 1), 220);
}
else {
  $title = !empty($cms['s_seoname']) ? $cms['s_name'] . '-' . $cms['s_seoname'] : $cms['s_name'];
  $keywords = $cms['s_keywords'];
  $description = str_cut(str_text($cms['s_description'], 1), 220);
}

// 默认分页结构
$cms['page_structure'] = json_decode(PAGE_STRUCTURE, true);
