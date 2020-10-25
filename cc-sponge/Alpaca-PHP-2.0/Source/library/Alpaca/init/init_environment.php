<?php
//系统环境
class Environment
{
    //单例模式
    private static $instance;

    //运行环境
    private  $mode;

    //配置文件
    private  $env_config;

    //env单例
    public static function env()
    {
        return self::getInstance();
    }

    //创建单例
    private static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /* init 函数,初始化系统环境 */
    public function init()
    {
        //运行环境
        $mode = getenv('MOD_ENV');
        if(empty($mode)){
            $this->mode="DEVELOPMENT";
        }

        //配置文件
        if(empty($this->env_config)){
            $this->env_config = require_once APP_PATH."/config/main.php";
        }

        $config_ex = [];
        if( $this->mode == "TEST"){
            $config_ex = require_once APP_PATH."/config/test.php";
        }elseif($this->mode == "PRODUCTION"){
            $config_ex = require_once APP_PATH."/config/production.php";
        }else{
            $config_ex = require_once APP_PATH."/config/development.php";
        }

        //合并配置文件
        foreach($config_ex as $key => $value){
            $this->env_config[$key] = $value;
        }

    }

    /* config 函数,返回配置信息 */
    public function config()
    {
        return $this->env_config;
    }
}



