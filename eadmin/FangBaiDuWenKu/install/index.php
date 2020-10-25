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
if(@!isset($_GET['c']) || @$_GET['c']=='agreement'){
    require './agreement.html';
}
// 检测环境页面
if(@$_GET['c']=='test'){
    require './test.html';
}
// 创建数据库页面
if(@$_GET['c']=='create'){
    require './create.html';
}
// 安装成功页面
if(@$_GET['c']=='success'){
    // 判断是否为post
    if($_SERVER['REQUEST_METHOD']=='POST'){
        $data=$_POST;
        
        

        	 	$link=@new mysqli($data['DB_HOST'],$data['DB_USER'],$data['DB_PWD']);
    
        // 获取错误信息
        $error=$link->connect_error;
        if (!is_null($error)) {
            // 转义防止和alert中的引号冲突
            $error=addslashes($error);
            die("<script>alert('数据库链接失败:$error');history.go(-1)</script>");
        }
        // 设置字符集
        $link->query("SET NAMES 'utf8'");
        $link->server_info>5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
        // 创建数据库并选中
        if(!$link->select_db($data['DB_NAME'])){
            $create_sql='CREATE DATABASE IF NOT EXISTS '.$data['DB_NAME'].' DEFAULT CHARACTER SET utf8;';
            $link->query($create_sql) or die('创建数据库失败');
            $link->select_db($data['DB_NAME']);
        }
        // 导入sql数据并创建表
        $shujuku_str=file_get_contents('./easysns.sql');
        $sql_array=preg_split("/;[\r\n]+/", str_replace('es_',$data['DB_PREFIX'],$shujuku_str));
        foreach ($sql_array as $k => $v) {
            if (!empty($v)) {
                $link->query($v);
            }
        }
        $link->close();

        
       
        
        
        
        
        
        
        
        
        
        
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $password ='';
        for ( $i = 0; $i < 5; $i++ )
        {
        	// 这里提供两种字符获取方式
        	// 第一种是使用 substr 截取$chars中的任意一位字符；
        	// 第二种是取字符数组 $chars 的任意元素
        	// $password .= substr($chars, mt_rand(0, strlen($chars) – 1), 1);
        	$password .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        
        
        $db_str=<<<php
<?php

return [
    'type'            => 'mysql',
    'hostname'        => '{$data['DB_HOST']}',
    'database'        => '{$data['DB_NAME']}',
    'username'        => '{$data['DB_USER']}',
    'password'        => '{$data['DB_PWD']}',
    'hostport'        => '{$data['DB_PORT']}',
    'dsn'             => '',
    'params'          => [],
    'charset'         => 'utf8',
    'prefix'          => '{$data['DB_PREFIX']}', 
    'debug'           => true,
    'deploy'          => 0,
    'rw_separate'     => false,
    'master_num'      => 1,
    'slave_no'        => '',
    'fields_strict'   => true,
    'resultset_type'  => 'array',
    'auto_timestamp'  => false,
    'datetime_format' => false,
    'sql_explain'     => false,
    'builder'         => '',
    'query'           => '\\think\\db\\Query',
];
php;
        // 创建数据库链接配置文件
        
        $fp=fopen('../app/database.php',"w");
        
        fputs($fp,$db_str);
        
        fclose($fp);
        
        
        $db_str=<<<php
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
    
        				
];
php;
        $fp=fopen('../app/extra/session.php',"w");
        
        fputs($fp,$db_str);
        
        fclose($fp);
       
        $db_str=<<<php
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
        
        $fp=fopen('../app/extra/cache.php',"w");
        
        fputs($fp,$db_str);
        
        fclose($fp);
        $db_str=<<<php
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
        
        $fp=fopen('../app/extra/cookie.php',"w");
        
        fputs($fp,$db_str);
        
        fclose($fp);
        
        
      //  file_put_contents('../application/database.php', $db_str);
        @touch('./install.lock');
        require './success.html';
    }

}

