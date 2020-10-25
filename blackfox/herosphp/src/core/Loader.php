<?php
/**
 * HerosPHP 资源加载器类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 */

namespace herosphp\core;

use herosphp\model\CommonService;
use herosphp\model\MysqlModel;

class Loader {

    //imported class cache
    protected static $IMPORTED_FILES = array();

    //imported configs cache
    protected static $CONFIGS = array();

    //instance cache array
    protected static $INSTANES = array();

    /**
     * 加载一个类或者加载一个包
     * 如果加载的包中有子文件夹不进行循环加载
     * 参数格式：'app:article.model.articleModel'
     * article.model.articleModel 相对的路径信息
     * 如果不填写应用名称 ，例如‘article.model.articleModel’，那么加载路径则相对于默认的应用路径
     *
     * 加载一个类的参数方式：'article.model.articleModel'
     * 加载一个包的参数方式：'article.service.*'
     * @param $classPath
     * @param int $type 导入了类包的类别，详情见Herosphp.const.php
     * @param $extension
     * @return boolean
     */
    public static function import( $classPath, $type = IMPORT_APP, $extension=EXT_PHP ) {

        if ( !$classPath ) return false;
        //如果该文件已经导入了，就不再导入
        $classKey = $classPath.'_'.$type.'_'.$extension;
        if ( isset(self::$IMPORTED_FILES[$classKey]) )  return false;

        switch ( $type ) {

            case IMPORT_APP :
                $path = APP_PATH.'modules/';
                break;

            case IMPORT_CUSTOM :
                $path = APP_ROOT;
                break;

            default:
                return false;
        }

        $classPathInfo = explode('.', $classPath);
        $classPath = str_replace('.', '/', $classPath);
        if ( $classPathInfo[count($classPathInfo)-1] == '*' ) {     //加载包

            $dir = $path.$classPath;
            chdir($dir);
            $classFiles = glob('*'.$extension);
            foreach ($classFiles as $file ) {
                if ( file_exists($dir.'/'.$file) ) {
                    require $dir.'/'.$file;
                }
            }

        } else {    //包含单个文件
            if ( file_exists($path.$classPath.$extension) ) {
                require $path.$classPath.$extension;
            }
        }

        self::$IMPORTED_FILES[$classKey] = 1;
        return true;
    }

    /**
     * 包含一个文件,并返回该文件的内容
     *
     * 包含一个文件的参数方式：'article.model.articleModel'
     * @param $classPath
     * @param int $type 导入了类包的类别，详情见Herosphp.const.php
     * @param $extension
     * @return boolean
     */
    public static function __include( $classPath, $type = IMPORT_APP, $extension=EXT_PHP ) {

        //组合文件路径
        switch ( $type ) {

            case IMPORT_APP :
                $path = APP_PATH.'/modules/';
                break;

            case IMPORT_CUSTOM :
                $path = APP_ROOT;
                break;

            default:
                return false;
        }

        $classPath = str_replace('.', '/', $classPath);
        return include $path.$classPath.$extension;
    }

    /**
     * 加载配置信息
     * @param string $key 配置文件名称key， 如果没有指定则加载所有配置文档
     * @param string $section (格式：beans.user) 配置文档所属片区|模块，如果没有指定，则默认加载当前应用配置文档根目录的配置文件
     * @return array
     */
    public static function config($key='*', $section=null) {

        if ( isset(self::$CONFIGS[$section][$key]) ) {
            return self::$CONFIGS[$section][$key];
        }
        if( $section == null ) {
            $configDir = APP_PATH.'configs/';   //加载应用configs根目录中的配置文件
        } else {
            $configDir = APP_PATH.'configs/'.str_replace('.', '/', $section).'/';   //加载应用configs文件夹中的配置文档
        }

        if ( $key != '*' ) {
            $configFile = $configDir.$key.EXT_CONFIG;
            if ( file_exists($configFile) ) {
                self::$CONFIGS[$section][$key] = include $configFile;
            }
        } else if ( file_exists($configDir) ) {
            chdir($configDir);
            $configFiles = glob("*.config.php");
            $configs = array();
            foreach ( $configFiles as $file ) {
                $tempConfig = include $configDir.$file;
                $configs = array_merge($configs, $tempConfig);
            }
            self::$CONFIGS[$section][$key] = &$configs;
        }

        if ( self::$CONFIGS[$section][$key] ) {
            return self::$CONFIGS[$section][$key];
        } else {
            return array();
        }
    }

    /**
     * 创建实体单例
     * @param $classPath
     * @return mixed
     */
    public static function singleton($classPath) {
        if ( !isset(self::$INSTANES[$classPath]) ) {
            $reflect = new \ReflectionClass($classPath);
            self::$INSTANES[$classPath] = $reflect->newInstance();
        }
        return self::$INSTANES[$classPath];
    }

    /**
     * 加载modelDao
     * @param string $modelName
     * @return MysqlModel
     */
    public static function model($modelPath) {
        return self::singleton($modelPath);
    }

    /**
     * @param $servicePath
     * @return CommonService
     */
    public static function service($servicePath, $singleton = true) {
        if ($singleton) {
            return self::singleton($servicePath);
        } else {
            $reflect = new \ReflectionClass($servicePath);
            return $reflect->newInstance();
        }
    }
}
