<?php
// +----------------------------------------------------------------------
// | Author: Zaker <49007623@qq.com>
// +----------------------------------------------------------------------

namespace app\common\behavior;

use think\Loader;
use think\Db;
use think\Request;

/**
 * 初始化基础信息行为
 */
class InitBase
{

    /**
     * 初始化行为入口
     */
    public function run()
    {
        
        // 初始化常量
        $this->initConst();
        
        // 初始化插件静态资源
        $this->initAddonStatic();
        
        // 初始化配置
        $this->initConfig();
        
        // 初始化数据库
        $this->initDbInfo();
        
    
        
        // 注册命名空间
        $this->registerNamespace();
    }
    
    /**
     * 初始化数据库
     */
    private function initDbInfo()
    {
        
        $database_config = config('database');
        
        $list_rows = config('list_rows');
    
        define('DB_PREFIX', $database_config['prefix']);
        
        empty($list_rows) ? define('DB_LIST_ROWS', 10) : define('DB_LIST_ROWS', $list_rows);
    }
    
    
    /**
     * 初始化常量
     */
    private function initConst()
    {
        
        // 初始化目录常量
        $this->initDirConst();
        
        // 初始化结果常量
        $this->initResultConst();
        
        // 初始化数据状态常量
        $this->initDataStatusConst();
        
        // 初始化时间常量
        $this->initTimeConst();
        
        // 初始化系统常量
        $this->initSystemConst();
        
        // 初始化路径常量
        $this->initPathConst();
    }
    
    /**
     * 初始化目录常量
     */
    private function initDirConst()
    {
        
        define('LAYER_LOGIC_NAME'     , 'logic');
        define('LAYER_MODEL_NAME'     , 'model');
        define('LAYER_SERVICE_NAME'   , 'service');
        define('LAYER_CONTROLLER_NAME', 'controller');
    }
    
    /**
     * 初始化结果常量
     */
    private function initResultConst()
    {
        
        define('RESULT_SUCCESS' , 'success');
        define('RESULT_ERROR'   , 'error');
        define('RESULT_REDIRECT', 'redirect');
        define('RESULT_MESSAGE' , 'message');
        define('RESULT_URL'     , 'url');
        define('RESULT_DATA'    , 'data');
    }
    
    /**
     * 初始化数据状态常量
     */
    private function initDataStatusConst()
    {
        
        define('DATA_COMMON_STATUS' ,  'status');
        define('DATA_NORMAL'        ,  1);
        define('DATA_DISABLE'       ,  0);
        define('DATA_DELETE'        , -1);
        define('DATA_SUCCESS'       , 1);
        define('DATA_ERROR'         , 0);
    }
    
    /**
     * 初始化时间常量
     */
    private function initTimeConst()
    {
        
        define('TIME_CT_NAME' ,  'create_time');
        define('TIME_UT_NAME' ,  'update_time');
        define('TIME_NOW'     ,   time());
    }
    
    /**
     * 初始化系统常量
     */
    private function initSystemConst()
    {

    	
        define('SYS_APP_NAMESPACE'              , config('app_namespace'));
        define('SYS_HOOK_DIR_NAME'              , 'hook');
        define('SYS_ADDON_DIR_NAME'             , 'addon');
        define('SYS_COMMON_DIR_NAME'            , 'common');

        define('SYS_DRIVER_DIR_NAME'            , 'driver');
        define('SYS_STATIC_DIR_NAME'            , 'static');
        
        
        define('SYS_VERSION'                    , '1.0.0');
        define('SYS_ADMINISTRATOR_ID'           , 1);
        define('SYS_DSS'                        , '/');
        define('SYS_DS_CONS'                    , '\\');
        define('SYS_ENCRYPT_KEY'                , '}a!vI9wX>l2V|gfZp{8`;jzR~6Y1_p-e,#"MN=e:');
    }
    
    /**
     * 初始化路径常量
     */
    private function initPathConst()
    {
        
    	// 定义URL
    	if (!defined('WEB_URL')) {
    		$url = rtrim(dirname(rtrim($_SERVER['SCRIPT_NAME'], '/')), '/');
    		define('WEB_URL', (('/' == $url || '\\' == $url) ? '' : $url));
    		
    	}
    	
    	
    	define('WEB_PATH_UPLOAD'    , WEB_URL .SYS_DSS. 'uploads'    . SYS_DSS);
    	define('WEB_PATH_PICTURE'   , WEB_PATH_UPLOAD . 'picture'   . SYS_DSS);
    	define('WEB_PATH_FILE'      , WEB_PATH_UPLOAD . 'file'      . SYS_DSS);
    	
        define('PATH_ADDON'     , ROOT_PATH   . SYS_ADDON_DIR_NAME . DS);
        define('PATH_PUBLIC'    , ROOT_PATH   . 'public'    . DS);
        define('PATH_UPLOAD'    , ROOT_PATH . 'uploads'    . DS);
        define('PATH_PICTURE'   , PATH_UPLOAD . 'picture'   . DS);
        define('PATH_FILE'      , PATH_UPLOAD . 'file'      . DS);
        define('PATH_SERVICE'   , ROOT_PATH   . DS . SYS_APP_NAMESPACE . DS . SYS_COMMON_DIR_NAME . DS . LAYER_SERVICE_NAME . DS);
    }
    
    /**
     * 初始化配置信息
     */
    private function initConfig()
    {
        
        $model = model(SYS_COMMON_DIR_NAME . SYS_DSS . 'Config');
        
        $config_list = $model->all();
        
        $config_array = [];
        
        foreach ($config_list as $info):
            
        $config_array[$info['name']] = $info['value'];
        
        endforeach;
        
        config($config_array);
       
        $this->initTmconfig();
    }
    
    /**
     * 初始化动态配置信息
     */
    private function initTmconfig()
    {
        
        $cache_max_number           = config('cache_max_number');
       
        $cache_clear_interval_time  = config('cache_clear_interval_time');
        
        define('SYS_CACHE_MAX_NUMBER'           , empty($cache_max_number)          ? 1000  : (int)$cache_max_number);
        define('SYS_CACHE_CLEAR_INTERVAL_TIME'  , empty($cache_clear_interval_time) ? 600   : (int)$cache_clear_interval_time);
    }
    
    /**
     * 注册命名空间
     */
    private function registerNamespace()
    {
        
        // 注册插件根命名空间
        Loader::addNamespace(SYS_ADDON_DIR_NAME, PATH_ADDON);
    }
    
    /**
     * 初始化插件静态资源
     */
    private function initAddonStatic()
    {
        
        $regex = '/[^\s]+\.(jpg|gif|png|bmp|js|css)/i';

        $url = htmlspecialchars(addslashes(Request::instance()->url()));
        
        if(strpos($url, SYS_ADDON_DIR_NAME) !== false && preg_match($regex, $url)) :

            $url = PATH_ADDON . str_replace(SYS_DSS, DS, substr($url, strlen(SYS_DSS . SYS_ADDON_DIR_NAME . SYS_DSS)));
        
            !is_file($url) && exit('plugin resources do not exist.');

            $ext = pathinfo($url, PATHINFO_EXTENSION);

            $header = 'Content-Type:';

            in_array($ext, ['jpg','gif','png','bmp']) && $header .= "image/jpeg;text/html;";

            switch ($ext)
            {
                case 'css': $header .= "text/css;"; break;
                case 'js' : $header .= "application/x-javascript;"; break;
            }

            $header .= "charset=utf-8";

            header($header);

            exit(file_get_contents($url));

        endif;
    }
}
