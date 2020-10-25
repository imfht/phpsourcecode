<?php
/**
 * [MicroBuilder System] Copyright (c) 2014 MICROBUILDER.CN
 * MicroBuilder is NOT a free software, it under the license terms, visited http://www.microb.cn/ for more details.
 */
error_reporting(E_ALL ^ E_NOTICE);
@set_time_limit(0);
ob_start();
define('MB_ROOT', str_replace("\\",'/', dirname(__FILE__)));
if($_GET['res']) {
    $res = $_GET['res'];
    $reses = tpl_resources();
    if(array_key_exists($res, $reses)) {
        header('content-type:image/png');
        echo base64_decode($reses[$res]);
        exit();
    }
}
$actions = array('license', 'env', 'db', 'finish');
$action = $_COOKIE['action'];
$action = in_array($action, $actions) ? $action : 'license';
$ispost = strtolower($_SERVER['REQUEST_METHOD']) == 'post';

if(file_exists(MB_ROOT . '/source/Conf/install.lock') && $action != 'finish') {
    header('location: ./index.php');
    exit;
}
header('content-type: text/html; charset=utf-8');
if($action == 'license') {
    if($ispost) {
        setcookie('action', 'env');
        header('location: ?refresh');
        exit;
    }
    tpl_install_license();
}
if($action == 'env') {
    if($ispost) {
        setcookie('action', $_POST['do'] == 'continue' ? 'db' : 'license');
        header('location: ?refresh');
        exit;
    }
    $ret = array();
    $ret['server']['os']['value'] = php_uname();
    if(PHP_SHLIB_SUFFIX == 'dll') {
        $ret['server']['os']['remark'] = '建议使用 Linux 系统以提升程序性能';
        $ret['server']['os']['class'] = 'warning';
    }
    $ret['server']['sapi']['value'] = $_SERVER['SERVER_SOFTWARE'];
    if(PHP_SAPI == 'isapi') {
        $ret['server']['sapi']['remark'] = '建议使用 Apache 或 Nginx 以提升程序性能';
        $ret['server']['sapi']['class'] = 'warning';
    }
    $ret['server']['php']['value'] = PHP_VERSION;
    $ret['server']['dir']['value'] = MB_ROOT;
    if(function_exists('disk_free_space')) {
        $ret['server']['disk']['value'] = floor(disk_free_space(MB_ROOT) / (1024*1024)).'M';
    } else {
        $ret['server']['disk']['value'] = 'unknow';
    }
    $ret['server']['upload']['value'] = @ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'unknow';

    $ret['php']['version']['value'] = PHP_VERSION;
    $ret['php']['version']['class'] = 'success';
    if(version_compare(PHP_VERSION, '5.3.0') == -1) {
        $ret['php']['version']['class'] = 'danger';
        $ret['php']['version']['failed'] = true;
        $ret['php']['version']['remark'] = 'PHP版本必须为 5.3.0 以上. <a href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58062">详情</a>';
    }

    $ret['php']['pdo']['ok'] = extension_loaded('pdo') && extension_loaded('pdo_mysql');
    if($ret['php']['pdo']['ok']) {
        $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['pdo']['class'] = 'success';
    } else {
        $ret['php']['pdo']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['pdo']['class'] = 'danger';
        $ret['php']['pdo']['remark'] = '您的PHP环境不支持PDO, 系统无法正常运行. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58074">详情</a>';
        $ret['php']['pdo']['failed'] = true;
    }

    $ret['php']['curl']['ok'] = extension_loaded('curl') && function_exists('curl_init');
    if($ret['php']['curl']['ok']) {
        $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['curl']['class'] = 'success';
    } else {
        $ret['php']['curl']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['curl']['class'] = 'danger';
        $ret['php']['curl']['remark'] = '您的PHP环境不支持cURL, 系统无法正常运行. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58086">详情</a>';
        $ret['php']['curl']['failed'] = true;
    }

    $ret['php']['ssl']['ok'] = extension_loaded('openssl');
    if($ret['php']['ssl']['ok']) {
        $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['ssl']['class'] = 'success';
    } else {
        $ret['php']['ssl']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
        $ret['php']['ssl']['class'] = 'warning';
        $ret['php']['ssl']['remark'] = '没有启用OpenSSL, 将无法访问支付宝服务窗功能, 系统部分功能无法正常运行. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58109">详情</a>';
    }

    $ret['php']['gd']['ok'] = extension_loaded('gd');
    if($ret['php']['gd']['ok']) {
        $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['gd']['class'] = 'success';
    } else {
        $ret['php']['gd']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['gd']['class'] = 'danger';
        $ret['php']['gd']['failed'] = true;
        $ret['php']['gd']['remark'] = '没有启用GD, 将无法正常上传和压缩图片, 系统无法正常运行. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58110">详情</a>';
    }

    $ret['php']['dom']['ok'] = class_exists('DOMDocument');
    if($ret['php']['dom']['ok']) {
        $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['dom']['class'] = 'success';
    } else {
        $ret['php']['dom']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['php']['dom']['class'] = 'danger';
        $ret['php']['dom']['failed'] = true;
        $ret['php']['dom']['remark'] = '没有启用DOMDocument, 将无法正常安装使用模块, 和访问Api接口, 系统无法正常运行. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58111">详情</a>';
    }
    
    $ret['php']['apc']['ok'] = extension_loaded('apc') && function_exists('apc_bin_dumpfile');
    if($ret['php']['apc']['ok']) {
        $ret['php']['apc']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['php']['apc']['class'] = 'success';
    } else {
        $ret['php']['apc']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
        $ret['php']['apc']['class'] = 'warning';
        $ret['php']['apc']['remark'] = '系统没有启用APC扩展, 将无法使用加密发行的扩展. <a target="_blank" href="http://bbs.microb.cn/forum.php?mod=redirect&goto=findpost&ptid=3564&pid=58111">详情</a>';
    }

    $ret['write']['root']['ok'] = local_writeable(MB_ROOT . '/');
    if($ret['write']['root']['ok']) {
        $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['write']['root']['class'] = 'success';
    } else {
        $ret['write']['root']['value'] = '<span class="glyphicon glyphicon-remove text-warning"></span>';
        $ret['write']['root']['class'] = 'warning';
        $ret['write']['root']['remark'] = '本地目录无法写入, 将无法使用自动更新功能, 系统无法正常运行.  <a href="http://bbs.microb.cn/">详情</a>';
    }
    $ret['write']['conf']['ok'] = local_writeable(MB_ROOT . '/source/Data');
    if($ret['write']['conf']['ok']) {
        $ret['write']['conf']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['write']['conf']['class'] = 'success';
    } else {
        $ret['write']['conf']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['write']['conf']['class'] = 'danger';
        $ret['write']['conf']['failed'] = true;
        $ret['write']['conf']['remark'] = 'Conf目录无法写入, 将无法写入配置文件, 系统无法正常安装. ';
    }
    $ret['write']['data']['ok'] = local_writeable(MB_ROOT . '/source/Data');
    if($ret['write']['data']['ok']) {
        $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-ok text-success"></span>';
        $ret['write']['data']['class'] = 'success';
    } else {
        $ret['write']['data']['value'] = '<span class="glyphicon glyphicon-remove text-danger"></span>';
        $ret['write']['data']['class'] = 'danger';
        $ret['write']['data']['failed'] = true;
        $ret['write']['data']['remark'] = 'Data目录无法写入, 将无法写入系统运行时临时文件, 系统无法正常运行. ';
    }

    $ret['continue'] = true;
    foreach($ret['php'] as $opt) {
        if($opt['failed']) {
            $ret['continue'] = false;
            break;
        }
    }
    foreach($ret['write'] as $opt) {
        if($opt['failed']) {
            $ret['continue'] = false;
            break;
        }
    }
    if($ret['write']['failed']) {
        $ret['continue'] = false;
    }
    tpl_install_env($ret);
}
if($action == 'db') {
    if($ispost) {
        if($_POST['do'] != 'continue') {
            setcookie('action', 'env');
            header('location: ?refresh');
            exit();
        }
        if(!empty($_POST['db'])) {
            $db = $_POST['db'];
            $pieces = explode(':', $db['server'], 2);
            $db['server'] = $pieces[0];
            $db['port'] = empty($pieces[1]) ? '3306' : $pieces[1];
            $user = $_POST['user'];
            $dsn = "mysql:host={$db['server']};port={$db['port']};dbname={$db['name']}";
            $pdo = null;
            try {
                $pdo = new \PDO($dsn, $db['username'], $db['password']);
            } catch(PDOException $ex) {
                $error = $ex->getMessage();
                if(strpos($error, 'Access denied for user') !== false) {
                    $error = '您的数据库访问用户名或是密码错误. <br />';
                } elseif(strpos($error, 'Unknown database') !== false) {
                    $error = '您指定的数据库不存在, 请检查输入是否正确, 或先使用数据库管理工具创建数据库. <br />';
                }
            }
            if(empty($error) && !empty($pdo)) {
                $pdo->exec("SET character_set_connection=utf8, character_set_results=utf8, character_set_client=binary");
                $pdo->exec("SET sql_mode=''");
                if($pdo->errorCode() != '00000') {
                    $error = $pdo->errorInfo();
                    $error = $error[2];
                }
            }
            if(empty($error)) {
                $prefix = $db['prefix'];
                $prefix = str_replace('_', '\_', $prefix);
                $prefix = str_replace('%', '\%', $prefix);
                $query = $pdo->query("SHOW TABLES LIKE '{$prefix}%';");
                $tables = $query->fetchAll();
                if(!empty($tables)) {
                    $error = '您的数据库不为空，请重新建立数据库或是清空该数据库或更改表前缀！';
                }
            }
        } else {
            $error = '不能安装, 请检查您的浏览器支持Javascript';
        }
        if(empty($error)) {
            $config = local_config();
            $cookiepre = local_salt(4) . '_';
            $authkey = local_salt(8);
            $config = str_replace(array(
                '{db-server}', '{db-username}', '{db-password}', '{db-port}', '{db-name}', '{db-tablepre}', '{cfg-cookiepre}', '{cfg-authkey}'
            ), array(
                $db['server'], $db['username'], $db['password'], $db['port'], $db['name'], $db['prefix'], $cookiepre, $authkey
            ), $config);
            $dat = local_schema();
            $dat['schemas'] = unserialize($dat['schemas']);
            foreach($dat['schemas'] as $schema) {
                $sql = local_create_sql($schema);
                local_run($sql);
            }
            foreach($dat['datas'] as $data) {
                local_run($data);
            }

            $salt = local_salt(8);
            $password = sha1("{$user['password']}{$salt}{$authkey}");
            $pdo->exec("INSERT INTO `{$db['prefix']}usr_users` (`username`, `password`, `role`, `salt`, `status`) VALUES('{$user['username']}', '{$password}', '-1', '{$salt}', '0')");
            file_put_contents(MB_ROOT . '/source/Conf/config.inc.php', $config);
            touch(MB_ROOT . '/source/Conf/install.lock');
            setcookie('action', 'finish');
            header('location: ?refresh');
            exit();
        }
    }
    tpl_install_db($error);

}
if($action == 'finish') {
    setcookie('action', '', -10);
    tpl_install_finish();
}

