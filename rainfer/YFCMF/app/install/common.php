<?php
// +----------------------------------------------------------------------
// | YFCMF [ WE CAN DO IT MORE SIMPLE]
// +----------------------------------------------------------------------
// | Copyright (c) 2016-2018 http://yfcmf.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: rainfer <rainfer520@qq.com>
// +----------------------------------------------------------------------

use think\facade\Env;

/**
 * 测试读写
 *
 * @param string $d
 *
 * @return bool
 */
function testwrite($d)
{
    $tfile = "_test.txt";
    $fp    = @fopen($d . "/" . $tfile, "w");
    if (!$fp) {
        return false;
    }
    fclose($fp);
    $rs = @unlink($d . "/" . $tfile);
    if ($rs) {
        return true;
    }
    return false;
}

/**
 * 建立文件夹
 *
 * @param string $path
 *
 * @return bool
 */
function create_dir($path)
{
    if (is_dir($path)) {
        return true;
    }
    $path    = dir_path($path);
    $temp    = explode('/', $path);
    $cur_dir = '';
    $max     = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir)) {
            continue;
        }
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}

/**
 * 返回路径
 *
 * @param string $path
 *
 * @return string
 */
function dir_path($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') {
        $path = $path . '/';
    }
    return $path;
}

/**
 * 执行sql文件
 *
 * @param \think\db\ $db
 * @param string     $file
 * @param string     $tablepre
 *
 * @return mixed
 */
function execute_sql($db, $file, $tablepre)
{
    //读取SQL文件
    $sql = file_get_contents(Env::get('app_path') . request()->module() . '/data/' . $file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    //替换表前缀
    $default_tablepre = "yf_";
    $sql              = str_replace(" `{$default_tablepre}", " `{$tablepre}", $sql);
    //开始安装
    showmsg('开始安装数据库...');
    foreach ($sql as $item) {
        $item = trim($item);
        if (empty($item)) {
            continue;
        }
        preg_match('/CREATE TABLE `([^ ]*)`/', $item, $matches);
        if ($matches) {
            $table_name = $matches[1];
            $msg        = "创建数据表{$table_name}";
            if (false !== $db->execute($item)) {
                showmsg($msg . ' 完成');
            } else {
                session('error', true);
                showmsg($msg . ' 失败！', 'error');
            }
        } else {
            $db->execute($item);
        }
    }
}

/**
 * 实时显示提示信息
 *
 * @param  string $msg   提示信息
 * @param  string $class 输出样式（success:成功，error:失败）
 */
function showmsg($msg, $class = '')
{
    echo "<script type=\"text/javascript\">showmsg(\"{$msg}\", \"{$class}\")</script>";
    flush();
    ob_flush();
}

/**
 * 创建管理员
 *
 * @param \think\db\ $db
 * @param  string    $table_prefix
 */
function create_admin_account($db, $table_prefix)
{
    $username       = input("manager");
    $admin_pwd_salt = random(10);
    $password       = encrypt_password(input("manager_pwd"), $admin_pwd_salt);
    $email          = input("manager_email");
    $create_date    = time();
    $ip             = request()->ip();
    $sql            = <<<hello
    INSERT INTO `{$table_prefix}admin` 
    (`id`, `username`, `password`, `pwd_salt`, `changepwd`, `email`, `realname`, `avatar`, `logtimes`, `last_ip`, `last_time`, `create_time`, `uid`) VALUES
    ('1', '{$username}', '{$password}', '{$admin_pwd_salt}', '{$create_date}', '{$email}', '', '', '1', '{$ip}', {$create_date}, {$create_date}, '1');
hello;
    $db->execute($sql);
    $sql = <<<hello
    INSERT INTO `{$table_prefix}user` 
    (`id`, `username`, `password`, `pwd_salt`, `nickname`, `province`, `city`, `town`, `sex`, `avatar`, `mobile`, `email`, `open`, `create_time`, `update_time`, `user_from`, `user_url`, `birthday`, `signature`, `last_ip`, `last_time`, `activation_key`, `status`) VALUES
('1', '{$username}', '{$password}', '{$admin_pwd_salt}', '{$username}', '0', '0', '0', '3', '', '', '{$email}', '1', {$create_date}, {$create_date}, '', '', '0', '', '{$ip}', {$create_date}, '', '1');
hello;
    $db->execute($sql);
    showmsg("管理员账号创建成功!");
}


/**
 * 写入配置
 *
 * @param array $config
 *
 * @return mixed
 */
function create_config($config)
{
    if (is_array($config)) {
        //读取配置内容
        $conf = file_get_contents(Env::get('app_path') . request()->module() . '/data/database.php');
        //替换配置项
        foreach ($config as $key => $value) {
            $conf = str_replace("#{$key}#", $value, $conf);
        }
        //写入应用配置文件
        if (file_put_contents(Env::get('config_path') . 'database.php', $conf)) {
            showmsg('配置文件写入成功');
        } else {
            session('error', true);
            showmsg('配置文件写入失败！', 'error');
        }
        return '';
    }
}
