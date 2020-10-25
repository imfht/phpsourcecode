<?php
// +---------------------------------------------------------------------+
// | OneBase    | [ WE CAN DO IT JUST THINK ]                            |
// +---------------------------------------------------------------------+
// | Licensed   | http://www.apache.org/licenses/LICENSE-2.0 )           |
// +---------------------------------------------------------------------+
// | Author     | Bigotry <3162875@qq.com>                               |
// +---------------------------------------------------------------------+
// | Repository | https://gitee.com/Bigotry/OneBase                      |
// +---------------------------------------------------------------------+

/**
 * 系统环境检测
 * @return array 系统环境数据
 */
function check_env()
{
    $items = array(
        'os'      => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
        'php'     => array('PHP版本', '5.3', '5.3+', PHP_VERSION, 'success'),
        'upload'  => array('附件上传', '不限制', '2M+', '未知', 'success'),
        'gd'      => array('GD库', '2.0', '2.0+', '未知', 'success'),
        'disk'    => array('磁盘空间', '5M', '不限制', '未知', 'success'),
    );

    //PHP环境检测
    if ($items['php'][3] < $items['php'][1]) {
        $items['php'][4] = 'error';
        session('error', true);
    }

    //附件上传检测
    if (@ini_get('file_uploads')) {
        $items['upload'][3] = ini_get('upload_max_filesize');
    }

    //GD库检测
    $tmp = function_exists('gd_info') ? gd_info() : array();
    if (empty($tmp['GD Version'])) {
        $items['gd'][3] = '未安装';
        $items['gd'][4] = 'error';
        session('error', true);
    } else {
        $items['gd'][3] = $tmp['GD Version'];
    }

    unset($tmp);

    //磁盘空间检测
    if (function_exists('disk_free_space')) {
        $items['disk'][3] = floor(disk_free_space(INSTALL_APP_PATH) / (1024*1024)).'M';
    }

    return $items;
}


/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function check_dirfile()
{
    $items = array(
        array('dir',  '可写', 'success', '/upload'),
        array('dir',  '可写', 'success', '../runtime'),
        array('dir',  '可写', 'success', '../data'),
    );

    foreach ($items as &$val) {

        $item =	INSTALL_APP_PATH . $val[3];

        if ('dir' == $val[0]) {
            if (!is_writable($item)) {
                if (is_dir($item)) {
                    $val[1] = '可读';
                    $val[2] = 'error';
                    session('error', true);
                } else {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        } else {
            if (file_exists($item)) {
                if (!is_writable($item)) {
                    $val[1] = '不可写';
                    $val[2] = 'error';
                    session('error', true);
                }
            } else {
                if (!is_writable(dirname($item))) {
                    $val[1] = '不存在';
                    $val[2] = 'error';
                    session('error', true);
                }
            }
        }
    }

    return $items;
}


/**
 * 函数检测
 * @return array 检测数据
 */
function check_func()
{
    $items = array(
        array('pdo','支持','success','类'),
        array('pdo_mysql','支持','success','模块'),
        array('file_get_contents', '支持', 'success','函数'),
        array('mb_strlen','支持', 'success','函数'),
    );

    foreach ($items as &$val) {
        if (('类' == $val[3] && !class_exists($val[0])) || ('模块'==$val[3] && 
            !extension_loaded($val[0])) || ('函数'==$val[3] && !function_exists($val[0]))) {
            $val[1] = '不支持';
            $val[2] = 'error';
            session('error', true);
        }
    }

    return $items;
}


/**
 * 创建数据表
 * @param  resource $db 数据库连接资源
 */
function create_tables($db_object, $prefix = '')
{
    //读取SQL文件
    $sql = file_get_contents('../app/install/data/install.sql');
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);

    //替换表前缀
    $orginal = 'ob_';
    
    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
    
    //开始安装
    foreach ($sql as $value) {

        $value = trim($value);

        if (empty($value)) {
            continue;
        }
        if (substr($value, 0, 12) == 'CREATE TABLE') {

            if (!(false !== $db_object->execute($value))) {
                session('error', true);
            }

        } else {
            $db_object->execute($value);
        }

    }
}


/**
 * 生成系统AUTH_KEY
 */
function build_auth_key()
{
   $chars  = 'abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $chars .= '`~!@#$%^&*()_+-=[]{};:"|,.<>/?';
   $chars  = str_shuffle($chars);
   return substr($chars, 0, 40);
}


function register_administrator($db_object, $prefix, $admin, $auth)
{
    $sql = "INSERT INTO `[PREFIX]member` VALUES " .
           "(1, '[NICKNAME]', '[USERNAME]', '[PASSWORD]', '[EMAIL]', '18555550710', '[UPDATETIME]', '[CREATETIME]', 1, 0, 0, 1)";

    $password = data_md5_key($admin['password'], $auth);
    
    $time = time();
    
    $sql = str_replace(
        array('[PREFIX]', '[NICKNAME]', '[USERNAME]', '[PASSWORD]', '[EMAIL]', '[UPDATETIME]', '[CREATETIME]'),
        array($prefix, $admin['username'], $admin['username'], $password, $admin['email'], $time, $time),
        $sql);

    //执行sql
    $db_object->execute($sql);
}


/**
* 系统非常规MD5加密方法
* @param  string $str 要加密的字符串
* @return string
*/
function user_md5($str, $key = '')
{
   return '' === $str ? '' : md5(sha1($str) . $key);
}



/**
 * 写入配置文件
 * @param  array $config 配置信息
 */
function write_config($config, $auth)
{
    if (is_array($config)) {

        //读取配置内容
        $conf = file_get_contents('../app/install/data/database.tpl');

        //替换配置项
        foreach ($config as $name => $value) {
            $conf = str_replace("[{$name}]", $value, $conf);
        }

        if (!file_put_contents('../app/database.php', str_replace('[SYS_DATA_KEY]', $auth, $conf))) {
            
            session('error', true);
        }
        
        return '';
    }
}