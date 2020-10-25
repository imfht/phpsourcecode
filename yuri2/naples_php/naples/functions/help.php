<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/30
 * Time: 14:37
 */
//系统助手函数声明处

use naples\lib\Factory;

/**
 * 获取|设置 全局配置 value1 保存默认代表获取
 * @param $action string 配置名
 * @param $value1 mixed 设置值
 * @param $value2 mixed 额外值
 * @return mixed 配置值
 */
function config($action='',$value1=FLAG_NOT_SET, $value2=null){
    return Factory::getConfig()->getSetConfig($action,$value1, $value2);
}

/**
 * 调试变量
 * @param $var mixed
 * @param $echo bool 是否打印
 * @param $label  string 标题
 * @param $flags int htmlspecialchars参数
 * @return string
 */
function dump($var, $echo = true, $label = null, $flags = ENT_SUBSTITUTE){
    return Yuri2::dump($var,$echo,$label,$flags);
}

/**
 * 追踪变量到后台记录
 * @param $key string 键名
 * @param $value mixed
 */
function trace($key,$value){
    Factory::getDebug()->traceDebug($key,$value);
}

/**
 * 返回加工后的url
 * @param $url string a/b/c 或 http://a.com/b/c
 * @param $params array gets
 * @return string
 */
function url($url='',$params=[]){
    if ($url=='#'){$url='';}
    return Factory::getRoute()->url($url,$params);
}

/**
 * 基于目前地址 返回地址
 * @param $url string a/b/c
 * @param $params array gets
 * @return string
 */
function urlBased($url='',$params=[]){
    if ($url=='#'){$url='';}
    return Factory::getRoute()->url(res($url),$params);
}

/**
 * 基于目前的res，生成新的res
 * @param $path string res
 * @param $param array res路由参数
 * @return string
 */
function res($path='',$param=[]){
    $module=Yuri2::arrPublic('p.module');
    $controller=Yuri2::arrPublic('p.controller');
    $action=Yuri2::arrPublic('p.action');
    $pathArr=Yuri2::explodeWithoutNull($path,'/');
    $paramStr='';
    if ($param){
        if (Yuri2::isAssoc($param)){
            foreach ($param as $k=>$v){
                $paramStr.="/$k/$v";
            }
        }else{
            $paramStr='/'.implode('/',$param);
        }
    }
    switch (count($pathArr)){
        case 0:
            return implode('/',[$module,$controller,$action]).$paramStr;
        case 1:
            return implode('/',[$module,$controller,$pathArr[0]]).$paramStr;
        case 2:
            return implode('/',[$module,$pathArr[0],$pathArr[1]]).$paramStr;
        default:
            return implode('/',$pathArr).$paramStr;
    }
}

/**
 * 带成功提示的跳转
 * @param $title string 提示内容
 * @param $url string
 * @param $time int 倒计时
 */
function success($title='',$url='',$time=3){
    $args=func_get_args();
    $attention=Factory::getAttention();
    ob_end_clean();
    $content=call_user_func_array([$attention,__FUNCTION__],$args);
    echo $content;
    exit();
}

/**
 * 带错误提示的跳转
 * @param $title string 提示内容
 * @param $url string
 * @param $time int 倒计时
 */
function error($title='',$url='',$time=3){
    $args=func_get_args();
    $attention=Factory::getAttention();
    ob_end_clean();
    $content=call_user_func_array([$attention,__FUNCTION__],$args);
    echo $content;
    exit();
}

/**
 * 重定向
 * @param $url string
 * @param $params array gets
 */
function  redirect($url,$params=[]){
    Yuri2::redirect($url,$params);
}

/**
 * 设置|获取 cookie
 * @param $key string 键名
 * @param $value mixed 键值
 * @param $expiry int 过期时间 (s)
 * @return mixed
 */
function cookie($key,$value=FLAG_NOT_SET,$expiry=604800){
    $cookie=Factory::getCookie();
    if (!empty($key)){
        $key=str_replace('.','_',VAR_PREFIX).$key;
    }
    if ($value==FLAG_NOT_SET){
        return $cookie->get($key);
    }else{
        return $cookie->set($key,$value,$expiry);
    }
}

