<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace TPHelper\Org;
/**
 * 用于ThinkPHP的自动生成
 */
class Build {

    static protected $controller   =   '<?php
namespace [MODULE]\Controller;
use Think\Controller;
class [CONTROLLER]Controller extends CommonController {
    public function index(){
        $this->display();
    }
}';

    static protected $model         =   '<?php
namespace [MODULE]\Model;
use Think\Model;
class [MODEL]Model extends CommonModel {

}';
    //入口文件
    static protected $entry         =   '<?php
// 应用入口文件
// 检测PHP环境
if(version_compare(PHP_VERSION,"5.3.0","<"))  die("require PHP > 5.3.0 !");
define("BIND_MODULE","[MODULE]");
// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define("APP_DEBUG",True);
// 定义应用目录
define("APP_PATH","./App/");
// 定义运行时目录
define("RUNTIME_PATH","Runtime/");
// 引入ThinkPHP入口文件
require "./PHP/ThinkPHP.php";
';
    // 检测应用目录是否需要自动创建
    static public function checkDir($module){
        if(!is_dir(APP_PATH.$module)) {
            // 创建模块的目录结构
            self::buildAppDir($module);
        }elseif(!is_dir(LOG_PATH)){
            // 检查缓存目录
            self::buildRuntime();
        }
    }

    // 创建应用和模块的目录结构
    static public function buildAppDir($module,$path = '') {
        // 没有创建的话自动创建
        if(!is_dir($path)) mkdir($path,0777,true);
        $APP_PATH = $path.'App/';
        if(!is_dir($APP_PATH)) mkdir($APP_PATH,0777,true);
        
        if(is_writeable($APP_PATH)) {
            $COMMON_PATH = $APP_PATH.'/Common/';
            $CONF_PATH = $APP_PATH.'/Common/Conf/';
            $RUNTIME_PATH = $path.'Runtime/';
            $CACHE_PATH = $RUNTIME_PATH.'/Cache/';
            $LOG_PATH = $RUNTIME_PATH.'/Logs/';
            $TEMP_PATH = $RUNTIME_PATH.'/Temp/';
            $DATA_PATH = $RUNTIME_PATH.'/Data/';
            
            $dirs  = array(
                $COMMON_PATH,
                $COMMON_PATH.'Common/',
                $CONF_PATH,
                $APP_PATH.$module.'/',
                $APP_PATH.$module.'/Common/',
                $APP_PATH.$module.'/Controller/',
                $APP_PATH.$module.'/Model/',
                $APP_PATH.$module.'/Conf/',
                $APP_PATH.$module.'/View/',
                $RUNTIME_PATH,
                $CACHE_PATH,
                $LOG_PATH,
                $LOG_PATH.$module.'/',                
                $TEMP_PATH,
                $DATA_PATH,
                );
            foreach ($dirs as $dir){
                if(!is_dir($dir))  mkdir($dir,0777,true);
            }
            
            // 写入目录安全文件
            self::buildDirSecure($dirs);
            // 写入应用配置文件
            if(!is_file($CONF_PATH.'config'.CONF_EXT))
                file_put_contents($CONF_PATH.'config'.CONF_EXT,'.php' == CONF_EXT ? "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);":'');
            // 写入模块配置文件
            if(!is_file($APP_PATH.$module.'/Conf/config'.CONF_EXT))
                file_put_contents($APP_PATH.$module.'/Conf/config'.CONF_EXT,'.php' == CONF_EXT ? "<?php\nreturn array(\n\t//'配置项'=>'配置值'\n);":'');
        }else{
            header('Content-Type:text/html; charset=utf-8');
            exit('应用目录['.APP_PATH.']不可写，目录无法自动生成！<BR>请手动生成项目目录~');
        }
    }

    // 检查缓存目录(Runtime) 如果不存在则自动创建
    static public function buildRuntime() {
        if(!is_dir(RUNTIME_PATH)) {
            mkdir(RUNTIME_PATH);
        }elseif(!is_writeable(RUNTIME_PATH)) {
            header('Content-Type:text/html; charset=utf-8');
            exit('目录 [ '.RUNTIME_PATH.' ] 不可写！');
        }
        mkdir(CACHE_PATH);  // 模板缓存目录
        if(!is_dir(LOG_PATH))   mkdir(LOG_PATH);    // 日志目录
        if(!is_dir(TEMP_PATH))  mkdir(TEMP_PATH);   // 数据缓存目录
        if(!is_dir(DATA_PATH))  mkdir(DATA_PATH);   // 数据文件目录
        return true;
    }

    // 创建控制器类
    static public function buildController($module,$controller='Index',$path = '') {
        $file   =   $path.$module.'/Controller/'.$controller.'Controller'.EXT;
        if(!is_file($file)){
            $content = str_replace(array('[MODULE]','[CONTROLLER]'),array($module,$controller),self::$controller);
            if(!C('APP_USE_NAMESPACE')){
                $content    =   preg_replace('/namespace\s(.*?);/','',$content,1);
            }
            file_put_contents($file,$content);
        }
    }

    // 创建模型类
    static public function buildModel($module,$model,$path = '') {
        $file   =   $path.$module.'/Model/'.$model.'Model'.EXT;
        if(!is_file($file)){
            $content = str_replace(array('[MODULE]','[MODEL]'),array($module,$model),self::$model);
            if(!C('APP_USE_NAMESPACE')){
                $content    =   preg_replace('/namespace\s(.*?);/','',$content,1);
            }
            file_put_contents($file,$content);
        }
    }

    // 创建入口文件
    static public function buildEntry($module,$entry = '') {
        if(!is_file($entry)){
            $content = str_replace(array('[MODULE]','[CONTROLLER]'),array($module,$controller),self::$entry);
            file_put_contents($entry,$content);
        }
    }

    // 创建视图文件夹
    static public function buildView($module,$view_dir,$path = '') {
        $dir   =   $path.$module.'/View/'.$view_dir.'/';
        if(!is_dir($dir))  mkdir($dir,0777,true);
    }

    // 生成目录安全文件
    static public function buildDirSecure($dirs=array()) {
        // 目录安全写入（默认开启）
        defined('BUILD_DIR_SECURE')  or define('BUILD_DIR_SECURE',    true);
        if(BUILD_DIR_SECURE) {
            defined('DIR_SECURE_FILENAME')  or define('DIR_SECURE_FILENAME',    'index.html');
            defined('DIR_SECURE_CONTENT')   or define('DIR_SECURE_CONTENT',     ' ');
            // 自动写入目录安全文件
            $content = DIR_SECURE_CONTENT;
            $files = explode(',', DIR_SECURE_FILENAME);
            foreach ($files as $filename){
                foreach ($dirs as $dir)
                    file_put_contents($dir.$filename,$content);
            }
        }

    }
}