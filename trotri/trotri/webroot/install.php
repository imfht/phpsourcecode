<?php
/**
 * Trotri
 *
 * @author    Huan Song <trotri@yeah.net>
 * @link      http://github.com/trotri/trotri for the canonical source repository
 * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 */
?>
<?php
define('SUCCESS_NUM',            0);
define('ERRNO_TBLPREFIX_WRONG',  1001);
define('ERRNO_DBLINK_WRONG',     1002);
define('ERRNO_DBNAME_WRONG',     1003);
define('ERRNO_MKCFGFILE_FAILED', 1004);
define('ERRNO_LOGINNAME_WRONG',  1005);
define('ERRNO_LOGINPWD_WRONG',   1006);
define('ERRNO_ADDUSER_FAILED',   1007);

define('REQUIRED_PHP_VERSION',   '5.3.0');
define('DB_CHARSET',             'utf8');
define('DB_TBLPREFIX_HOLDERS',   '#@__');
define('DB_IMG_HOLDERS',         '###baseurl###');

define('DS',                     DIRECTORY_SEPARATOR);
define('DIR_ROOT',               substr(dirname(__FILE__), 0, -8));
define('DIR_CFG_DB',             DIR_ROOT . DS . 'cfg'  . DS . 'db');
define('DIR_CFG_KEY',            DIR_ROOT . DS . 'cfg'  . DS . 'key');
define('DIR_LOG',                DIR_ROOT . DS . 'log');
define('DIR_DATA_INSTALL',       DIR_ROOT . DS . 'data' . DS . 'install');
define('DIR_DATA_RUNTIME',       DIR_ROOT . DS . 'data' . DS . 'runtime');
define('DIR_DATA_UPLOAD',        DIR_ROOT . DS . 'data' . DS . 'u');
define('PATH_CFG_DB',            DIR_CFG_DB       . DS . 'cluster.php');
define('PATH_CFG_KEY',           DIR_CFG_KEY      . DS . 'cluster.php');
define('PATH_DB_TABLES',         DIR_DATA_INSTALL . DS . 'db_tables.sql');
define('PATH_DB_DATA',           DIR_DATA_INSTALL . DS . 'db_data.sql');
define('PATH_DB_REGIONS',        DIR_DATA_INSTALL . DS . 'db_regions.sql');

$baseUrl = Util::getBaseUrl();
$do = isset($_GET['do']) ? trim($_GET['do']) : (isset($_POST['do']) ? trim($_POST['do']) : '');
?>
<?php
if ($do === 'source_db_tables') {
	$db = new Db();

	$commands = $db->getCreateTables();
	if ($commands && is_array($commands)) {
		foreach ($commands as $tableName => $command) {
			if ($db->query($command)) {
				echo '<p>创建表&nbsp;"' . $tableName . '"&nbsp;<span class="glyphicon glyphicon-ok"></span></p>';
			}
			else {
				echo '<p>创建表&nbsp;"' . $tableName . '"&nbsp;<span class="glyphicon glyphicon-remove"></span></p>';
				exit;
			}
		}

		echo '<p>创建表完成，正在导入数据，大约需要5分钟，请稍后 ...</p>';
	}
	else {
		echo '<p>创建表失败.</p>';
	}

	exit;
}

