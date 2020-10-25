<?php
/**
 * 安装向导
 */
header('Content-type:text/html;charset=utf-8');
define('IN_INSTALL', TRUE);
// 检测是否安装过
if (file_exists('./install.lock')) {
    header("Location://".$_SERVER['HTTP_HOST']);
    die;
}
// 检测环境页面
if(@!isset($_GET['c']) || @$_GET['c']=='test'){
    require './test.php';
}
// 创建数据库页面
if(@$_GET['c']=='create'){
    require './create.php';
}
// 安装成功页面
if(@$_GET['c']=='success'){
    try{
        // 判断是否为post
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            $install = './db/install.sql';
            //判断SQL文件是否存在
            if (!file_exists($install)) {
                echo '数据库文件不存在';
                die;
            }
            $data = $_POST;
            // 连接数据库
            $link = @new mysqli("{$data['DB_HOST']}:{$data['DB_PORT']}",$data['DB_USER'],$data['DB_PWD']);
            // 获取错误信息
            $error = $link->connect_error;
            if (!is_null($error)) {
                // 转义防止和alert中的引号冲突
                $error = addslashes($error);
                die("<script>alert('数据库链接失败:$error');history.go(-1)</script>");
            }
            // 设置字符集
            $link->query("SET NAMES'utf8'");
            $link->server_info>5.0 or die("<script>alert('请将您的mysql升级到5.0以上');history.go(-1)</script>");
            // 创建数据库并选中
            if(!$link->select_db($data['DB_NAME'])){
                $create_sql = 'CREATE DATABASE IF NOT EXISTS '.$data['DB_NAME'].' DEFAULT CHARACTER SET utf8;';
                $link->query($create_sql) or die('创建数据库失败');
                $link->select_db($data['DB_NAME']);
            }
            // 导入sql数据并创建表
            $sql_str = file_get_contents($install);
            $str = preg_replace('/(--.*)|(\/\*(.|\s)*?\*\/)|(\n)/', '',$sql_str);
            if($data['DB_PREFIX'] != 'ai_'){
                $str = str_replace('ai_',$data['DB_PREFIX'],$str);
            }
            $sql_array = explode(';',trim($str));
            foreach ($sql_array as $k => $v) {
                if (!empty($v)) {
                    $link->query($v);
                }
            }
            $link->close();
            $db_str=<<<php
<?php
return [
    // 数据库类型
    'type'            => 'mysql',
    // 服务器地址
    'hostname'        => '{$data['DB_HOST']}',
    // 数据库名
    'database'        => '{$data['DB_NAME']}',
    // 用户名
    'username'        => '{$data['DB_USER']}',
    // 密码
    'password'        => '{$data['DB_PWD']}',
    // 端口
    'hostport'        => '{$data['DB_PORT']}',
    // 连接dsn
    'dsn'             => '',
    // 数据库连接参数
    'params'          => [],
    // 数据库编码默认采用utf8
    'charset'         => 'utf8',
    // 数据库表前缀
    'prefix'          => '{$data['DB_PREFIX']}',
    // 数据库调试模式
    'debug'           => false,
    // 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
    'deploy'          => 0,
    // 数据库读写是否分离 主从式有效
    'rw_separate'     => false,
    // 读写分离后 主服务器数量
    'master_num'      => 1,
    // 指定从服务器序号
    'slave_no'        => '',
    // 是否严格检查字段是否存在
    'fields_strict'   => true,
    // 数据集返回类型
    'resultset_type'  => 'array',
    // 自动写入时间戳字段
    'auto_timestamp'  => false,
    // 时间字段取出后的默认时间格式
    'datetime_format' => false,
    // 是否需要进行SQL性能分析
    'sql_explain'     => false,
    // Query类
    'query'           => '\\think\\db\\Query',
];
php;
            // 创建数据库链接配置文件
            file_put_contents('../../config/database.php',$db_str);
            @touch('./install.lock');
            //组装安装url
            $url = $_SERVER['HTTP_HOST'].trim($_SERVER['SCRIPT_NAME'],'install/index.php');
            header("Location: success.html");
        }
    }catch (\Exception $e) {
        exit($e->getMessage());
    }
}