function local_writeable($dir) {
    $writeable = 0;
    if(!is_dir($dir)) {
        @mkdir($dir, 0777);
    }
    if(is_dir($dir)) {
        if($fp = fopen("$dir/test.txt", 'w')) {
            fclose($fp);
            unlink("$dir/test.txt");
            $writeable = 1;
        } else {
            $writeable = 0;
        }
    }
    return $writeable;
}

function local_salt($length = 8) {
    $result = '';
    while(strlen($result) < $length) {
        $result .= sha1(uniqid('', true));
    }
    return substr($result, 0, $length);
}

function local_config() {
    $cfg = <<<'EOF'
<?php

$config = array();
$config['db']['default']['host']        = '{db-server}';
$config['db']['default']['username']    = '{db-username}';
$config['db']['default']['password']    = '{db-password}';
$config['db']['default']['port']        = '{db-port}';
$config['db']['default']['database']    = '{db-name}';
$config['db']['default']['charset']     = 'utf8';
$config['db']['default']['tablepre']    = '{db-tablepre}';

$config['common']['cookiepre']          = '{cfg-cookiepre}';
$config['common']['authkey']            = '{cfg-authkey}';

return $config;
EOF;
    return trim($cfg);
}

function local_schema() {
    $sql = array();
    $sql['schemas'] = '//{init-db-schemas}';
    $sql['datas'] = array();
//{$init-db-datas}    
    return $sql;
}

