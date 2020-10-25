<?php
// +----------------------------------------------------------------------
// | Loadphp Framework designed by www.loadphp.com
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.loadphp.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 亓斌 <qibin0506@gmail.com>
// +----------------------------------------------------------------------

    /**
     +------------------------------------------------------------------------------
     * 配置参数
     +------------------------------------------------------------------------------
     */
//  error_reporting(0);                                          //项目上线后将前面的 //去掉
    session_start();                                             //开启session
/*
//  在用户没有禁止cookie的情况下，进行退出操作时，由于注销了存取session.id的cookie，所以SID会变成有值
    if(''!=SID) {
        output_add_rewrite_var("PHPSESSID",session_id());
    }
*/
    define("ENCODE","utf-8");                                    //可根据项目编码修改
    header("Content-Type:text/html;charset=".ENCODE);
    date_default_timezone_set("PRC");                            //设置时区
    
    /**
     +------------------------------------------------------------------------------
     * 定义路径常量
     +------------------------------------------------------------------------------
     */
    define("LIBS_PATH",LOAD_PATH."/loadphp/libs/");              //smarty包含路径
    define("CORE_PATH",LOAD_PATH."/loadphp/core/");              //核心类包含路径
    define("CLASS_PATH",LOAD_PATH."/loadphp/class/");            //功能类包含路径
    define("FUNC_PATH",LOAD_PATH."/loadphp/function/");          //系统函数包含路径
    define("PUB_PATH",LOAD_PATH."/pub/");                        //框架级别的公用目录
    define("COMMONS_PATH",APP_PATH."/commons/");                 //用户扩展包含路径
    define("CONTROLER_PATH",APP_PATH."/controler/");             //控制器包含路径
    define("MODEL_PATH",APP_PATH."/model/");                     //model包含了路径
    define("VIEW_PATH",APP_PATH."/view/");                       //视图包含路径
    define("RUNTIME_PATH",APP_PATH."/runtime/");                 //运行时包含路径
    define("PUBLIC_PATH",APP_PATH."/public/");                   //APP级别js、图片等包含相对路径 
    define("URL",$_SERVER['SCRIPT_NAME']);                       //当前执行文件名
    
    /**
     +------------------------------------------------------------------------------
     * 设置包含路径
     +------------------------------------------------------------------------------
     */
    $include_path = get_include_path().PATH_SEPARATOR;
    $include_path .= LIBS_PATH.PATH_SEPARATOR;
    $include_path .= CORE_PATH.PATH_SEPARATOR;
    $include_path .= CLASS_PATH.PATH_SEPARATOR;
    $include_path .= FUNC_PATH.PATH_SEPARATOR;
    $include_path .= COMMONS_PATH."class/".PATH_SEPARATOR;
    $include_path .= COMMONS_PATH."function/".PATH_SEPARATOR;
    $include_path .= CONTROLER_PATH.PATH_SEPARATOR;
    $include_path .= MODEL_PATH.PATH_SEPARATOR;
    set_include_path($include_path);
    
    /**
     +------------------------------------------------------------------------------
     * 定义js等文件的路径
     +------------------------------------------------------------------------------
     */
    $public_path = dirname(URL);
    if($public_path == "/" || $public_path == "\\") {
        $public_path = "";
    }

    $pub_path = $public_path.'/pub';                            //框架目录级别的公用文件
    
    $public_path .= str_replace('.','',APP_PATH).'/public';     //APP级别的公用目录
    /**
     +------------------------------------------------------------------------------
     * 包含用户配置文件
     +------------------------------------------------------------------------------
     */
    include("loadConfig.php");
    
    /**
     +------------------------------------------------------------------------------
     * 包含系统函数文件
     +------------------------------------------------------------------------------
     */
    include("functions.php");
    
    /**
     +------------------------------------------------------------------------------
     * 自动包含用户定义函数文件
     +------------------------------------------------------------------------------
     */
    foreach(glob(COMMONS_PATH."function/*.php") as $commFile) {
        require_once($commFile);
    } 
    
    /**
     +------------------------------------------------------------------------------
     * 连接数据库
     +------------------------------------------------------------------------------
     */
    DBModel::connect(DSN,DBUSER,DBPWD);
    
    /**
     +------------------------------------------------------------------------------
     * 检测目录(第一次运行时检测)
     +------------------------------------------------------------------------------
     */
    if(!file_exists(PUBLIC_PATH."/success")) {
        checkDir();
        file_put_contents(PUBLIC_PATH."/success","success");
    }
    
    /**
     +------------------------------------------------------------------------------
     * URL路由
     +------------------------------------------------------------------------------
     */
    UrlRouter::parseUrl();
    
    /**
     +------------------------------------------------------------------------------
     * 当前控制器URL
     +------------------------------------------------------------------------------
     */ 
    define("CURURL",URL.'/'.$_GET['c']);
     
    /**
     +------------------------------------------------------------------------------
     * 根据参数实例化类
     +------------------------------------------------------------------------------
     */
    load();
?>