/**
 * 是否有某个cookie
 * @param $key string
 * @return bool
 */
function hasCookie($key){
    $rel=cookie($key);
    if (isFlagNotSet($rel)){
        return false;
    }else{
        return true;
    }
}

/**
 * 缓存设置|获取
 * @param $key string 键名
 * @param $value mixed 键值
 * @param $expire int 过期倒计时 留空表示永久
 * @return mixed
 */
function cache($key,$value=FLAG_NOT_SET,$expire=null){
    $cache=Factory::getCache();
    if ($value==FLAG_NOT_SET){
        $rel= $cache->get($key,FLAG_NOT_SET);
    }else{
        if ($expire=='++'){
            $rel=  $cache->inc($key,$value);
        }elseif($expire=='--'){
            $rel=  $cache->dec($key,$value);
        }else{
            $rel=  $cache->set($key,$value,$expire);
        }
    }
    if (config('debug')){
        if (isFlagNotSet($rel)){
            Factory::getDebug()->cacheHitOrNot(false,$key);
        }else{
            Factory::getDebug()->cacheHitOrNot(true,$key);
        }
    }

    return $rel;
}

/**
 * 是否有某个缓存
 * @param $key string
 * @return bool
 */
function hasCache($key){
    $rel=cache($key);
    if (isFlagNotSet($rel)){
        return false;
    }else{
        return true;
    }
}

/**
 * 是否是不存在标志值
 * @param $var mixed
 * @return bool
 */
function isFlagNotSet($var){
    if ($var===FLAG_NOT_SET){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取|设置 $_REQUEST数组元素
 * @param $target string 目标
 * @param $value mixed 设置值
 * @return mixed 结果
 */
function request($target='',$value=FLAG_NOT_SET){
    return Yuri2::arrPublic('request.'.$target,$value);
}

/**
 * 获取|设置 $_GET数组元素
 * @param $target string 目标
 * @param $value mixed 设置值
 * @return mixed 结果
 */
function get($target='',$value=FLAG_NOT_SET){
    return Yuri2::arrPublic('get.'.$target,$value);
}

/**
 * 获取|设置 $_POST数组元素
 * @param $target string 目标
 * @param $value mixed 设置值
 * @return mixed 结果
 */
function post($target='',$value=FLAG_NOT_SET){
    return Yuri2::arrPublic('post.'.$target,$value);
}

/**
 * 获取|设置 $_SESSION数组元素
 * @param $target string 目标
 * @param $value mixed 设置值
 * @return mixed 结果
 */
function session($target='',$value=FLAG_NOT_SET){
    if (!empty($target)){
        $target=VAR_PREFIX.$target;
    }
    return Yuri2::arrPublic('session.'.$target,$value);
}

/**
 * 返回毫秒级时间戳 如1488348016345
 * @return float
 */
function getMillisecond() {
    list($t1, $t2) = explode(' ', microtime());
    return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
}

/**
 * 返回微秒级日期 如2017/03/01 14:00:16 345541
 * @return string
 */
function getMilliDate() {
    list($t1, $t2) = explode(' ', microtime());
    $date=date('Y/m/d H:i:s',$t2);
    $milli=round(floatval($t1)*1000000);
    return $date.' '.$milli;
}

/**
 * 快速记录到日志
 * @param $var mixed 待记录变量
 * @param $label string 标题
 * @param $level int 级别 1红色,2黄色,3绿色
 */
function fastLog($var,$label=null,$level=3){
    if ($label){
        $label.='----IP : '.Yuri2::getIp();
    }
    Factory::getLogger()->log($var,$label,$level);
}

/**
 * 初始化数据库设置
 * @param $db string
 * @param $arrOverride array 覆盖项
 * */
function initDb($db='local',$arrOverride=[]){
    $confArr=Factory::getDbConfig()->load($db);
    $confArr=array_merge($confArr,$arrOverride);
    \ORM::configure($confArr);
    \Model::$auto_prefix_models="";

    //存放备用数据库配置
    $dbConArr=Factory::getDbConfig()->loadAll();
    foreach ($dbConArr as $k=>$v){
        \ORM::configure($v,null,$k);
    }
}