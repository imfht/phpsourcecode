<?php
include_once LIB.'FrontendController.php';

class FrontendCore{
    public static function run(){
        spl_autoload_register('FrontendCore::load');
        register_shutdown_function('FrontendCore::fatalError');
        set_error_handler('FrontendCore::appError');
        set_exception_handler('FrontendCore::appException');

    }
    public static function load($class){
        $file=Controllers.$class.'.php';
        if(is_file($file)){
            require_once $file;
        }
    }
    // 致命错误捕获
    public static  function fatalError() {
        $e=error_get_last();
        if($e &&$e['type']!=8192){
            $data['info']=$e['message'].'在'.$e['line'].'行';
            $data['file']=$e['file'];
            $curl = curl_init(WWW.'/new.php?c=error');
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);//SSL证书认证
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);//严格认证
            curl_setopt($curl, CURLOPT_CAINFO,'');//证书地址
            curl_setopt($curl, CURLOPT_HEADER, 0 ); // 过滤HTTP头
            curl_setopt($curl,CURLOPT_RETURNTRANSFER, 1);// 显示输出结果
            curl_setopt($curl,CURLOPT_POST,true); // post传输数据
            curl_setopt($curl,CURLOPT_POSTFIELDS,$data);// post传输数据
            curl_exec($curl);
            curl_close($curl);
            header('Location:/500.html');
        }

    }
    /**
     * 自定义异常处理
     * @access public
     * @param mixed $e 异常对象
     */
    public static function appException($e) {
        exit('程序异常了!!');
    }
    /**
     * 自定义错误处理
     * @access public
     * @param int $errno 错误类型
     * @param string $errstr 错误信息
     * @param string $errfile 错误文件
     * @param int $errline 错误行数
     * @return void
     */
    public static  function appError($errno, $errstr, $errfile, $errline) {
        if(DEBUG){
            echo  $errstr.'在'.$errfile.'在第'.$errline.'行';
        }
        //exit('程序出错了!!');
    }
}
//获取数据库配置信息
db_dns($db_host, $db_name,$db_user,$db_pass);
$db_host = $db_user = $db_pass = $db_name = NULL;
C('prefix',$prefix);
//设置缓存
if (!file_exists(ROOT_PATH.'temp/caches/frontend'))
{
    @mkdir(ROOT_PATH.'temp/caches/frontend', 0777,true);
    @chmod(ROOT_PATH.'temp/caches/frontend', 0777);
}

if (!file_exists(ROOT_PATH.'temp/compiled/frontend'))
{
    @mkdir(ROOT_PATH.'temp/compiled/frontend', 0777,true);
    @chmod(ROOT_PATH.'temp/compiled/frontend', 0777);
}
//caches
if (!file_exists(ROOT_PATH.'temp/query_caches'))
{
    @mkdir(ROOT_PATH.'temp/query_caches', 0777);
    @chmod(ROOT_PATH.'temp/query_caches', 0777);
}
if (!file_exists(ROOT_PATH.'temp/static_caches'))
{
    @mkdir(ROOT_PATH.'temp/static_caches', 0777);
    @chmod(ROOT_PATH.'temp/static_caches', 0777);
}

$smarty->compile_dir   = ROOT_PATH . 'temp/compiled/frontend';
$smarty->cache_dir   = ROOT_PATH . 'temp/caches/frontend';

FrontendCore::run();
$class=isset($_REQUEST['c'])?trim($_REQUEST['c']):'home';
$class=ucfirst(strtolower($class));
$controller=new $class($smarty);
$action=isset($_REQUEST['a'])?trim($_REQUEST['a']):'index';
$controller->$action();

