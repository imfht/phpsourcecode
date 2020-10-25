<?php
/**
 * Created by PhpStorm.
 * User: Yuri2
 * Date: 2016/11/29
 * Time: 22:14
 */

namespace naples\lib;


use naples\lib\base\Service;

/**
 * [关键]
 * 路由处理
 */
class Route extends Service
{
    /**
     * 从url获取此次访问的子目录
     * @return string
     */
    public function getPathFix(){
        if (self::isCached('getPathFix')){
            return self::useCache('getPathFix');
        }else{
            $naples_path_fix=$_SERVER['SCRIPT_FILENAME'];
            $naples_path_fix=preg_replace('/\/index.php$/i','',$naples_path_fix);
            $naples_path_fix=\Yuri2::strReplaceOnce($_SERVER['DOCUMENT_ROOT'],'',$naples_path_fix);
            self::useCache('getPathFix',$naples_path_fix);
            if (!defined('URL_PUBLIC')){
                define('URL_PUBLIC',\Yuri2::getHttpType().'://'.$this->getHost().$naples_path_fix);
                define('URL_UE_UPLOAD',URL_PUBLIC.'/html/ueditor/upload');
                define('URL_HTML',URL_PUBLIC.'/html/htmlPageRes');
            }
            return $naples_path_fix;
        }
    }

    /**
     * 获得主机名
     * @return string
     */
    private function getHost(){
        if (isset($_SERVER['HTTP_HOST'])){
            $host=$_SERVER['HTTP_HOST'];
        }else{
            $host=$_SERVER['SERVER_NAME'].($_SERVER["SERVER_PORT"]==80?'':':'.$_SERVER["SERVER_PORT"]);
        }
        return $host;
    }
    
    /**
     * 从url获取此次访问的res
     * @return string 形如 a/b/c[/d?e=xx.....]
     */
    public function getRes(){
        if (self::isCached('getRes')){
            return self::useCache('getRes');
        }else{
            switch (config('url_mode')){
                case '1':
                    //兼容模式
                    if(isset($_GET["U"])){
                        $fileName=$_GET["U"];}
                    else{return false;}
                    break;
                case '2':
                    //pathinfo模式
                    $path_info=empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO'];
                    $orig_path_info=empty($_SERVER['ORIG_PATH_INFO']) ? '' : $_SERVER['ORIG_PATH_INFO'];
                    $pathinfo = $path_info ? $path_info : $orig_path_info; //获取index.php/action=login 这样的参数
                    $fileName=$pathinfo;
                    break;
                case '3':
                    //rewrite模式
                    if(isset($_SERVER['REDIRECT_URL'])){
                        $fileName=$_SERVER['REDIRECT_URL'];}
                    else{$fileName='';}
                    break;
                case '4':
                    //贪婪模式
                    if(isset($_SERVER['REDIRECT_URL'])){
                        $fileName=$_SERVER['REDIRECT_URL'];
                        config('url_mode',3);
                    }
                    elseif(isset($_SERVER['PATH_INFO']) or isset($_SERVER['ORIG_PATH_INFO'])){
                        //pathinfo模式
                        $path_info=empty($_SERVER['PATH_INFO']) ? '' : $_SERVER['PATH_INFO'];
                        $orig_path_info=empty($_SERVER['ORIG_PATH_INFO']) ? '' : $_SERVER['ORIG_PATH_INFO'];
                        $pathinfo = $path_info ? $path_info : $orig_path_info; //获取index.php/action=login 这样的参数
                        $fileName=$pathinfo;
                        config('url_mode',2);
                    }
                    elseif(isset($_GET["U"])){
                        $fileName=$_GET["U"];
                        config('url_mode',1);
                    }else{
                        $fileName='';
                        config('url_mode',1);
                    }
                    break;
                default:
                    return false;
            }
            $path_fix_reg='/^'.str_replace('/','\/',$this->getPathFix()).'/';
            $fileName=preg_replace($path_fix_reg,'',$fileName);
            $fileName=preg_replace('/\.'.config('mask_suffix')."$/i",'',$fileName);
            $nameArr=\Yuri2::explodeWithoutNull($fileName,'?');
            $fileName=array_shift($nameArr);
            if (config('single_module')){
                $fileName=config('default_module').'/'.$fileName;//单模块处理
            }
            $fileName=$this->readAlias($fileName);
            $fileArr=\Yuri2::explodeWithoutNull($fileName,'/');
            switch (count($fileArr)){
                case 0:
                    $fileName='/'.config('default_module').'/Index/index';
                    break;
                case 1:
                    $fileName="/".$fileArr[0]."/Index/index";
                    break;
                case 2:
                    $fileName="/".implode('/',$fileArr)."/index";
                    break;
                default:
                    $fileName="/".implode('/',$fileArr);
                    break;
            }
            self::useCache('getRes',$fileName);
            return $fileName;
        }
    }
    
