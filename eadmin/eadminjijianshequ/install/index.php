<?php
/**
 * 安装向导
 */
header('Content-type:text/html;charset=utf-8');

// 检测是否安装过
if (file_exists('./install.lock')) {
    echo '你已经安装过该系统，请删除./install/文件';
    die;
}
// 同意协议页面
if (@!isset($_GET['c']) || @$_GET['c'] == 'agreement') {
    require './agreement.html';
}
// 检测环境页面
if (@$_GET['c'] == 'test') {
    require './test.html';
}
// 创建数据库页面
if (@$_GET['c'] == 'create') {
    require './create.html';
}
// 安装成功页面
if (@$_GET['c'] == 'success') {
    // 判断是否为post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = $_POST;


        $link = @new mysqli($data['DB_HOST'], $data['DB_USER'], $data['DB_PWD']);

        // 获取错误信息
        $error = $link->connect_error;
        if (!is_null($error)) {
            // 转义防止和alert中的引号冲突
            $error = addslashes($error);
            die("<script>alert('数据库链接失败:$error');history.go(-1)</script>");
        }
        // 设置字符集
        $link->query("SET NAMES 'utf8'");
        $link->server_info > 5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
        // 创建数据库并选中
        if (!$link->select_db($data['DB_NAME'])) {
            $create_sql = 'CREATE DATABASE IF NOT EXISTS ' . $data['DB_NAME'] . ' DEFAULT CHARACTER SET utf8;';
            $link->query($create_sql) or die('创建数据库失败');
            $link->select_db($data['DB_NAME']);
        }
        // 导入sql数据并创建表
        $shujuku_str = file_get_contents('./ESPHP.sql');
        $sql_array   = preg_split("/;[\r\n]+/", str_replace('es_', $data['DB_PREFIX'], $shujuku_str));
        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $link->query($v);
            }
        }
        $link->close();


        $chars    = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password = '';
        for ($i = 0; $i < 5; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用 substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组 $chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
            $password .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        if (!file_exists('../data/config/')) {
            mkdir('../data/config/');
        }

        $db_str = <<<php
<?php

return [
    	'type' => 'mysql', //数据库类型
		'host' => '{$data['DB_HOST']}', //主机地址
		'dbname' => '{$data['DB_NAME']}', //数据库名称
		'port' => '{$data['DB_PORT']}', //连接端口
		'username' => '{$data['DB_USER']}', //用户名
		'password' => '{$data['DB_PWD']}', //登录密码
		'prefix' =>'{$data['DB_PREFIX']}', //数据表前缀
        'charset'         => 'utf8',
];
php;
        // 创建数据库链接配置文件

        $fp = fopen('../data/config/database.php', "w");

        fputs($fp, $db_str);

        fclose($fp);


        $db_str = <<<php
<?php
        
return [
  
        		'id'             => '',
        		// SESSION_ID的提交变量,解决flash上传跨域
        		'var_session_id' => '',
        		// SESSION 前缀
        		'prefix'         => '{$password}',
        		// 驱动方式 支持redis memcache memcached
        		'type'           => '',
        		// 是否自动开启 SESSION
        		'auto_start'     => true,
        		'secure'         => false,
                'maxLifeTime' => 60*24*7, //生存期(分钟)
			    'savePath' => '', //保存路径，不设置则为默认
        				
];
php;
        $fp     = fopen('../data/config/session.php', "w");

        fputs($fp, $db_str);

        fclose($fp);

        $db_str = <<<php
<?php
        
return [
        

        // 驱动方式
        'type'   => 'File',
        // 缓存保存目录
        'path'   => CACHE_PATH,
        // 缓存前缀
        'prefix' =>'{$password}',
        // 缓存有效期 0表示永久缓存
        'expire' => 0,

];
php;

        $fp = fopen('../data/config/cache.php', "w");

        fputs($fp, $db_str);

        fclose($fp);
        $db_str = <<<php
<?php
        
return [
        
        // cookie 名称前缀
        'prefix'    =>'{$password}',
        // cookie 保存时间
        'expire'    => 0,
        // cookie 保存路径
        'path'      => '/',
        // cookie 有效域名
        'domain'    => '',
        //  cookie 启用安全传输
        'secure'    => false,
        // httponly设置
        'httponly'  => '',
        // 是否使用 setcookie
        'setcookie' => true,
];
php;

        $fp = fopen('../data/config/cookie.php', "w");

        fputs($fp, $db_str);

        fclose($fp);


        //  file_put_contents('../application/database.php', $db_str);
        @touch('./install.lock');
        require './success.html';
    }

}

