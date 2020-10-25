<?php
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('INSTALLPATH', dirname($_SERVER["SCRIPT_FILENAME"]) . '/');
define('BASEPATH', str_ireplace('install/', '', INSTALLPATH));
define('ENVIRONMENT', 'development');

include_once './install.conf.php';

$siteUrl = str_ireplace('install/', '', $_SERVER["HTTP_REFERER"]);

$step = isset($_GET['step']) ? $_GET['step'] : $_POST['step_number']+1;

switch ($step) {
    case 1:
        check_system();
        break;
    case 2:
        set_database();
        break;
    case 3:
        import_database();
        break;
    case 4:
        set_admin();
        break;
    case 5:
        finish_install();
        break;
    case "setconfig":
        set_config();
        break;
    case "setdatabase":
        test_database_connect();
        break;
    case "setadmin":
        setAdmin();
        break;
}


function check_system()
{
    global $siteUrl,$directories;


    $premit = true;

    echo '<div class="well">将下列目录/文件设置成可写。</div>';

    foreach ($directories as $dir) {
        $prem = check_write(BASEPATH . $dir);

        if ($prem) {
            $check = 'icon-ok';
        } else {
            $check  = 'icon-remove text-danger';
            $premit = false;
        }

        $str = '<div class="row"><div class="col-md-5">' . $dir . '</div>' . '<div class="col-md-3">..........................................</div>' . '<div class="col-md-4"><i class="' . $check . '"></i></div>' . '</div>';
        echo $str;
    }
    echo '<br />';
    if (!$premit) {
        echo '<button type="button" class="btn btn-warning pull-right" onclick="window.location.reload();">重新检测</button>';
    } else {
        echo <<<EOT
        <div class="row">
            <div class="form-group">
                <label class="col-sm-2 text-right">网站地址</label>
                <div class="col-sm-8">
                <input type="text" class="form-control" id="siteurl" value="{$siteUrl}">
                </div>
            </div>
        </div>
EOT;
    }
}

function check_write($directory)
{
    if (is_dir($directory) && $fp = @fopen($directory . '/test.txt', 'w+')) {
        fwrite($fp, 'test');
        fclose($fp);
        unlink($directory . '/test.txt');
        return true;
    } elseif (is_file($directory) && is_writable($directory)) {
        return true;
    }
    return false;
}

function set_config()
{
    $siteurl = $_POST['siteurl'];
    $config  = file_get_contents(BASEPATH . 'app/config/config.php');
    $config  = preg_replace("/config\['base_url'\]\s*=\s*'.*'/i", "config['base_url'] = '{$siteurl}'", $config);
    file_put_contents(BASEPATH . 'app/config/config.php', $config);
}

function set_database()
{
    echo <<<EOT
        <div class="well">数据库配置</div>

        <div class="input-group">
            <span class="input-group-addon">数据库地址</span>
            <input type="text" class="form-control" id="db_host" value="localhost">
        </div>
        <br/>

        <div class="input-group">
            <span class="input-group-addon">数据库名称</span>
            <input type="text" class="form-control" id="db_name" value="dmnovel">
        </div>
        <br/>

        <div class="input-group">
            <span class="input-group-addon">管理员名称</span>
            <input type="text" class="form-control" id="db_user" value="root">
        </div>
        <br/>

        <div class="input-group">
            <span class="input-group-addon">管理员密码</span>
            <input type="text" class="form-control" id="db_pass" VALUE="123456">
        </div>
        <br/>

        <div class="checkbox">
            <label>
                此操作将覆盖原表（如果表存在，将删除原表，重新建立)
            </label>
        </div>
    </form>
EOT;
}