    /**
     * 生成一个naplesUrl
     * @param $url string RES
     * @param $params array getArr
     * @return string
     */
    public function url($url='',$params=[]){
        //如果留空，返回当前访问地址
        if (!$url){
            if (self::isCached('url_self')){
                return self::useCache('url_self');
            }
            else{
                $url=\Yuri2::getHttpType().'://'.$this->getHost().$_SERVER["REQUEST_URI"];
                self::useCache('url_self',$url);
                return $url;
            }

        }

        //如果是http开头  说明是外链 将params看作get参数
        if (preg_match('/^http/',$url)){
            if (count($params)>0){
                $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                $url .= (strpos($url, '?') ? '&' : '?') . $query;
            }
            return $url;
        }

        //处理 url
        $consRel=$this->consAlias($url,$params);
        if (is_array($consRel)){
            $url=$consRel[0];
            $params=$consRel[1];
        }else{
            $url=$consRel;
            $params=[];
        }

        if ($url{0}!='/' and $url{0}!='\\'){$url='/'.$url;}

        //定义处理函数
        $funcs=[
            function($url,$params){
                //兼容模式
                $url=URL_PUBLIC.'/?U='.$url.'&';
                foreach ($params as $k => $v){
                    $value=urlencode($v);
                    $url.=$k.'='.$value.'&';
                }
                $url=rtrim($url,'&');
                return $url;
            } ,
            function($url,$params){
                //pathinfo模式
                $url=URL_PUBLIC.'/index.php'.$url;
                if (count($params)>0 and !strstr($url,'?')){$url.='?';}
                foreach ($params as $k => $v){
                    $value=urlencode($v);
                    $url.=$k.'='.$value.'&';
                }
                $url=rtrim($url,'&');
                return $url;
            } ,
            function($url,$params){
                //rewrite模式
                $url.='.'.config('mask_suffix');
                $url=URL_PUBLIC.$url;
                if (count($params)>0 and !strstr($url,'?')){$url.='?';}
                foreach ($params as $k => $v){
                    $value=urlencode($v);
                    $url.=$k.'='.$value.'&';
                }
                $url=rtrim($url,'&');
                return $url;
            }
        ];

        switch (config('url_mode')){
            case '1':
                //兼容模式
                $url=$funcs[0]($url,$params);
                return $url;
            case '2':
                //pathinfo模式
                $url=$funcs[1]($url,$params);
                return $url;
            case '3':
                //rewrite模式
                $url=$funcs[2]($url,$params);
                return $url;
            default :
                return false;
        }
    }

    /**
     * 解读res
     * @param $res
     * @return array controllerName,actionName,moduleName,urlParam
     */
    public function getResInfo($res=FLAG_NOT_SET){
        if (isFlagNotSet($res)){
            $res=$this->getRes();
        }
        if (isFlagNotSet(cache('Route_getResInfo_'.$res))){
            $arr=\Yuri2::explodeWithoutNull($res,'/');
            $moduleName=array_shift($arr);
            $controllerName=array_shift($arr);
            $actionName=array_shift($arr);
            $urlParam=$arr;
            $rel= [
                'controllerName'=>ucfirst($controllerName),
                'actionName'=>$actionName,
                'moduleName'=>ucfirst($moduleName),
                'urlParam'=>$urlParam,
            ];
            cache('Route_getResInfo_'.$res,$rel);
            return $rel;
        }else{
            return cache('Route_getResInfo_'.$res);
        }
    }

    /**
     * 解读路由别名
     * @param $fileName string
     * @return string 转换后的结果
     */
    private function readAlias($fileName){
        $rules=$this->config('alias');
        $md5=md5(filemtime(PATH_NAPLES.DS.'configs/route.php'));
        if (hasCache("Route_readAlias_{$md5}_".$fileName)){
            return cache("Route_readAlias_{$md5}_".$fileName);
        }
        foreach ($rules as $rule=>$func){
            //构建正则
            $preg='/'.str_replace('/','\/',$rule).'/';
            $isMatch=preg_match($preg,$fileName,$matches);
            if ($isMatch){
                $rel=call_user_func_array($func,$matches);
                if ($rel){
                    cache("Route_readAlias_{$md5}_".$fileName,$rel);
                    return $rel;
                }
            }
        }
        cache("Route_readAlias_{$md5}_".$fileName,$fileName);
        return $fileName;
    }

    /**
     * 生成路由别名
     * @param $url string 地址
     * @param $params array 路由参数
     * @return array 转换后的结果
     */
    private function consAlias($url,$params){
        $rules=$this->config('reverse');
        foreach ($rules as $rule=>$func){
            //构建正则
            $preg='/'.str_replace('/','\/',$rule).'/';
            $isMatch=preg_match($preg,$url,$matches);
            if ($isMatch){
                $rel=call_user_func_array($func,[$matches,$params]);
                if ($rel){
                    return $rel;
                }
            }
        }
        return [$url,$params];
    }
}