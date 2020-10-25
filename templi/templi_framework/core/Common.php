<?php
/**
 * TempLi 共共函数库 常用函数
 * @author 七觞酒
 * @email 739800600@qq.com
 * @date  2013-1-20
 */
namespace framework\core;
use Templi;

/**
 * Class Common
 * @package framework\core
 */
class Common
{
    /**
     * 返回经addslashes处理过的字符串或数组
     * @param string|array  $data
     * @return array|string
     * @rerurn string|array
     */
    public static function addslashes($data){
        if(!is_array($data)){
            return addslashes($data);
        }else{
            foreach($data as $k=>$v){
               $data[$k] = self::addslashes($v);
            }
            return $data;
        }
    }

    /**
     * 返回经stripslashes处理过的字符串或数组
     * @param string|array  $data
     * @return array|string
     * @rerurn string or array
     */
    public static function stripslashes($data){
        if(!is_array($data)){
            return stripslashes($data);
        }else{
            foreach($data as $k=>$v){
                $data[$k]= self::stripslashes($v);
            }
            return $data;
        }
    }
    /**
     * 返回经htmlspecialchars处理过的字符串或数组
     * @param array|string $data 需要处理的字符串或数组
     * @return mixed
     */
    public static function htmlSpecialChars($data) {
        if(!is_array($data)) {
            return htmlspecialchars($data);
        }
        foreach($data as $k => $v){
            $data[$k] = self::htmlSpecialChars($v);
        }
        return $data;
    }
    /**
     * 返回经htmlspecialchars_decode处理过的字符串或数组
     * @param array|string $data 需要处理的字符串或数组
     * @return mixed
     */
    public static function htmlSpecialCharsDecode($data) {
        if(!is_array($data)){
            return htmlspecialchars_decode($data);
        }
        foreach($data as $k => $v){
            $data[$k] = self::htmlSpecialCharsDecode($v);
        }
        return $data;
    }
    /**
     * 安全过滤函数
     *
     * @param $string
     * @return string
     */
    public static function safeReplace($string) {
        $string =str_replace(
            array(0=>'%20',1=>'%27',2=>'%2527',3=>'*',4=>'\'',5=>'"',6=>';',7=>'<',8=>'>',9=>'{',10=>'}',11=>'\\'),
            array(0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'&lt;',8=>'&gt;',9=>'',10=>'',11=>''),
            $string
        );
        return $string;
    }
    /**
     * 错误输出
     */
    public static function halt(array $error){
        include TEMPLI_PATH . 'tpl/halt.html';
        die;
    }
    /**
     * 404输出
     */
    public static function show404($url='')
    {
        if(!$url){
            $url =  Templi::getApp()->getConfig('404_url');
        }
        if($url){
            self::redirect($url);
        }elseif(function_exists('send_http_status')){
            send_http_status(404);
        }
        die;
    }
    /**
    * url 重定向
     * @param string $url
     * @param int $time
     * @param string $msg
    */
    public static function redirect($url, $time=0, $msg='') {
        //多行URL地址支持
        $url = str_replace(array("\n", "\r"), '', $url);
        if (empty($msg))
            $msg = "系统将在{$time}秒之后自动跳转到{$url}！";
        if(!headers_sent()){
            if($time){
                header('"refresh:{$time};url={$url}"');
                echo $msg;
            }else{
                header('Location: ' . $url);
            }
        }else{
            $str ="<meta http-equiv='Refresh' content='{$time};URL={$url}'>";
            if ($time != 0)
                $str .= $msg;
            exit($str);
        }
    }
    /**
     * url js 跳转
     * @param $url
     */
    public static function urlSkip($url){
        die('<script type="text/javascript">window.location.href="'.$url.'"</script>');
    }
    /**
     * 获取当前页面完整URL地址
     */
    public static function getUrl() {
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $php_self = $_SERVER['PHP_SELF'] ? self::safeReplace($_SERVER['PHP_SELF']) : self::safeReplace($_SERVER['SCRIPT_NAME']);
        $path_info = isset($_SERVER['PATH_INFO']) ? self::safeReplace($_SERVER['PATH_INFO']) : '';
        $relate_url = isset($_SERVER['REQUEST_URI']) ? self::safeReplace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.self::safeReplace($_SERVER['QUERY_STRING']) : $path_info);
        return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
    }

    /**
     * 生成url 如果不穿 参数 则 返回当前页面url
     * @param string $main_params =$module/$controller/$action;
     * @param array $params
     * @param null $url_model
     * @return string
     * @internal param array $param get 参数 array('id'=>3)
     * @example url('home/member/index',array('id'=>28)));
     */
    public static function url($main_params = NULL, $params = array(), $url_model = NULL){
        $main_params = explode('/',$main_params);
        if(is_null($main_params) && is_null($params)){
            //没有参数返回当前页url
            return self::getUrl();
        }
        $url_model = $url_model?$url_model: Templi::getApp()->getConfig('url_model');
        //$url_model = 2;
        $params =   array_map('trim',$params);
        $str   =   rtrim(APP_URL,'/').'/';
        //http://www.TempLi.com/m/c-a-id-5.html
        if($url_model ==1){
            $str .=$main_params[0].'/'.$main_params[1].'-'.$main_params[2];
            foreach($params as $key=>$val){
               $str .= '-'.$key.'-'.$val;
            }
            $str .='.html';
        }elseif($url_model ==2){
            $str .=$main_params[0].'/'.$main_params[1].'-'.$main_params[2];
            foreach($params as $key=>$val){
               $str .= '-'.$val;
            }
            $str .='.html';
        }else{
            $str .='index.php?m='.$main_params[0].'&c='.$main_params[1].'&a='.$main_params[2];
            $str .= $params?'&'.http_build_query($params):'';
        }
        return $str;
    }
    /**
    * 转换字节数为其他单位
    *
    *
    * @param	string	$fileSize	字节大小
    * @return	string	返回大小
    */
    public static function sizeCount($fileSize) {
        if ($fileSize >= 1073741824) {
            $fileSize = round($fileSize / 1073741824 * 100) / 100 .' GB';
        } elseif ($fileSize >= 1048576) {
            $fileSize = round($fileSize / 1048576 * 100) / 100 .' MB';
        } elseif($fileSize >= 1024) {
            $fileSize = round($fileSize / 1024 * 100) / 100 . ' KB';
        } else {
            $fileSize = $fileSize.' Bytes';
        }
        return $fileSize;
    }


    /**
     * 程序执行时间 时间戳
     *
     * @return	int
     */
    public static function getCostTime() {
        $microTime = microtime ( TRUE );
        return $microTime - SYS_START_TIME;
    }
    /**
     * 程序执行时间
     *
     * @return	int	单位ms
     */
    public static function executeTime() {
        $sTime = explode ( ' ', SYS_START_TIME );
        $eTime = explode ( ' ', microtime () );
        return number_format ( ($eTime [1] + $eTime [0] - $sTime [1] - $sTime [0]), 6 );
    }

    /**
     * 将 json 字符串装换为数组
     * @param $jsonStr
     * @return mixed
     */
    public static function jsonDecode($jsonStr)
    {
        return json_decode($jsonStr, true);
    }
}