<?php
/*
* PHPCrazy 安装程序文件
* 
* Package:    PHPCrazy
* Link:     http://zhangyun.org/
* Author:     Crazy <mailzhangyun@qq.com>
* Copyright:    2014-2015 Crazy
* License:    Please read the LICENSE file.
*/

function installHeader($title, $nav = '') {

echo <<<HTML
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{$title}</title>
    <link href="./css/bootstrap.min.css" rel="stylesheet">
    <link href="./css/jumbotron-narrow.css" rel="stylesheet">
    <!--[if lt IE 9]><script src="./js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="./js/ie-emulation-modes-warning.js"></script>
    <!--[if lt IE 9]>
      <script src="./js/html5shiv.min.js"></script>
      <script src="./js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="container">
      <div class="header">
        {$nav}
        <h3 class="text-muted">{$title}</h3>
      </div>
HTML;
}

function installFooter() {

  date_default_timezone_set("PRC");
  
  $date = date('Y');

  echo <<<HTML
      <footer class="footer text-center">
        <p>&copy; <a href="http://zhangyun.org">PHPCrazy</a> {$date}</p>
      </footer>
    </div>
    <script src="./js/ie10-viewport-bug-workaround.js"></script>
  </body>
</html>
HTML;

  exit;
}

function installWelcome() {

installHeader(L('安装向导'));

$title = L('安装 你好');
$Welcome = L('安装 欢迎界面');
$install = L('安装 现在安装');
echo <<<HTML
      <div class="jumbotron">
        <h1 class="text-center">{$title}</h1>
        <p class="lead">{$Welcome}</p>
        <p><a class="btn btn-lg btn-success" href="./install.php?setup=db" role="button">{$install}</a></p>
      </div>
HTML;

installFooter();

}

function installDB() {
    $lang_dbinfo = L('安装 数据库信息');
    $lang_type = L('安装 数据库类型');
    $lang_dbhost = L('安装 数据库地址');
    $lang_dbname = L('安装 数据库名');
    $lang_tableprefix = L('安装 表前缀');
    $lang_dbuser = L('安装 数据库用户');
    $lang_dbpass = L('安装 数据库密码');
    $lang_next = L('安装 下一步');

    $submit = isset($_POST['submit']) ? true : false;

    $select_db = '';

    foreach ($GLOBALS['db_list'] as $value => $name) {
      
      $selected = ($value == 'sqlite') ? ' selected = "selected"': '';

      $select_db .= '<option value="' . $value . '"'.$selected.'>' . $name .'</option>' . "\n\t\t";

    }


    if ($submit) {

      $db = isset($_POST['db']) ? $_POST['db'] : '';
      $_SESSION['install_config']['db'] = isset($GLOBALS['db_list'][$db]) ? $db : 'sqlite';

      $db_host = isset($_POST['db_host']) ? $_POST['db_host'] : '';
      $_SESSION['install_config']['db_host'] = empty($db_host) ? (($_SESSION['install_config']['db'] == 'sqlite') ? '' : 'localhost') : $db_host;

      $db_name = isset($_POST['db_name']) ? $_POST['db_name'] : '';
      $_SESSION['install_config']['db_name'] = $db_name;

      $db_user = isset($_POST['db_user']) ? $_POST['db_user'] : '';
      $_SESSION['install_config']['db_user'] = $db_user;
      
      $table_prefix = isset($_POST['table_prefix']) ? $_POST['table_prefix'] : '';
      $_SESSION['install_config']['table_prefix'] = $table_prefix;

      $db_pass = isset($_POST['db_pass']) ? $_POST['db_pass'] : '';
      $_SESSION['install_config']['db_pass'] = $db_pass;

      header('Location: install.php?setup=admin');

    }

installHeader(L('安装向导'));

echo <<<HTML
      <div class="jumbotron">
        <h1 class="text-center">{$lang_dbinfo}</h1>
        <form action="install.php?setup=db" method="post" class="form-horizontal" role="form">
          <div class="form-group">
            <label for="inputDB" class="col-sm-2 control-label">{$lang_type}</label>
            <div class="col-sm-10">
              <select id="inputDB" class="form-control" name="db">
                {$select_db}
              </select>
            </div>
          </div>
          <div class="form-group"> 
            <label for="inputDBhost" class="col-sm-2 control-label">{$lang_dbhost}</label>
            <div class="col-sm-10">
              <input type="text" name="db_host" class="form-control" id="inputDBhost" placeholder="localhost" value="" />
            </div>
          </div>          
          <div class="form-group"> 
            <label for="inputDBname" class="col-sm-2 control-label">{$lang_dbname}</label>
            <div class="col-sm-10">
              <input type="text" name="db_name" class="form-control" id="inputDBname" value="">
            </div>
          </div>
          <div class="form-group"> 
            <label for="inputTableprefix" class="col-sm-2 control-label">{$lang_tableprefix}</label>
            <div class="col-sm-10">
              <input type="text" name="table_prefix" class="form-control" id="inputTableprefix" value="crazy_">
            </div>
          </div>          
          <div class="form-group"> 
            <label for="inputDBuser" class="col-sm-2 control-label">{$lang_dbuser}</label>
            <div class="col-sm-10">
              <input type="text" name="db_user" class="form-control" id="inputDBuser" value="">
            </div>
          </div>
          <div class="form-group">
            <label for="inputDBpass" class="col-sm-2 control-label">{$lang_dbpass}</label>
            <div class="col-sm-10">
              <input type="password" name="db_pass" class="form-control" id="inputDBpass" value="">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="submit" name="submit" class="btn btn-default" value="{$lang_next}">
            </div>
          </div>
        </form>
      </div>
HTML;

installFooter();
}

