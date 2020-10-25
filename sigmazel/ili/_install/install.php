<?php
// 加载文件

require_once '../source/boot.php';

header('Content-Type: text/html; charset=utf-8');

function pc_show_message($message, $url_forward = '')
{
    show_message($message, $url_forward, '/_install/view/show_message');
}

if($config['installed']) pc_show_message('系统已安装成功，无需重复安装！', '../');

$data['Version'] = $config['crypt'] == '_EC2014010300' ? 'Free' : 'Release';
$data['Application'] = $config['install'];

if (! is_file(ROOTPATH . "/_install/data.{$config[install]}.sql")) exit('系统安装数据库脚本不存在！');

if (empty($_var['ac'])){

    $extensions['curl'] = extension_loaded('curl');
    $extensions['gd'] = extension_loaded('gd');
    $extensions['mbstring'] = extension_loaded('mbstring');
    $extensions['mysql'] = extension_loaded('mysql');
    $extensions['pdo'] = extension_loaded('pdo_mysql');

    if ($_var['gp_formsubmit']) {
        if (!$extensions['gd']) pc_show_message('gd组件未安装！');
        if (!$extensions['mbstring']) pc_show_message('mbstring组件未安装！');
        if (!($extensions['mysql'] || $extensions['pdo'])) pc_show_message('mysql组件未安装！');
        
        if (empty($_var['gp_cbxAgree'])) pc_show_message('请先阅读协议并同意条款！');
        if (empty($_var['gp_txtHost'])) pc_show_message('域名不能为空！');
        if (empty($_var['gp_txtUser'])) pc_show_message('用户名不能为空！');
        if (empty($_var['gp_txtDatabase'])) pc_show_message('数据库不能为空！');

        if($extensions['mysql']){
            $link = @mysql_connect($_var['gp_txtHost'], $_var['gp_txtUser'], $_var['gp_txtPassword'], 1);
            if (!$link) pc_show_message('数据库连接失败，请仔细检查数据库参数！');

            $btn_select_db = @mysql_select_db($_var['gp_txtDatabase'], $link);
            if (!$btn_select_db) mysql_query("CREATE DATABASE {$_var[gp_txtDatabase]}", $link);
        }else{
            try{
                $link = new \PDO("mysql:host={$_var[gp_txtHost]};dbname=mysql", $_var['gp_txtUser'], $_var['gp_txtPassword']);
                $sth = $link->prepare("SHOW DATABASES");
                $sth->execute();
                $result = $sth->fetchAll();

                $fined = false;
                foreach($result as $key => $val){
                    $arr = array_values($val);
                    if(in_array($_var['gp_txtDatabase'], $arr)){
                        $fined = true;
                        break;
                    }

                    unset($arr);
                }

                if(!$fined) $link->exec("CREATE DATABASE {$_var[gp_txtDatabase]}");
            }catch(PDOException $exception){
                pc_show_message($exception->getMessage());
            }
        }

        $config_file_content = "";
        $config_file_content .= "<?php\r\n";
        $config_file_content .= "//版权所有(C) 2014 www.ilinei.com\r\n";
        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['database'][1]['dbhost'] = '{$_var[gp_txtHost]}';\r\n";
        $config_file_content .= "\$config['database'][1]['dbuser'] = '{$_var[gp_txtUser]}';\r\n";
        $config_file_content .= "\$config['database'][1]['dbpw'] = '{$_var[gp_txtPassword]}';\r\n";
        $config_file_content .= "\$config['database'][1]['dbcharset'] = 'utf8';\r\n";
        $config_file_content .= "\$config['database'][1]['pconnect'] = '0';\r\n";
        $config_file_content .= "\$config['database'][1]['dbname'] = '{$_var[gp_txtDatabase]}';\r\n";

        if(!$extensions['mysql']) $config_file_content .= "\$config['database']['driver'] = 'pdo';\r\n";

        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['cache']['type'] = 'file';\r\n";
        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['output']['charset'] = 'utf-8';\r\n";
        $config_file_content .= "\$config['output']['forceheader'] = '1';\r\n";
        $config_file_content .= "\$config['output']['gzip'] = '0';\r\n";
        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['cookie']['cookiepre'] = 'EC_';\r\n";
        $config_file_content .= "\$config['cookie']['cookiedomain'] = '';\r\n";
        $config_file_content .= "\$config['cookie']['cookiepath'] = '/';\r\n";
        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['qrcode'] = 'png'; //qrcode image type:png,jpg\r\n";
        $config_file_content .= "\$config['filter'] = ''; //site filter file path\r\n";
        $config_file_content .= "\$config['image'] = ''; //image server host\r\n";
        $config_file_content .= "\r\n";
        $config_file_content .= "\$config['crypt'] = '{$config[crypt]}';\r\n";
        $config_file_content .= "\$config['install'] = '{$config[install]}';\r\n";
        $config_file_content .= "\$config['redis'] = 0;\r\n";
        $config_file_content .= "?>";
        
        file_put_contents(ROOTPATH . '/source/config.php', $config_file_content);
        
        header('location:install.php?ac=step2');
        exit(0);
    }
    
    $page_title = '检查环境-安装';
    $nav_title = '第一步：检查环境';
    
    include_once view('/_install/view/install_step1');
} elseif ($_var['ac'] == 'step2') {
    $page_title = '执行数据库脚本-安装';
    $nav_title = '第二步：执行数据库脚本';
    
    include_once view('/_install/view/install_step2');
} elseif ($_var['ac'] == 'step3') {
    $dirs = scandir(ROOTPATH . '/tpl/');
    
    foreach ($dirs as $file) {
        if (is_dir(ROOTPATH . '/tpl/' . $file) && $file != '.' && $file != '..' && is_file(ROOTPATH . '/tpl/' . $file . '/_info.xml')) {
            $info_xml = (array) simplexml_load_file(ROOTPATH . '/tpl/' . $file . '/_info.xml');
            
            if (is_array($info_xml) && $info_xml['application'] == $config['install']) {
                $info_xml['rel'] = $file;
                $info_xml['theme'] = '/tpl/' . $file;
                $tpls[] = $info_xml;
            }
        }
    }
    
    if ($_var['gp_formsubmit']) {
        if (empty($_var['gp_tpl'])) pc_show_message('请选择模板！');
        if (strlen($_var['gp_txtPasswd']) < 6)  pc_show_message('请输入6位以上数字、字母、符号自由组合！');
        
        $fined = false;
        foreach ($tpls as $loop => $tpl) {
            if ($tpl['rel'] == $_var['gp_tpl']) {
                $fined = true;
                break;
            }
        }
        
        if (count($tpls) > 0 && ! $fined) pc_show_message('请选择模板！');
        
        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], '/_install/'));
        $path && $path = $path . '/';
	        
        $db->connect();
        
        $_setting = new \admin\model\_setting();
        
        $version = $_setting->get('Version');
        $version = strtolower($version['Version']);
        $version = str_replace(array('free', 'release'), '', $version);
        $version = ($config['crypt'] == '_EC2014010300' ? 'Free' : 'Release') . $version;

        $db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('AdminPassword', '".md5($_var['gp_txtPasswd'])."')");
        $db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('Version', '{$version}')");
        $db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('SiteHost', 'http://{$_SERVER[HTTP_HOST]}{$path}')");
        $db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('SiteTheme', '/tpl/{$_var[gp_tpl]}/')");
        $db->query("REPLACE INTO tbl_setting(SKEY, SVALUE) VALUES('CreateTime', '" . date('Y-m-d H:i:s') . "')");
        
        cache_write('setting', $_setting->format($_setting->get()));
        
        $db->update('tbl_user', array('PASSWD' => md5($_var['gp_txtPasswd'])), "USERNAME = 'admin'");
        
        $config_file_content = file_get_contents(ROOTPATH . "/source/config.php");
        $config_file_content = str_replace('$config[\'install\']', '$config[\'installed\']', $config_file_content);
        
        file_put_contents(ROOTPATH . '/source/config.php', $config_file_content);
        
        header('location:../admin.php');
        exit(0);
    }
    
    $page_title = '选择模板-安装';
    $nav_title = '第三步：选择模板';
    
    include_once view('/_install/view/install_step3');
} elseif ($_var['ac'] == 'data') {
    $data_contents = file_get_contents(ROOTPATH . "/_install/data.{$config[install]}.sql");
    
    $tmparr = explode(";\r", $data_contents);
    if (count($tmparr) < 10)
        $tmparr = explode(";\n", $data_contents);
    $data_contents = $tmparr;
    
    $sql_start = $sql_end = '';
    
    $start = $_var['gp_start'] + 0;
    $start < 0 && $start = 0;
    
    $sqls['CREATE'] = array();
    $sqls['INSERT'] = array();
    
    foreach ($data_contents as $key => $line) {
        $sql_prev = '';
        $sql_type = '';
        
        if (strexists($line, 'CREATE TABLE')) {
            $sql_prev = trim(str_replace('CREATE TABLE', '', substr($line, 0, strpos($line, '('))));
            if ($sql_prev) {
                $sqls['CREATE'][$sql_prev] = 1;
                $sql_type = 'CREATE';
            }
        } elseif (strexists($line, 'INSERT INTO')) {
            $sql_prev = trim(str_replace('INSERT INTO', '', substr($line, 0, strpos($line, 'VALUES'))));
            if ($sql_prev) {
                $sqls['INSERT'][$sql_prev] = 1;
                $sql_type = 'INSERT';
            }
        }
        
        if (count($sqls['CREATE']) + count($sqls['INSERT']) == $start + 1)
            $sql_start = $sql_type . ':' . $sql_prev;
        elseif (count($sqls['CREATE']) + count($sqls['INSERT']) == $start + 6) {
            $sql_end = $sql_type . ':' . $sql_prev;
            break;
        }
        
        unset($sql_prev);
        unset($sql_type);
    }
    
    $json['LIST'] = array();
    if (! $sql_start)
        exit_json($json);
    
    $db->connect();
    
    $quering = false;
    
    foreach ($data_contents as $key => $line) {
        $sql_prev = '';
        $sql_type = '';
        
        if (! $line)
            continue;
        
        if (strexists($line, 'CREATE TABLE')) {
            $sql_prev = trim(str_replace('CREATE TABLE', '', substr($line, 0, strpos($line, '('))));
            $sql_type = 'CREATE';
        } elseif (strexists($line, 'INSERT INTO')) {
            $sql_prev = trim(str_replace('INSERT INTO', '', substr($line, 0, strpos($line, 'VALUES'))));
            $sql_type = 'INSERT';
        }
        
        if (! $sql_prev)
            continue;
        if ($sql_type . ':' . $sql_prev == $sql_start)
            $quering = true;
        if (! $quering)
            continue;
        
        if ($sql_type . ':' . $sql_prev == $sql_end)
            break;
        
        if ($sql_type == 'CREATE')
            $db->query("DROP TABLE IF EXISTS {$sql_prev}");
        $db->query($sql_type == 'CREATE' ? str_replace("\r\n", '', $line) : $line);
        
        ! in_array($sql_type . ':' . $sql_prev, $json['LIST']) && $json['LIST'][$sql_type . ':' . $sql_prev] = 1;
        
        unset($sql_prev);
        unset($sql_type);
    }
    
    $json['LIST'] = array_keys($json['LIST']);
    
    exit_json($json);
}
?>