function local_mkdirs($path) {
    if(!is_dir($path)) {
        local_mkdirs(dirname($path));
        mkdir($path);
    }
    return is_dir($path);
}

function local_run($sql) {
    global $pdo, $db;
    if(!isset($sql) || empty($sql)) return;
    $stuff = 'mb_';
    $prefix = $db['prefix'];

    $sql = str_replace("\r", "\n", str_replace(' ' . $stuff, ' ' . $prefix, $sql));
    $sql = str_replace("\r", "\n", str_replace(' `' . $stuff, ' `' . $prefix, $sql));
    $ret = array();
    $num = 0;
    foreach(explode(";\n", trim($sql)) as $query) {
        $ret[$num] = '';
        $queries = explode("\n", trim($query));
        foreach($queries as $query) {
            $ret[$num] .= (isset($query[0]) && $query[0] == '#') || (isset($query[1]) && isset($query[1]) && $query[0].$query[1] == '--') ? '' : $query;
        }
        $num++;
    }
    unset($sql);
    foreach($ret as $query) {
        $query = trim($query);
        if(!empty($query)) {
            $pdo->exec($query);
        }
    }
}

function local_create_sql($schema) {
    $pieces = explode('_', $schema['charset']);
    $charset = $pieces[0];
    $engine = $schema['engine'];
    $sql = "CREATE TABLE IF NOT EXISTS `{$schema['tablename']}` (\n";
    foreach ($schema['fields'] as $value) {
        if(!empty($value['length'])) {
            $length = "({$value['length']})";
        } else {
            $length = '';
        }

        $signed  = empty($value['signed']) ? ' unsigned' : '';
        if(empty($value['null'])) {
            $null = ' NOT NULL';
        } else {
            $null = '';
        }
        if(isset($value['default'])) {
            $default = " DEFAULT '" . $value['default'] . "'";
        } else {
            $default = '';
        }
        if($value['increment']) {
            $increment = ' AUTO_INCREMENT';
        } else {
            $increment = '';
        }

        $sql .= "`{$value['name']}` {$value['type']}{$length}{$signed}{$null}{$default}{$increment},\n";
    }
    foreach ($schema['indexes'] as $value) {
        $fields = implode('`,`', $value['fields']);
        if($value['type'] == 'index') {
            $sql .= "KEY `{$value['name']}` (`{$fields}`),\n";
        }
        if($value['type'] == 'unique') {
            $sql .= "UNIQUE KEY `{$value['name']}` (`{$fields}`),\n";
        }
        if($value['type'] == 'primary') {
            $sql .= "PRIMARY KEY (`{$fields}`),\n";
        }
    }
    $sql = rtrim($sql);
    $sql = rtrim($sql, ',');

    $sql .= "\n) ENGINE=$engine DEFAULT CHARSET=$charset;\n\n";
    return $sql;
}