function installAdmin() {

  $lang_username = L('安装 用户名');
  $lang_sitename = L('安装 网站标题');
  $lang_email = L('安装 邮箱');
  $lang_id = L('安装 起始ID');
  $lang_pass = L('安装 密码');
  $lang_finish_install = L('安装 完成安装');


  $submit = isset($_POST['submit']) ? true : false;

  $inputUsernameHTML = <<<HTML

          <div class="form-group"> 
            <label for="inputUsername" class="col-sm-2 control-label">{$lang_username}</label>
            <div class="col-sm-10">
              <input type="text" name="username" class="form-control" id="inputUsername" value="" placeholder="admin">
            </div>
          </div>
HTML;

  if ($submit) {

    $sitename = isset($_POST['sitename']) ? $_POST['sitename'] : '';
    $_SESSION['install_config']['sitename'] = $sitename;

    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $_SESSION['install_config']['system_mail'] = $email;

    $id = isset($_POST['id']) ? $_POST['id'] : 1;
    $_SESSION['install_config']['id'] = abs(intval($id));

    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $_SESSION['install_config']['password'] = $password;

    $username = isset($_POST['username']) ? $_POST['username'] : '';
    $_SESSION['install_config']['username'] = $username;

    $error = Vusername($username);

    if ($error['error']) {
      
      $inputUsernameHTML = <<<HTML
      <div class="form-group has-error has-feedback">
        <label class="col-sm-2 control-label" for="inputUsername">{$lang_username}</label>
        <div class="col-sm-10">
          <input type="text" name="username" class="form-control" id="inputUsername" value="" placeholder="{$error['error_msg']}" aria-describedby="inputUsernameStatus">
          <span class="glyphicon glyphicon-remove form-control-feedback" aria-hidden="true"></span>
          <span id="inputUsernameStatus" class="sr-only">(error)</span>
        </div>
      </div>
HTML;
    } else {

      header('Location: install.php?setup=finish');

    }

  }

installHeader(L('安装向导'));

echo <<<HTML
      <div class="jumbotron">
        <form action="install.php?setup=admin" method="post" class="form-horizontal" role="form">  
          <div class="form-group"> 
            <label for="inputSitename" class="col-sm-2 control-label">{$lang_sitename}</label>
            <div class="col-sm-10">
              <input type="text" name="sitename" class="form-control" id="inputSitename" value="">
            </div>
          </div>
          <div class="form-group"> 
            <label for="inputEmail" class="col-sm-2 control-label">{$lang_email}</label>
            <div class="col-sm-10">
              <input type="text" name="email" class="form-control" id="inputEmail" placeholder="example@domain.com" value="" />
            </div>
          </div>
          <div class="form-group">
            <label for="inputID" class="col-sm-2 control-label">{$lang_id}</label>
            <div class="col-sm-10">
              <input type="text" name="id" class="form-control" id="inputID" placeholder="1" value="" />
            </div>
          </div>
          {$inputUsernameHTML}
          <div class="form-group">
            <label for="inputPassword" class="col-sm-2 control-label">{$lang_pass}</label>
            <div class="col-sm-10">
              <input type="password" name="password" class="form-control" id="inputPassword" value="">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="submit" name="submit" class="btn btn-default" value="{$lang_finish_install}">
            </div>
          </div>
        </form>
      </div>
HTML;

installFooter();

}