if ($do === 'source_db_data') {
	$db = new Db();

	$commands = $db->getInsertCommands();
	if ($commands && is_array($commands)) {
		foreach ($commands as $command) {
			if (!$db->query($command)) {
				echo '<p>导入数据&nbsp;&nbsp;' . $command . '<span class="glyphicon glyphicon-remove"></span></p>';
			}
		}

		echo '<p>导入数据完成.</p>';
	}
	else {
		echo '<p>导入数据失败.</p>';
	}

	exit;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Trotri-install</title>
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/static/plugins/bootstrap/3.0.3/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="<?php echo $baseUrl; ?>/static/plugins/bootstrap/3.0.3/css/bootstrap-theme.min.css" />
<style type="text/css">.container { width: 1170px; }</style>
<script type="text/javascript">
/**
 * Ajax
 * @param json p
 * p = {type: "GET"|"POST", url: "", data: "", dataType: "TEXT|JSON", async: true|false, success: function(ret) {}}
 * @return mixed
 */
function ajax(p) {
  if (typeof(p) != "object") {
    window.console.log("ajax args is wrong");
  }

  var xhrObj = false;
  try {
    xhrObj = new XMLHttpRequest(); // Firefox IE8和非IE内核
  }
  catch (e) {
    var progid = ["MSXML2.XMLHTTP.5.0", "MSXML2.XMLHTTP.4.0", "MSXML2.XMLHTTP.3.0", "MSXML2.XMLHTTP", "Microsoft.XMLHTTP"]; // IE5.5 IE6 IE7内核
    for (var i in progid) {
      try {
        xhrObj = new ActiveXObject(progid[i]);
      }
      catch (e) { continue; }
      break;
    }
  }

  if (p.type != undefined) { p.type = p.type.toUpperCase(); }
  if (p.type != "POST") {
    p.type = "GET";
    p.url.indexOf("?") == -1 ? p.url += "?" + p.data : "";
    p.data = null;
  }

  p.async != false ? p.async = true : "";
  xhrObj.open(p.type, p.url, p.async);
  if (p.type == "POST") {
    xhrObj.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  }
  xhrObj.send(p.data);

  if (p.dataType != undefined) { p.dataType = p.dataType.toUpperCase(); }
  p.dataType != "JSON" ? p.dataType = "TEXT" : "";

  xhrObj.onreadystatechange = function() {
    /**
     * readyState 值
     * 2 已经发送数据了，但是还没接收到反馈
     * 3 收到反馈了，反馈描述数据正在发送的过程中
     * 4 反馈描述数据已经被接收完毕
     */
    if (xhrObj.readyState == 4) {
      if (xhrObj.status == 200) {
        var data = xhrObj.responseText;
        if (p.dataType == "JSON") {
          eval("data = " + data + ";");
        }
        return p.success(data);
      }
    }
  }
}

/**
 * 展示导库日志
 * @param string id
 * @param string message
 * @return void
 */
function showMessage(id, message) {
  document.getElementById(id).innerHTML += message + "<br/>";
  document.body.scrollTop = 100000000;
}
</script>
</head>

<body>
<div class="container">
  <div class="jumbotron"><h1>欢迎使用Trotri!</h1></div>

<!-- 第一步：检查PHP版本、文件权限，介绍安装说明等 -->
<?php if ($do === '') : ?>
  <?php $hasError = false; ?>
  <div class="jumbotron">
  <p>
    PHP版本&nbsp;
    <?php if (version_compare(REQUIRED_PHP_VERSION, phpversion(), '<=')) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>必须是5.3或5.3以上版本！</small>
    <?php endif; ?>
  </p>
  <p>
    PDO支持&nbsp;
    <?php if (class_exists('PDO')) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法操作MySQL数据库！</small>
    <?php endif; ?>
  </p>
  <p>
    /cfg/db目录可写权限&nbsp;
    <?php if (is_writeable(DIR_CFG_DB)) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法写入DB配置文件！</small>
    <?php endif; ?>
  </p>
  <p>
    /cfg/key目录可写权限&nbsp;
    <?php if (is_writeable(DIR_CFG_KEY)) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法写入密钥配置文件！</small>
    <?php endif; ?>
  </p>
  <p>
    /log目录可写权限&nbsp;
    <?php if (is_writeable(DIR_LOG)) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法写入日志！</small>
    <?php endif; ?>
  </p>
  <p>
    /data/runtime目录可写权限&nbsp;
    <?php if (is_writeable(DIR_DATA_RUNTIME)) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法写入用户权限数据、表结构、生成的代码等！</small>
    <?php endif; ?>
  </p>
  <p>
    /data/u目录可写权限&nbsp;
    <?php if (is_writeable(DIR_DATA_UPLOAD)) : ?>
    <span class="glyphicon glyphicon-ok"></span>
    <?php else : ?>
    <?php $hasError = true; ?>
    <span class="glyphicon glyphicon-remove"></span>&nbsp;<small>无法写入用户上传图片、Flash等！</small>
    <?php endif; ?>
  </p>
  <p>安装需要一些数据库信息，用来导入SQL安装文件，这些数据库信息将被写入/cfg/db/cluster.php配置文件。</p>
  <p>1. 数据库名</p>
  <p>2. 数据库用户名</p>
  <p>3. 数据库密码</p>
  <p>4. 数据库主机</p>
  <p>5. 数据表前缀</p>
  <p>如果自动安装失败，手动安装过程：</p>
  <p>1、请手动将数据库信息写入 “根目录/cfg/db/cluster-sample.php” 文件，并将 “cluster-sample.php” 文件重命名为 “cluster.php”。</p>
  <p>2、请手动将 “根目录/data/install/db_tables.sql” 、 “根目录/data/install/db_data.sql” 和 “根目录/data/install/db_regions.sql” 中的#@__替换成表前缀，并依次将三个文件手动导入数据库。</p>
  <p>3、如果 “根目录/cfg/key/cluster.php” 文件不存在，请手动将密钥信息写入 “根目录/cfg/key/cluster-sample.php” 文件，并将 “cluster-sample.php” 文件重命名为 “cluster.php”。</p>
  <p>4、以上都完成后，再次执行此安装操作，这时会跳过数据库配置，直接转到创建管理员操作，输入管理员 “用户名” 和 “密码” 后提交即可。</p>
  <p></p>
  <?php if (!$hasError) : ?>
    <?php if (Util::mkKeyCfg() !== SUCCESS_NUM) : ?>
    <h2>创建密钥配置文件失败&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>请手动将密钥信息写入 “根目录/cfg/key/cluster-sample.php” 文件，并将 “cluster-sample.php” 文件重命名为 “cluster.php”。再刷新此页面，重新安装。</p>
    <?php else : ?>
    <p><a class="btn btn-primary btn-lg" href="install.php?do=dbform">继续 &gt;&gt;</a></p>
    <?php endif; ?>
  <?php endif; ?>
  </div>
<?php endif; ?>
<!-- /第一步 -->

<!-- 第二步：填写数据库信息表单 -->
<?php if ($do === 'dbform') : ?>
  <?php if (Util::hasDbCfg()) { header('location: install.php?do=adform'); } ?>
  <div class="row"><form class="form-horizontal" name="dbform" action="install.php?do=dbsubmit" method="post">
    <div class="form-group">
      <label class="col-lg-2 control-label">数据库主机</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="dbhost" value="localhost">
      </div>
      <span class="control-label">通常都是localhost.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">数据库用户名</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="dbuser" value="root">
      </div>
      <span class="control-label">连接MySQL用户名.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">数据库密码</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="dbpwd" value="">
      </div>
      <span class="control-label">连接MySQL密码.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">数据库名</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="dbname" value="">
      </div>
      <span class="control-label">请先手动创建数据库，系统不会自动创建数据库，数据库必须是utf8编码，如果需要用gbk编码，需要手动修改建表语句和配置文件.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">数据库表前缀</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="tblprefix" value="tr_">
      </div>
      <span class="control-label">表前缀是必填项，由英文字母开头，英文字母、数字或下划线组成.</span>
    </div>

    <div class="form-group">
      <div class="col-lg-1"></div>
      <div class="col-lg-11">
        <button class="btn btn-primary btn-lg" type="submit" name="_button_save_">继续 &gt;&gt;</button>
      </div>
    </div>
  </form></div>
<?php endif; ?>
<!-- /第二步 -->

<!-- 第三步：创建数据库配置文件，导入数据库 -->
<?php if ($do === 'dbsubmit') : ?>
  <?php if (Util::hasDbCfg()) { header('location: install.php?do=adform'); } ?>
  <?php
  $dbhost    = isset($_POST['dbhost'])    ? trim($_POST['dbhost']) : '';
  $dbuser    = isset($_POST['dbuser'])    ? trim($_POST['dbuser']) : '';
  $dbpwd     = isset($_POST['dbpwd'])     ? trim($_POST['dbpwd'])  : '';
  $dbname    = isset($_POST['dbname'])    ? trim($_POST['dbname']) : '';
  $tblprefix = isset($_POST['tblprefix']) ? strtolower(trim($_POST['tblprefix'])) : '';
  substr($tblprefix, -1) === '_' || $tblprefix .= '_';
  $errNo = Util::mkDbCfg($dbhost, $dbuser, $dbpwd, $dbname, $tblprefix);
  ?>
  <div class="jumbotron">
  <?php if ($errNo !== SUCCESS_NUM) : ?>
    <?php if ($errNo === ERRNO_TBLPREFIX_WRONG) : ?>
    <h2>数据库表前缀错误&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>1. 表前缀不能为空</p>
    <p>2. 表前缀只能由英文字母开头</p>
    <p>3. 表前缀只能由英文字母、数字或下划线组成</p>
    <?php elseif ($errNo === ERRNO_DBLINK_WRONG) : ?>
    <h2>数据库连接错误&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>1. 请确认用户名和密码是否正确？</p>
    <p>2. 请确认主机名是否正确？</p>
    <p>3. 请确认数据库服务器是否正常运行？</p>
    <?php elseif ($errNo === ERRNO_DBNAME_WRONG) : ?>
    <h2>无法选择数据库&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>1. 请确认数据库<?php echo $dbname; ?>是否存在？</p>
    <p>2. 请确认用户<?php echo $dbuser; ?>是否拥有使用<?php echo $dbname; ?>数据库的权限？</p>
    <?php elseif ($errNo === ERRNO_MKCFGFILE_FAILED) : ?>
    <h2>创建数据库配置文件失败&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <?php endif; ?>
    <p>&nbsp;</p>
    <p><a class="btn btn-primary btn-lg" href="javascript: history.back();">&lt;&lt; 返回上一步</a></p>
  <?php exit; endif; ?>
    <h2>正在导入数据库，大约需要8分钟，请稍后 ...</h2>
    <div id="dbsource_tables"></div>
    <div id="dbsource_data"></div>
    <p id="button_to_adform" style="display: none;"><a class="btn btn-primary btn-lg" href="install.php?do=adform">继续 &gt;&gt;</a></p>
    <script type="text/javascript">
    ajax({
      "type"     : "POST",
      "dataType" : "TEXT",
      "url"      : "install.php",
      "data"     : "do=source_db_tables",
      "success"  : function(data) {
        showMessage("dbsource_tables", data);
        ajax({
          "type"     : "POST",
          "dataType" : "TEXT",
          "url"      : "install.php",
          "data"     : "do=source_db_data",
          "success"  : function(data) {
            showMessage("dbsource_data", data);
            document.getElementById("button_to_adform").style.display = "block";
          }
        });
      }
    });
    </script>
  </div>
<?php endif; ?>
<!-- /第三步 -->

<!-- 第四步：填写管理员信息表单 -->
<?php if ($do === 'adform') : ?>
  <?php
  $db = new Db();
  if ($db->hasUser()) { header('location: install.php?do=done'); }
  ?>
  <div class="row"><form class="form-horizontal" name="adform" action="install.php?do=adsubmit" method="post">
    <div class="form-group">
      <label class="col-lg-2 control-label">管理员登录名</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="text" name="login_name" value="">
      </div>
      <span class="control-label">登录名由英文字母开头，6~18个英文字母、数字或下划线组成.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">管理员登录密码</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="password" name="login_pwd" value="">
      </div>
      <span class="control-label">6~20位字符，可使用字母、数字或符号的组合，不建议使用纯数字、纯字母或纯符号.</span>
    </div>

    <div class="form-group">
      <label class="col-lg-2 control-label">确认登录密码</label>
      <div class="col-lg-4">
        <input class="form-control input-sm" type="password" name="login_repwd" value="">
      </div>
      <span class="control-label">请再次输入密码.</span>
    </div>

    <div class="form-group">
      <div class="col-lg-1"></div>
      <div class="col-lg-11">
        <button class="btn btn-primary btn-lg" type="submit" name="_button_save_">继续 &gt;&gt;</button>
      </div>
    </div>
  </form></div>
<?php endif; ?>
<!-- /第四步 -->

<!-- 第五步：创建管理员 -->
<?php if ($do === 'adsubmit') : ?>
  <?php
  $db = new Db();
  if ($db->hasUser()) { header('location: install.php?do=done'); }
  ?>
  <?php
  $loginName  = isset($_POST['login_name'])  ? trim($_POST['login_name'])  : '';
  $loginPwd   = isset($_POST['login_pwd'])   ? trim($_POST['login_pwd'])   : '';
  $loginRepwd = isset($_POST['login_repwd']) ? trim($_POST['login_repwd']) : '';

  $errNo = $db->addUser($loginName, $loginPwd, $loginRepwd);
  ?>
  <div class="jumbotron">
  <?php if ($errNo !== SUCCESS_NUM) : ?>
    <?php if ($errNo === ERRNO_LOGINNAME_WRONG) : ?>
    <h2>管理中心登录名错误&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>1. 登录名不能为空</p>
    <p>2. 表前缀只能由英文字母开头</p>
    <p>2. 登录名只能由6~18个英文字母、数字或下划线组成</p>
    <?php elseif ($errNo === ERRNO_LOGINPWD_WRONG) : ?>
    <h2>管理中心登录密码错误&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <p>1. 登录密码不能为空</p>
    <p>2. 登录密码只能由6~20位字符组成</p>
    <p>3. 两次密码输入不一致</p>
    <?php elseif ($errNo === ERRNO_ADDUSER_FAILED) : ?>
    <h2>创建管理员失败&nbsp;<span class="glyphicon glyphicon-remove"></span></h2>
    <?php endif; ?>
    <p>&nbsp;</p>
    <p><a class="btn btn-primary btn-lg" href="javascript: history.back();">&lt;&lt; 返回上一步</a></p>
  <?php exit; endif; ?>

  <?php header('location: install.php?do=done'); ?>
  </div>
<?php endif; ?>
<!-- /第五步 -->

<!-- 第六步：安装完成 -->
<?php if ($do === 'done') : ?>
  <div class="jumbotron">
    <h2>安装完成</h2>
    <p><a href="administrator.php" target="_blank">进入管理中心</a></p>
    <p><a href="index.php" target="_blank">进入首页</a></p>
  </div>
<?php endif; ?>
<!-- /第六步 -->
</div>
</body>

<?php
/**
 * Db class file
 * 数据库操作类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Db.php 1 2014-11-05 01:08:06Z huan.song $
 * @since 1.0
 */
class Db
{
	/**
	 * @var string 数据库配置名
	 */
	const DB_CLUSTER = 'trotri';

	/**
	 * @var string 数据库主机
	 */
	public $dbhost;

	/**
	 * @var string 数据库用户名
	 */
	public $username;

	/**
	 * @var string 数据库密码
	 */
	public $password;

	/**
	 * @var string 数据库名
	 */
	public $dbname;

	/**
	 * @var string 数据库字符集编码
	 */
	public $charset;

	/**
	 * @var string 数据库表前缀
	 */
	public $tblprefix;

	/**
	 * @var resource 数据库链接信息
	 */
	public $dblink;

	/**
	 * 构造方法：从配置中获取数据库信息
	 * @return void
	 */
	public function __construct()
	{
		if (!is_file(PATH_CFG_DB)) {
			$this->halt('Install Error: Db Cfg Not Exists!');
		}

		$cluster = require_once PATH_CFG_DB;
		if (!$cluster || !is_array($cluster) || !isset($cluster[self::DB_CLUSTER]) || !is_array($cluster[self::DB_CLUSTER])) {
			$this->halt('Install Error: Db Cfg Wrong!');
		}

		$cluster = $cluster[self::DB_CLUSTER];

		$dsn       = isset($cluster['dsn'])       ? $cluster['dsn']       : '';
		$username  = isset($cluster['username'])  ? $cluster['username']  : '';
		$password  = isset($cluster['password'])  ? $cluster['password']  : '';
		$charset   = isset($cluster['charset'])   ? $cluster['charset']   : '';
		$tblprefix = isset($cluster['tblprefix']) ? $cluster['tblprefix'] : '';

		list($dbhost, $dbname) = explode(';', $dsn);
		$dbhost = trim(substr($dbhost, strlen('mysql:host=')));
		$dbname = trim(substr($dbname, strlen('dbname=')));

		if ($dbhost === '' || $dbname === '' || $username === '' || $charset === '' || $tblprefix === '') {
			$this->halt('Install Error: Db Cfg Wrong!');
		}

		$this->dbhost    = $dbhost;
		$this->dbname    = $dbname;
		$this->username  = $username;
		$this->password  = $password;
		$this->charset   = $charset;
		$this->tblprefix = $tblprefix;

		// 如果把此类移到类库当着共用类，不建议在构造方法中连接数据库。
		$this->connect();
	}

	/**
	 * 析构方法：关闭数据库连接
	 * @return void
	 */
	public function __destruct()
	{
		$this->close();
	}

	/**
	 * 连接数据库操作
	 * @return void
	 */
	public function connect()
	{
		$this->dblink = mysql_connect($this->dbhost, $this->username, $this->password);
		if (!$this->dblink) {
			$this->halt('Install Error: Can not connect to MySQL server!');
		}

		$version = mysql_get_server_info($this->dblink);
		if ($version > '4.1') {
			$command = 'SET character_set_connection=' . $this->charset . ', character_set_results=' . $this->charset . ', character_set_client=binary';
			if ($version > '5.0.1') {
				$command .= ", sql_mode=''";
			}

			$this->query($command);
		}

		if (!mysql_select_db($this->dbname, $this->dblink)) {
			$this->halt('Install Error: Db Name Not Exists!');
		}
	}

	/**
	 * 执行SQL语句操作
	 * @param string $command
	 * @return resource
	 */
	public function query($command)
	{
		return mysql_query($command, $this->dblink);
	}

	/**
	 * 关闭数据库连接
	 * @return boolean
	 */
	public function close()
	{
		if ($this->dblink) {
			return mysql_close($this->dblink);
		}

		return true;
	}

	/**
	 * 创建管理员账号
	 * @param string $loginName
	 * @param string $loginPwd
	 * @param string $loginRepwd
	 * @return integer
	 */
	public function addUser($loginName, $loginPwd, $loginRepwd)
	{
		if ($this->hasUser()) {
			return SUCCESS_NUM;
		}

		if (!Util::checkLoginName($loginName)) {
			return ERRNO_LOGINNAME_WRONG;
		}

		if (!Util::checkLoginPwd($loginPwd, $loginRepwd)) {
			return ERRNO_LOGINPWD_WRONG;
		}

		$salt = Util::randChars();
		$password = md5($salt . substr(md5($loginPwd), 3));
		$nowTime = date('Y-m-d H:i:s');
		$command = "INSERT INTO {$this->tblprefix}users VALUES ('1', '{$loginName}', 'name', '{$password}', '{$salt}', '{$loginName}', '', '', '{$nowTime}', '{$nowTime}', '0000-00-00 00:00:00', '0', '0', '0', '0', '0', 'n', 'n', 'n', 'n')";
		if (!$this->query($command)) {
			return ERRNO_ADDUSER_FAILED;
		}

		return SUCCESS_NUM;
	}

	/**
	 * 是否已经创建管理员账号
	 * @return boolean
	 */
	public function hasUser()
	{
		$command = "SELECT user_id, login_name FROM {$this->tblprefix}users WHERE user_id = '1'";
		$query = $this->query($command);
		$row = mysql_fetch_assoc($query);
		if ($row && is_array($row)) {
			return true;
		}

		return false;
	}

	/**
	 * 获取Insert语句
	 * @return array
	 */
	public function getInsertCommands()
	{
		global $baseUrl;

		$data = array();

		$fileName = PATH_DB_DATA;
		$commands = file($fileName);
		foreach ($commands as $command) {
			if (($command = trim($command)) === '') {
				continue;
			}

			$command = str_replace(array(DB_TBLPREFIX_HOLDERS, DB_IMG_HOLDERS), array($this->tblprefix, $baseUrl), $command);
			$data[] = $command;
		}

		$fileName = PATH_DB_REGIONS;
		$commands = file($fileName);
		foreach ($commands as $command) {
			if (($command = trim($command)) === '') {
				continue;
			}

			$command = str_replace(DB_TBLPREFIX_HOLDERS, $this->tblprefix, $command);
			$data[] = $command;
		}

		return $data;
	}

	/**
	 * 获取建表语句
	 * @return array
	 */
	public function getCreateTables()
	{
		$data = array();

		$fileName = PATH_DB_TABLES;
		$commands = explode(';', file_get_contents($fileName));
		foreach ($commands as $command) {
			if (($command = trim($command)) === '') {
				continue;
			}

			$command = str_replace(DB_TBLPREFIX_HOLDERS, $this->tblprefix, $command);
			$tableName = preg_replace('/CREATE TABLE `(\w+)` \(.*/is', '${1}', $command, 1);
			$data[$tableName] = $command . ';';
		}

		return $data;
	}

	/**
	 * 出错处理
	 * @param string $message
	 * @return void
	 */
	public function halt($message = '')
	{
		echo $message;
		exit;
	}

}

/**
 * Util class file
 * 工具集合类
 * @author 宋欢 <trotri@yeah.net>
 * @version $Id: Db.php 1 2014-11-05 01:08:06Z huan.song $
 * @since 1.0
 */
class Util
{
	/**
	 * 创建数据库配置文件
	 * @return integer
	 */
	public static function mkKeyCfg()
	{
		if (self::hasKeyCfg()) {
			return SUCCESS_NUM;
		}

		$data = array (
			'auth_administrator' => array (
				'crypt' => self::randChars(32),
				'sign' => self::randChars(32),
				'expiry' => 2592000,
				'rnd_len' => 16
			),
			'auth_site' => array (
				'crypt' => self::randChars(32),
				'sign' => self::randChars(32),
				'expiry' => 2592000,
				'rnd_len' => 16,
			),
			'cookie' => array (
				'crypt' => self::randChars(16),
				'sign' => self::randChars(16),
				'expiry' => 86400,
				'rnd_len' => 8
			),
			'repwd' => array (
				'crypt' => self::randChars(16),
				'sign' => self::randChars(16),
				'expiry' => 86400,
				'rnd_len' => 8
			)
		);

		$content  = "<?php\n";
		$content .= "/**\n";
		$content .= " * Trotri\n";
		$content .= " *\n";
		$content .= " * @author    Huan Song <trotri@yeah.net>\n";
		$content .= " * @link      http://github.com/trotri/trotri for the canonical source repository\n";
		$content .= " * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.\n";
		$content .= " * @license   http://www.apache.org/licenses/LICENSE-2.0\n";
		$content .= " */\n\n";
		$content .= "return " . var_export($data, true) . ";\n";

		file_put_contents(PATH_CFG_KEY, $content);
		if (self::hasKeyCfg()) {
			return SUCCESS_NUM;
		}

		return ERRNO_MKCFGFILE_FAILED;
	}

	/**
	 * 是否已经创建密钥配置文件
	 * @return boolean
	 */
	public static function hasKeyCfg()
	{
		return is_file(PATH_CFG_KEY) ? true : false;
	}

	/**
	 * 创建数据库配置文件
	 * @param string $dbhost
	 * @param string $dbuser
	 * @param string $dbpwd
	 * @param string $dbname
	 * @param string $tblprefix
	 * @return integer
	 */
	public static function mkDbCfg($dbhost, $dbuser, $dbpwd, $dbname, $tblprefix)
	{
		if (self::hasDbCfg()) {
			return SUCCESS_NUM;
		}

		$dbhost    = trim($dbhost);
		$dbuser    = trim($dbuser);
		$dbpwd     = trim($dbpwd);
		$dbname    = trim($dbname);
		$tblprefix = strtolower(trim($tblprefix));
		substr($tblprefix, -1) === '_' || $tblprefix .= '_';

		if (!self::checkTblprefix($tblprefix)) {
			return ERRNO_TBLPREFIX_WRONG;
		}

		if ($dbhost === '' || $dbuser === '') {
			return ERRNO_DBLINK_WRONG;
		}

		if ($dbname === '') {
			return ERRNO_DBNAME_WRONG;
		}

		$link = mysql_connect($dbhost, $dbuser, $dbpwd);
		if (!$link) {
			return ERRNO_DBLINK_WRONG;
		}

		if (!mysql_select_db($dbname, $link)) {
			mysql_close($link);
			return ERRNO_DBNAME_WRONG;
		}

		mysql_close($link);

		$data = array (
			'trotri' => array (
				'dsn' => 'mysql:host=' . $dbhost . ';dbname=' . $dbname,
				'username' => $dbuser,
				'password' => $dbpwd,
				'charset' => 'utf8',
				'retry' => 3,
				'tblprefix' => $tblprefix
			),
		);

		$content  = "<?php\n";
		$content .= "/**\n";
		$content .= " * Trotri\n";
		$content .= " *\n";
		$content .= " * @author    Huan Song <trotri@yeah.net>\n";
		$content .= " * @link      http://github.com/trotri/trotri for the canonical source repository\n";
		$content .= " * @copyright Copyright &copy; 2011-2013 http://www.trotri.com/ All rights reserved.\n";
		$content .= " * @license   http://www.apache.org/licenses/LICENSE-2.0\n";
		$content .= " */\n\n";
		$content .= "return " . var_export($data, true) . ";\n";

		file_put_contents(PATH_CFG_DB, $content);
		if (self::hasDbCfg()) {
			return SUCCESS_NUM;
		}

		return ERRNO_MKCFGFILE_FAILED;
	}

	/**
	 * 是否已经创建数据库配置文件
	 * @return boolean
	 */
	public static function hasDbCfg()
	{
		return is_file(PATH_CFG_DB) ? true : false;
	}

	/**
	 * 验证“数据库表前缀”是否符合规范
	 * @param string $tblprefix
	 * @return boolean
	 */
	public static function checkTblprefix($tblprefix)
	{
		return preg_match('/^[a-z]+\w+$/i', $tblprefix) ? true : false;
	}

	/**
	 * 验证“登录名”是否符合规范
	 * @param string $loginName
	 * @return boolean
	 */
	public static function checkLoginName($loginName)
	{
		if (strlen($loginName) < 6 || strlen($loginName) > 18) {
			return false;
		}

		return preg_match('/^[a-z]+\w+$/i', $loginName) ? true : false;
	}

	/**
	 * 验证“登录密码”是否符合规范
	 * @param string $loginPwd
	 * @param string $loginRepwd
	 * @return boolean
	 */
	public static function checkLoginPwd($loginPwd, $loginRepwd)
	{
		if (strlen($loginPwd) < 6 || strlen($loginPwd) > 20) {
			return false;
		}

		if ($loginPwd !== $loginRepwd) {
			return false;
		}

		return true;
	}

	/**
	 * 获取随机字符串
	 * @param integer $length
	 * @param string $format
	 * @return string
	 */
	public static function randChars($length = 6, $format = 'ALL')
	{
		static $chars = 'ABCDEFGHIJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789';
		$start = $format == 'NUMBER' ? 49 : 0;
		$end   = $format == 'LATTER' ? 48 : 56;
		$rand  = '';
		for ($i = 0; $i < $length; $i++) {
			$pos = mt_rand($start, $end);
			$rand .= $chars{$pos};
		}

		return $rand;
	}

	/**
	 * 获取当前应用的路径
	 * @return string
	 */
	public static function getBaseUrl()
	{
		$scriptName = isset($_SERVER['SCRIPT_FILENAME']) ? basename($_SERVER['SCRIPT_FILENAME']) : '';
		if (isset($_SERVER['SCRIPT_NAME']) && (basename($_SERVER['SCRIPT_NAME']) === $scriptName)) {
			$scriptUrl = $_SERVER['SCRIPT_NAME'];
		}
		elseif (isset($_SERVER['PHP_SELF']) && (basename($_SERVER['PHP_SELF']) === $scriptName)) {
			$scriptUrl = $_SERVER['PHP_SELF'];
		}
		elseif (isset($_SERVER['ORIG_SCRIPT_NAME']) && (basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)) {
			$scriptUrl = $_SERVER['ORIG_SCRIPT_NAME'];
		}
		elseif (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false) {
			$scriptUrl = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
		}
		elseif (isset($_SERVER['DOCUMENT_ROOT']) && (strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)) {
			$scriptUrl = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
		}
		else {
			$scriptUrl = '';
		}

		$baseUrl = rtrim(dirname($scriptUrl), '\\/');
		return $baseUrl;
	}
}
?>