function tpl_frame() {
    global $action, $actions;
    $action = $_COOKIE['action'];
    $step = array_search($action, $actions);
    $steps = array();
    for($i = 0; $i <= $step; $i++) {
        if($i == $step) {
            $steps[$i] = ' list-group-item-info';
        } else {
            $steps[$i] = ' list-group-item-success';
        }
    }
    $progress = $step * 25 + 25;
    $content = ob_get_contents();
    ob_clean();
    $tpl = <<<EOF
<!DOCTYPE html>
<html lang="zh-cn">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>安装系统 - 微构 - 开源的手机App构建方案</title>
        <link rel="stylesheet" href="./w/static/css/bootstrap-united.min.css">
        <style>
            html,body{font-size:13px;font-family:"Microsoft YaHei UI", "微软雅黑", "宋体";}
            .pager li.previous a{margin-right:10px;}
            .header a:hover{color:#428bca;}
            .footer{padding:10px;}
            .footer a,.footer{color:#666;font-size:14px;line-height:25px;}
        </style>
        <!--[if lt IE 9]>
          <script src="http://cdn.bootcss.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="http://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>
    <body>
        <div class="container">
            <div class="header" style="margin:15px auto;">
                <ul class="nav nav-pills pull-right" role="tablist">
                    <li role="presentation" class="active"><a href="javascript:;">安装微构</a></li>
                    <li role="presentation"><a href="http://www.microb.cn">微构官网</a></li>
                    <li role="presentation"><a href="http://bbs.microb.cn">访问论坛</a></li>
                </ul>
                <img src="?res=logo" />
            </div>
            <div class="row well" style="margin:auto 0;">
                <div class="col-xs-3">
                    <div class="progress" title="安装进度">
                        <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" aria-valuenow="{$progress}" aria-valuemin="0" aria-valuemax="100" style="width: {$progress}%;">
                            {$progress}%
                        </div>
                    </div>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            安装步骤
                        </div>
                        <ul class="list-group">
                            <a href="javascript:;" class="list-group-item{$steps[0]}"><span class="glyphicon glyphicon-copyright-mark"></span> &nbsp; 许可协议</a>
                            <a href="javascript:;" class="list-group-item{$steps[1]}"><span class="glyphicon glyphicon-eye-open"></span> &nbsp; 环境监测</a>
                            <a href="javascript:;" class="list-group-item{$steps[2]}"><span class="glyphicon glyphicon-cog"></span> &nbsp; 参数配置</a>
                            <a href="javascript:;" class="list-group-item{$steps[3]}"><span class="glyphicon glyphicon-ok"></span> &nbsp; 成功</a>
                        </ul>
                    </div>
                </div>
                <div class="col-xs-9">
                    {$content}
                </div>
            </div>
            <div class="footer" style="margin:15px auto;">
                <div class="text-center">
                    <a href="http://www.microb.cn">关于微构</a> &nbsp; &nbsp; <a href="http://bbs.microb.cn">微构帮助</a> &nbsp; &nbsp; <a href="http://www.microb.cn">购买授权</a>
                </div>
                <div class="text-center">
                    Powered by <a href="http://www.microb.cn"><b>微构</b></a> v0.6 &copy; 2014 <a href="http://www.microb.cn">www.microb.cn</a>
                </div>
            </div>
        </div>
        <script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
        <script src="http://cdn.bootcss.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    </body>
</html>
EOF;
    echo trim($tpl);
}

function tpl_install_license() {
    echo <<<EOF
        <div class="panel panel-default">
            <div class="panel-heading">阅读许可协议</div>
            <div class="panel-body" style="overflow-y:scroll;max-height:400px;line-height:20px;">
                <h3>版权所有 (c)2014，微构官方保留所有权利。 </h3>
                <p>
                    感谢您选择微构 - 开源的手机App构建方案（以下简称MB，MB基于 PHP + MySQL的技术开发，全部源码开放。 <br />
                    为了使您正确并合法的使用本软件，请您在使用前务必阅读清楚下面的协议条款：
                </p>
                <p>
                    <strong>一、本授权协议适用且仅适用于微构系统(MB, MicroBuilder. 以下简称微构)任何版本，微构官方对本授权协议的最终解释权。</strong>
                </p>
                <p>
                    <strong>二、协议许可的权利 </strong>
                    <ol>
                        <li>您可以在完全遵守本最终用户授权协议的基础上，将本软件应用于非商业用途，而不必支付软件版权授权费用。</li>
                        <li>您可以在协议规定的约束和限制范围内修改微构源代码或界面风格以适应您的网站要求。</li>
                        <li>您拥有使用本软件构建的网站全部内容所有权，并独立承担与这些内容的相关法律义务。</li>
                        <li>获得商业授权之后，您可以将本软件应用于商业用途，同时依据所购买的授权类型中确定的技术支持内容，自购买时刻起，在技术支持期限内拥有通过指定的方式获得指定范围内的技术支持服务。商业授权用户享有反映和提出意见的权力，相关意见将被作为首要考虑，但没有一定被采纳的承诺或保证。</li>
                    </ol>
                </p>
                <p>
                    <strong>三、协议规定的约束和限制 </strong>
                    <ol>
                        <li>未获商业授权之前，不得将本软件用于商业用途（包括但不限于企业网站、经营性网站、以营利为目的或实现盈利的网站）。</li>
                        <li>未经官方许可，不得对本软件或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</li>
                        <li>未经官方许可，禁止在微构的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于重新分发。</li>
                        <li>如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。</li>
                    </ol>
                </p>
                <p>
                    <strong>四、有限担保和免责声明 </strong>
                    <ol>
                        <li>本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。</li>
                        <li>用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未购买产品技术服务之前，我们不承诺对免费用户提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任。</li>
                        <li>电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始确认本协议并安装  微构系统，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</li>
                        <li>如果本软件带有其它软件的整合API示范例子包，这些文件版权不属于本软件官方，并且这些文件是没经过授权发布的，请参考相关软件的使用许可合法的使用。</li>
                    </ol>
                </p>
            </div>
        </div>
        <form class="form-inline" role="form" method="post">
            <ul class="pager">
                <li class="pull-left" style="display:block;padding:5px 10px 5px 0;">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox"> 我已经阅读并同意此协议
                        </label>
                    </div>
                </li>
                <li class="previous"><a href="javascript:;" onclick="if(jQuery(':checkbox:checked').length == 1){jQuery('form')[0].submit();}else{alert('您必须同意软件许可协议才能安装！')};">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
            </ul>
        </form>
EOF;
    tpl_frame();
}

function tpl_install_env($ret = array()) {
    if(empty($ret['continue'])) {
        $continue = '<li class="previous disabled"><a href="javascript:;">请先解决环境问题后继续</a></li>';
    } else {
        $continue = '<li class="previous"><a href="javascript:;" onclick="$(\'#do\').val(\'continue\');$(\'form\')[0].submit();">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>';
    }
    echo <<<EOF
        <div class="panel panel-default">
            <div class="panel-heading">服务器信息</div>
            <table class="table table-striped">
                <tr>
                    <th style="width:150px;">参数</th>
                    <th>值</th>
                    <th></th>
                </tr>
                <tr class="{$ret['server']['os']['class']}">
                    <td>服务器操作系统</td>
                    <td>{$ret['server']['os']['value']}</td>
                    <td>{$ret['server']['os']['remark']}</td>
                </tr>
                <tr class="{$ret['server']['sapi']['class']}">
                    <td>Web服务器环境</td>
                    <td>{$ret['server']['sapi']['value']}</td>
                    <td>{$ret['server']['sapi']['remark']}</td>
                </tr>
                <tr class="{$ret['server']['php']['class']}">
                    <td>PHP版本</td>
                    <td>{$ret['server']['php']['value']}</td>
                    <td>{$ret['server']['php']['remark']}</td>
                </tr>
                <tr class="{$ret['server']['dir']['class']}">
                    <td>程序安装目录</td>
                    <td>{$ret['server']['dir']['value']}</td>
                    <td>{$ret['server']['dir']['remark']}</td>
                </tr>
                <tr class="{$ret['server']['disk']['class']}">
                    <td>磁盘空间</td>
                    <td>{$ret['server']['disk']['value']}</td>
                    <td>{$ret['server']['disk']['remark']}</td>
                </tr>
                <tr class="{$ret['server']['upload']['class']}">
                    <td>上传限制</td>
                    <td>{$ret['server']['upload']['value']}</td>
                    <td>{$ret['server']['upload']['remark']}</td>
                </tr>
            </table>
        </div>

        <div class="alert alert-info">PHP环境要求必须满足下列所有条件，否则系统或系统部份功能将无法使用。</div>
        <div class="panel panel-default">
            <div class="panel-heading">PHP环境要求</div>
            <table class="table table-striped">
                <tr>
                    <th style="width:150px;">选项</th>
                    <th style="width:180px;">要求</th>
                    <th style="width:50px;">状态</th>
                    <th>说明及帮助</th>
                </tr>
                <tr class="{$ret['php']['version']['class']}">
                    <td>PHP版本</td>
                    <td>5.3或者5.3以上</td>
                    <td>{$ret['php']['version']['value']}</td>
                    <td>{$ret['php']['version']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['pdo']['class']}">
                    <td>PDO_MYSQL</td>
                    <td>支持</td>
                    <td>{$ret['php']['pdo']['value']}</td>
                    <td>{$ret['php']['pdo']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['curl']['class']}">
                    <td>cURL</td>
                    <td>支持</td>
                    <td>{$ret['php']['curl']['value']}</td>
                    <td>{$ret['php']['curl']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['ssl']['class']}">
                    <td>OpenSSL</td>
                    <td>支持</td>
                    <td>{$ret['php']['ssl']['value']}</td>
                    <td>{$ret['php']['ssl']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['gd']['class']}">
                    <td>GD2</td>
                    <td>支持</td>
                    <td>{$ret['php']['gd']['value']}</td>
                    <td>{$ret['php']['gd']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['dom']['class']}">
                    <td>DOM</td>
                    <td>支持</td>
                    <td>{$ret['php']['dom']['value']}</td>
                    <td>{$ret['php']['dom']['remark']}</td>
                </tr>
                <tr class="{$ret['php']['apc']['class']}">
                    <td>APC加速器</td>
                    <td>推荐支持</td>
                    <td>{$ret['php']['apc']['value']}</td>
                    <td>{$ret['php']['apc']['remark']}</td>
                </tr>
            </table>
        </div>

        <div class="alert alert-info">系统要求微构整个安装目录必须可写, 才能使用微构所有功能。</div>
        <div class="panel panel-default">
            <div class="panel-heading">目录权限监测</div>
            <table class="table table-striped">
                <tr>
                    <th style="width:150px;">目录</th>
                    <th style="width:180px;">要求</th>
                    <th style="width:50px;">状态</th>
                    <th>说明及帮助</th>
                </tr>
                <tr class="{$ret['write']['root']['class']}">
                    <td>/</td>
                    <td>整目录可写</td>
                    <td>{$ret['write']['root']['value']}</td>
                    <td>{$ret['write']['root']['remark']}</td>
                </tr>
                <tr class="{$ret['write']['conf']['class']}">
                    <td>/source/Conf</td>
                    <td>Data目录可写</td>
                    <td>{$ret['write']['conf']['value']}</td>
                    <td>{$ret['write']['conf']['remark']}</td>
                </tr>
                <tr class="{$ret['write']['data']['class']}">
                    <td>/source/Data</td>
                    <td>Data目录可写</td>
                    <td>{$ret['write']['data']['value']}</td>
                    <td>{$ret['write']['data']['remark']}</td>
                </tr>
            </table>
        </div>
        <form class="form-inline" role="form" method="post">
            <input type="hidden" name="do" id="do" />
            <ul class="pager">
                <li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
                {$continue}
            </ul>
        </form>
EOF;
    tpl_frame();
}

function tpl_install_db($error = '') {
    if(!empty($error)) {
        $message = '<div class="alert alert-danger">发生错误: ' . $error . '</div>';
    }
    echo <<<EOF
    {$message}
    <form class="form-horizontal" method="post" role="form">
        <div class="panel panel-default">
            <div class="panel-heading">数据库选项</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">数据库主机</label>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="db[server]" value="localhost">
                            </div>
                        </div>
                        <div class="help-block">请输入的您数据库主机地址, 一般情况是 localhost 或 127.0.0.1</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">数据库用户</label>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="db[username]" value="root">
                            </div>
                        </div>
                        <div class="help-block">请输入的您数据库访问用户</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">数据库密码</label>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="db[password]">
                            </div>
                        </div>
                        <div class="help-block">请输入的您数据库访问密码</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">表前缀</label>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="db[prefix]" value="mb_">
                            </div>
                        </div>
                        <div class="help-block">如果您要在同一个数据库中安装多个程序, 可以使用表前缀来区分不同程序的数据表</div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">数据库名称</label>
                    <div class="col-sm-10">
                        <div class="row">
                            <div class="col-sm-5">
                                <input class="form-control" type="text" name="db[name]" value="microb">
                            </div>
                        </div>
                        <div class="help-block">请输入的您的数据库</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">管理选项</div>
            <div class="panel-body">
                <div class="form-group">
                    <label class="col-sm-2 control-label">管理员账号</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="username" name="user[username]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">管理员密码</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="password" name="user[password]">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">确认密码</label>
                    <div class="col-sm-4">
                        <input class="form-control" type="password"">
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="do" id="do" />
        <ul class="pager">
            <li class="previous"><a href="javascript:;" onclick="$('#do').val('back');$('form')[0].submit();"><span class="glyphicon glyphicon-chevron-left"></span> 返回</a></li>
            <li class="previous"><a href="javascript:;" onclick="if(check(this)){jQuery('#do').val('continue');$('form')[0].submit();}">继续 <span class="glyphicon glyphicon-chevron-right"></span></a></li>
        </ul>
    </form>
    <script>
        var lock = false;
        function check(obj) {
            if(lock) {
                return;
            }
            $('.form-control').parent().parent().removeClass('has-error');
            var error = false;
            $('.form-control').each(function(){
                if($(this).val() == '') {
                    $(this).parent().parent().addClass('has-error');
                    this.focus();
                    error = true;
                }
            });
            if(error) {
                alert('请检查未填项');
                return false;
            }
            if($(':password').eq(0).val() != $(':password').eq(1).val()) {
                $(':password').parent().parent().addClass('has-error');
                alert('确认密码不正确.');
                return false;
            }
            lock = true;
            $(obj).parent().addClass('disabled');
            $(obj).html('正在执行安装');
            return true;
        }
    </script>
EOF;
    tpl_frame();
}

function tpl_install_finish() {
    echo <<<EOF
    <div class="page-header"><h3>安装完成</h3></div>
    <div class="alert alert-success">
        恭喜您! 已成功安装“微构 - 开源的手机App构建方案”系统
    </div>
    <div class="form-horizontal">
        <div class="form-group">
            <div class="col-sm-3">
                <a target="_blank" class="btn btn-success btn-block" href="./w/index.php">访问网站首页</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-3">
                <a target="_blank" class="btn btn-success btn-block" href="./w/index.php/bench">访问内容管理中心</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-3">
                <a target="_blank" class="btn btn-success btn-block" href="./w/index.php/control">访问控制中心</a>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-3">
                <a target="_blank" class="btn btn-info btn-block" href="./m/index.php">访问手机页面(WebApp)</a>
            </div>
        </div>
    </div>
EOF;
    tpl_frame();
}

function tpl_resources() {
    static $res = array(
        'logo' => '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',
    );
    return $res;
}