function installFinish() {

  // 指定数据库
  try {
    
    switch ($_SESSION['install_config']['db']) {

      case 'mysql':

        $db_type = 'mysql';

        $dsn = 'mysql:host=' . $_SESSION['install_config']['db_host'] . ';dbname=' . $_SESSION['install_config']['db_name'];
        
        break;
      
      case 'sqlite':

        $db_type = 'sqlite';

        $SQLiteDir = str_replace('install', '', dirname(__FILE__));

        $dsn = 'sqlite:'.$SQLiteDir.$_SESSION['install_config']['db_host'].$_SESSION['install_config']['db_name'];
        
        break;

      default:
        
        installError(L('安装 ！请指定数据库'));
        
        break;
    }

    // 尝试链接数据库
    $PDO = new PDO($dsn, $_SESSION['install_config']['db_user'], $_SESSION['install_config']['db_pass']);
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SESSION['install_config']['db'] == 'mysql') {
        
        $PDO->exec('set names utf8');
    }

    // 开启事物处理
    $PDO->beginTransaction();

    // 把表写入到数据库中
    $sql_query = file_get_contents($GLOBALS['db_list'][$_SESSION['install_config']['db']] . '.sql');

    $sql_query = SQLinArray($sql_query, 'crazy_', $_SESSION['install_config']['table_prefix'], ';');

    foreach ($sql_query as $sql) {
      
      $PDO->exec($sql);

    }

    // 把数据写入到数据库中
    $sql_query = file_get_contents('data.sql');

    $sql_query = SQLinArray($sql_query, 'crazy_', $_SESSION['install_config']['table_prefix'], ';');

    foreach ($sql_query as $sql) {
      
      $PDO->exec($sql);

    }

    // 更新网站的一些设置
    $ConfigArray = array(
      'sitename' => $_SESSION['install_config']['sitename'],
      'author' => $_SESSION['install_config']['username'],
      'lang' => $_SESSION['install_config']['lang'],
      'system_mail' => $_SESSION['install_config']['system_mail'],
      'app_path' => str_replace('install', '', str_replace('install', '', dirname($_SERVER['PHP_SELF'])))
    );

    foreach ($ConfigArray as $config_key => $config_value) {
        
        UpConfig($config_key, $config_value, $PDO);
    }

    $NewSid = mksid($_SESSION['install_config']['id'], $_SESSION['install_config']['username'], $_SESSION['install_config']['password']);
    
    // 重置管理员信息

    $sql = 'INSERT INTO ' . $_SESSION['install_config']['table_prefix'] . "users (id, username, password, email, sid, activation_key, regtime, auth)
      VALUES (" . $_SESSION['install_config']['id'] . ", '" . $_SESSION['install_config']['username'] . "', '" . md5($_SESSION['install_config']['password']) . "', '', '" . $NewSid . "', '', " . time() . ", 2)";

    $PDO->exec($sql);

    // 把数据库信息写入到#config.php
    $ConfigData = '<?php'."\n";
    $ConfigData .= 'define(\'DSN\', \''.$dsn.'\');'."\n";
    $ConfigData .= 'define(\'DB_TYPE\', \''.$db_type.'\');'."\n";
    $ConfigData .= 'define(\'DB_USER\', \''.$_SESSION['install_config']['db_user'].'\');'."\n";
    $ConfigData .= '$DB_PASS = \''.$_SESSION['install_config']['db_pass'].'\';'."\n";
    $ConfigData .= 'define(\'TABLE_PREFIX\', \''.$_SESSION['install_config']['table_prefix'].'\');'."\n";

    if ($_SESSION['install_config']['db'] == 'mysql') {
      
      $ConfigData .= 'define(\'DB_ENCODE\', true);'."\n";
    }

    $ConfigData .= 'define(\'INSTALL_FINISH\', true);'."\n";
    $ConfigData .= '?'.'>';

    $fp = @fopen('../#config.php', 'w');
    @fwrite($fp, $ConfigData, strlen($ConfigData));
    @fclose($fp);

    require_once '../includes/lib/Session.class.php';

    $S = new Session();

    $S->Login($NewSid);

    session_destroy();

    header('Location: ../admin.php');

    // 如果没有出错则提交
    $PDO->commit();

    exit;

  } catch (PDOException $e) {
    
    $PDO->rollback();

    installError($e->getMessage().' 在 '.basename($e->getFile()).' 第 ' .$e->getLine() .' 行');

  }

}