function test_database_connect()
{
    $db_host = $_POST['db_host'];
    $db_name = $_POST['db_name'];
    $db_user = $_POST['db_user'];
    $db_pass = $_POST['db_pass'];
    //$db_cover = $_POST['db_cover'];

    $db = mysqli_connect($db_host, $db_user, $db_pass);

    if (!$db) {
        echo("不能连接到数据库，请检查用户名、密码是否输入错误！");
        exit();
    }

    $table = $db->select_db($db_name);
    if (!$table) {
        $db->query('CREATE DATABASE ' . $db_name);
    }

    $config = file_get_contents(BASEPATH . 'app/config/database.php');
    $config = preg_replace("/'hostname'\s*=>\s*'.*'/i", "'hostname'=>'{$db_host}'", $config);
    $config = preg_replace("/'username'\s*=>\s*'.*'/i", "'username'=>'{$db_user}'", $config);
    $config = preg_replace("/'password'\s*=>\s*'.*'/i", "'password'=>'{$db_pass}'", $config);
    $config = preg_replace("/'database'\s*=>\s*'.*'/i", "'database'=>'{$db_name}'", $config);
    file_put_contents(BASEPATH . 'app/config/database.php', $config);
}

function import_database()
{
    $db_cover = isset($_GET['db_cover'])?1:0;
    require_once BASEPATH . 'app/config/database.php';

    if (!$database = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'])) {
        echo "不能连接到数据库，请检查是否输入错误！";
        exit();
    }

    if (!$database->select_db($db['default']['database'])) {
        echo "数据库 {$db['default']['database']} 不存在！";
        exit();
    }

    $sqlstr = file_get_contents(BASEPATH . '/install/install.sql');

    $sqls = explode(';', $sqlstr);

    $database->set_charset('utf8');
    header("Content-Type:text/html;charset=utf8;");
    echo '<div class="well">正在导入数据库...</div>';

    ob_start();

    foreach ($sqls as $sql) {
        if (preg_match('/(CREATE|insert|DROP).*`(' . $db['default']['dbprefix'] . '\w+)`/i', $sql, $match)) {
            echo str_pad('', 4096) . "\n";
            switch (strtolower($match[1])) {
                case 'create':
                    echo '创建表：';
                    if ($db_cover == 1) {
                        $str = "DROP TABLE IF EXISTS {$match[2]} ;";
                        $database->query($str) or die($database->error);
                    }
                    break;
                case 'insert':
                    echo '插入表数据:';
                    break;
                case 'drop':
                    echo '删除表';
                    break;
            }
            echo $match[2] . '<br />';
            ob_flush();
            flush();
            $database->query($sql) or die($database->error);
        }
    }
    ob_end_clean();
}

function set_admin()
{
    echo<<<EOT
    <div class="well">设置管理员</div>
        <div class="input-group">
            <span class="input-group-addon">管理员名称</span>
            <input type="text" class="form-control" id="user_name" value="admin" required>
        </div>
        <br/>

        <div class="input-group">
            <span class="input-group-addon">管理员密码</span>
            <input type="password" class="form-control" id="password" VALUE="" required>
        </div>
        <br/>
        <div class="input-group">
            <span class="input-group-addon">重复密码</span>
            <input type="password" class="form-control" id="re_password" VALUE="" required>
        </div>

EOT;
}

function setAdmin()
{
    $username   = $_POST['user_name'];
    $password   = $_POST['password'];
    $repassword = $_POST['re_password'];

    if ($password != $repassword) {
        echo('两次输入的密码不相同。');
        exit();
    }
    require_once BASEPATH . 'app/config/database.php';
    $database =mysqli_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);
    if (!$database) {
        echo "不能连接到数据库，请检查是否输入错误！";
        exit();
    }
    if (!$database->select_db($db['default']['database'])) {
        echo "数据库 {$db['default']['database']} 不存在！";
        exit();
    }
    $password = md5($password);
    $sql="INSERT INTO `users` VALUES (0,'{$username}','{$password}',NULL,NULL,NULL,9,0);";

    $database->query($sql) ;
    if ($database->error) {
        echo $database->error;
    }
}

function finish_install()
{
    global $siteUrl;
    echo <<<EOT
    <div class="well">
        <h2>恭喜：）  已经完成安装。</h2>
        <h4>
        <ul>
            <li><a href="{$siteUrl}">首页</a></li>
            <li><a href="{$siteUrl}/admin">后台</li>
        </ul>
        </h4>
    </div>
EOT;
}
