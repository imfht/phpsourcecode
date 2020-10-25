<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/28
 * Time: 10:40
 */

namespace naples\lib;
use Michelf\Markdown;
use naples\lib\Captcha\Captcha;
use naples\lib\naplesTpl\NaplesTpl;
use naples\lib\weiChat\NPqyWeichat;
use naples\lib\weiChat\NPweiChat;


/**
 * 核心类库生产工厂
 */
class Factory
{
    private static $configs=[];//工厂配置数组
    private static $is_loaded=false;//工厂配置已读标记
    private static $singles=[];//存放单例
    private static $productConfigs=[];//存放每个产品配置

    /** 读取配置数组 */
    private static function loadConfigs(){
        if (!self::$is_loaded and is_file(PATH_NAPLES.'/configs/factory.php')){
            self::$configs=require PATH_NAPLES.'/configs/factory.php';
            self::$is_loaded=true;
        }
    }

    /**
     * 读一项配置，返回配置数组
     * @param $name string 配置名
     * @param $configs array 额外覆盖项
     * @return array 结果
     */
    private static function loadConfig($name,$configs=[]){
        self::loadConfigs();
        if (!$configs and !empty(self::$productConfigs[$name])){
            return self::$productConfigs[$name];
        }
        $rel[0]=self::$configs[$name][0];
        $rel[1]=[];
        if (isset(self::$configs[$name][1])){
            if (is_array(self::$configs[$name][1])){
                $rel[1]=array_merge(self::$configs[$name][1],$configs);
            }elseif (is_string(self::$configs[$name][1])){
                $rel[1]= require self::$configs[$name][1];
                $rel[1]=array_merge($rel[1],$configs);
            }
        }
        if (!$configs){
            self::$productConfigs[$name]=$rel;
        }
        return $rel;

    }

    /**
     * 生成指定实例
     * @param $methodName string 方法名
     * @param $configs array 覆盖配置项
     * @param $isNew bool 是否产生新的实例
     * @return object
     */
    private static function getInstance($methodName,$configs=[],$isNew=false){
        $flagAddConfig=!empty($configs);
        $methodName=str_replace("naples\\lib\\Factory::get",'',$methodName);
        $configs=self::loadConfig($methodName,$configs);
        $className=$configs[0];
        if ($isNew){
            //生成新实例
            $obj=new $className();
            self::$singles[$className]=$obj;
            self::InitService($obj,$configs[1]);
            self::$singles[$className]=$obj;
            return $obj;
        }else{
            if (!$flagAddConfig and isset(self::$singles[$className])){
                return self::$singles[$className];
            }else{
                //生成新实例
                $obj=new $className();
                self::$singles[$className]=$obj;
                self::InitService($obj,$configs[1]);
                self::$singles[$className]=$obj;
                return $obj;
            }
        }
    }

    /**
     * 为一个继承自service的类执行初始化
     * @param $obj object
     * @param $configs array
     */
    private static function InitService($obj,$configs=[]){
        if (is_callable([$obj,'config'])){
            $obj->config($configs);
        }
        if (is_callable([$obj,'init'])){
            $obj->init();
        }
    }

    /**
     * 核心类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Core
     */
    public static function  getCore($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 错误处理类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return ErrorCatch
     */
    public static function  getErrorCatch($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 助手函数注册类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Help
     */
    public static function  getHelp($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 全局配置管理类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Config
     */
    public static function  getConfig($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 日志记录类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Logger
     */
    public static function  getLogger($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 调试工具类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Debug
     */
    public static function  getDebug($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 路由管理
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Route
     */
    public static function  getRoute($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * res调度
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Dispatch
     */
    public static function  getDispatch($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 注释解析器
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return DocParser
     */
    public static function  getDocParser($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 提示信息管理
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Attention
     */
    public static function  getAttention($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 视图类实例
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return NaplesTpl
     * */
    public static function getView($configs=[],$isNew=false){
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * Cookie
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Cookie
     */
    public static function  getCookie($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * Cache
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return \naples\lib\caches\fileCache
     */
    public static function  getCache($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * TplExtend
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return TplExtend
     */
    public static function  getTplExtend($configs=[],$isNew=false)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * Captcha默认验证码
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return Captcha
     */
    public static function  getCaptcha($configs=[],$isNew=true)
    {
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 微信
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return NPweiChat
     */
    public static function getWeiChat($configuration='default',$isNew=true){
        return self::getInstance(__METHOD__,['configuration'=>$configuration],$isNew);
    }

    /**
     * 企业微信
     * @param $configuration  string 配置项
     * @param $isNew bool 是否新实例
     * @return NPqyWeichat
     */
    public static function getQyWeiChat($configuration='default',$isNew=true){
        return self::getInstance(__METHOD__,['configuration'=>$configuration],$isNew);
    }
    /**
     * 数据库配置数组生成
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return DbConfig
     */
    public static function getDbConfig($configs=[],$isNew=false){
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 模型工厂
     * @param $class_name string
     * @param $connection_name string
     * @return \ORMWrapper
     */
    public static function getModel($class_name, $connection_name = null){
        $class_name="\\naples\\app\\".Factory::getDispatch()->config('moduleName')."\\model\\".$class_name;
        $m= \Model::factory($class_name, $connection_name);
        return $m;
    }

    /**
     * 本地数组数据库
     * @param $param string 数据库名
     * @return ArrData
     */
    public static function getArrDatabase($param){
        return new ArrData($param);
    }

    /**
     * 定时时间间隔任务管理
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return TimingProcess
     */
    public static function getTimingProcess($configs=[],$isNew=false){
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * PHPExcel的辅助类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return PhpExcelHelper
     */
    public static function getPhpExcelHelper($configs=[],$isNew=true){
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 文件上传辅助类
     * @param $configs array 配置数组
     * @param $isNew bool 是否新实例
     * @return FileUpload
     */
    public static function getFileUpload($configs=[],$isNew=true){
        return self::getInstance(__METHOD__,$configs,$isNew);
    }

    /**
     * 获取md文本形成的html文本
     * @param $str string
     * @return string
     */
    public static function getMdHtml($str){
        return Markdown::defaultTransform($str);
    }

    /**
     * phpExcel对象
     * @param $fromFile string
     * @return \PHPExcel
     */
    public static function getPHPExcel($fromFile=''){
        if (is_file($fromFile)){
            return \PHPExcel_IOFactory::load($fromFile);
        }else{
            return new \PHPExcel();
        }
    }

    /**
     * phpExcelWriter对象
     * @param  $objExcel \PHPExcel
     * @param $writerType string
     * @return	\PHPExcel_Writer_IWriter
     */
    public static function getPHPExcelWriter($objExcel,$writerType = 'Excel2007'){
        $objWriter = \PHPExcel_IOFactory::createWriter($objExcel, $writerType);
        return $objWriter;
    }
}