function UpConfig($config_name, $config_value, &$PDO) {

  try {

    $sql = 'UPDATE ' . $_SESSION['install_config']['table_prefix'] . "config
      SET config_value = :config_value 
      WHERE config_name = :config_name";

    $result = $PDO->prepare($sql);

    $result->execute(array(
      ':config_value' => $config_value,
      ':config_name' => $config_name)
    );

  } catch (PDOException $e) {

    installError($e->getMessage().' 在 '.basename($e->getFile()).' 第 ' .$e->getLine() .' 行');

  }

}

function mksid($id, $name, $pass) {
  return strtr(base64_encode(str_shuffle(base64_encode(md5(md5($name,true).md5(microtime(),true).md5($pass,true),true))).base64_encode(pack('V',$id))), array('+'=>'-', '/'=>'_','='=> ''));
}

function installError($errorMsg) {
  
  ob_clean();

  installHeader(L('安装 安装过程出错'));

  echo <<<HTML
      <div class="jumbotron">
        <p>{$errorMsg}</p>
      </div>
HTML;
  
  installFooter();

}

function Vusername($username) {

  if ($username == '') {

    return array('error' => true, 'error_msg' => L('！用户名不能为空'));
  }

  if (!preg_match('/[^0-9]/', $username)) {

    return array('error' => true, 'error_msg' => L('！用户名不能为全数字'));
  }

  // 去除空格
  $username = preg_replace('#\s+#', ' ', trim($username));
  
  // 非法字符
  $illegal_username = array('*', '"', '<', '>', '-', ';', '=', ',', '`', '&', '#', '(', ')', "\\", '%', '$');
  
  foreach ($illegal_username as $illegal_value) {
    
    if (strstr($username, $illegal_value)) {

      return array('error' => true, 'error_msg' => L('！用户名带有非法字符'));
    }
  }

  // 用户名小于12字符
  if (mb_strlen($username, 'UTF-8') > 12) {

    return array('error' => true, 'error_msg' => L('！用户名太长'));
  }

  return array('error' => false, 'error_msg' => '');
}

function L($lang_key) {

  if (isset($GLOBALS['lang'][$lang_key])) {

    return $GLOBALS['lang'][$lang_key];
  }

  return $lang_key;

}
?>