<?php
/**
 * iPHP - i PHP Framework
 * Copyright (c) iiiPHP.com. All rights reserved.
 *
 * @author iPHPDev <master@iiiphp.com>
 * @website http://www.iiiphp.com
 * @license http://www.iiiphp.com/license
 * @version 2.1.0
 */
class iURL {
    const PAGE_SIGN = '{P}';

    public static $config   = array();
    public static $callback = array();
    public static $data     = array();

    protected static $ARRAY = null;

    public static function init($config=array(),$_config=array()){
        self::$config   = array_merge($config,$_config);
        self::$callback = array_merge((array)self::$config['callback'],self::$callback);
    }

    public static function router($key, $var = null) {
        $routerArray = self::$config['config'];
        $routerKey   = $key;
        is_array($key) && $routerKey = $key[0];
        $router = $routerArray[$routerKey];
        $rewrite = iPHP_ROUTER_REWRITE;
        if(self::$callback['router']['rewrite']){
            $rewrite = self::$callback['router']['rewrite'];
        }
        $url = $rewrite?$router[0]:$router[1];

        if ($rewrite && stripos($routerKey, 'uid:') === 0) {
            $url = rtrim(self::$config['user_url'], '/') . $url;
        }

        if (is_array($key)) {
            if (is_array($key[1])) {
                /* 多个{} 例:/{uid}/{cid}/ */
                preg_match_all('/\{(\w+)\}/i', $url, $matches);
                $url = str_replace($matches[0], $key[1], $url);
            } else {
                $url = preg_replace('/\{\w+\}/i', $key[1], $url);
            }
            $key[2] && $url = $key[2] . $url;
        }

        if ($var == '?&') {
            $url .= $rewrite ? '?' : '&';
        }
        if(!$rewrite){
            $url = self::$config['api_url'].'/'.$url;
        }else{
            if(!iFS::checkHttp($url)){
                $url = rtrim(self::$config['url'],'/').$url;
            }
        }
        if(self::$callback['router']['data']){
            call_user_func_array(self::$callback['router']['data'], array(&$url));
        }
        return $url;
    }
    public static function Hashids($salt='',$len=8) {
        empty($len) && $len = 8;
        self::$config['hash']['len'] && $len = self::$config['hash']['len'];
        self::$config['hash']['salt']&& $salt = self::$config['hash']['salt'];
        return iPHP::vendor('Hashids',array("salt"=>$salt,"len"=>$len));
    }
    public static function set_app_data($app,$data) {
        self::$data[$app] = $data;
    }
    public static function rule($matches) {
    	$rule = $matches[1];
        $_time = 0;
        if(strpos($rule,'AUTHID:')!==false||strpos($rule,'AUTHCID:')!==false){
            list($rule,$_time) = explode(':', $rule);
        }

        list($a,$c,$tc) = self::$ARRAY;

        //{BOOK:ID}
        if(strpos($rule,':')!==false){
            list($app,$rule) = explode(':', $rule);
            $app = strtolower($app);
            self::$data[$app] && $a = self::$data[$app];
        }
        //兼容
        $rule =='0x3ID'   && $rule = '0xID,0,3';
        $rule =='0x3,2ID' && $rule = '0xID,3,2';
        $is_substr = false;
        if(strpos($rule,',')!==false){
            //{@random,8}
            if($rule[0]=='@'){
                $_rule = substr($rule, 1);
                $rule  = '@';
            }else{
                //{0xID,3,2}
                list($rule,$start,$len) = explode(',', $rule);
                $substr_func = function($e,$start=0,$len=0){
                    if($len===null){
                        $len   = $start;
                        $start = 0;
                    }
                    return substr($e, $start, $len);
                };
            }
        }
        if(strpos($rule,'Hash@')!==false){
            list($si,$rule,$len,$salt) = explode('@', $rule);
            $Hashids = self::Hashids($salt,$len);
            $rule =="ID"    && $id = $a['id'];
            $rule =="0xID"  && $id = sprintf("%08s",$a['id']);
            $rule =="CID"   && $id = $a['cid'];
            $rule =="0xCID" && $id = sprintf("%08s",$c['cid']);
            return $e = $Hashids->encode($id);
        }

        switch($rule) {
            case 'ID':      $e = $a['id'];break;
            case '0xID':	$e = sprintf("%08s",$a['id']);break;
            case 'AUTHID':  $e = rawurlencode(auth_encode($a['id'],$_time));break;
            case 'MD5':     $e = substr(md5($a['id']),8,16);break;
            case 'TMD5':    $e = substr(md5(time().uniqid()),8,16);break;

            case 'CID':     $e = $c['cid'];break;
            case 'CMD5':    $e = substr(md5($c['cid']),8,16);break;
            case '0xCID':   $e = sprintf("%08s",$c['cid']);break;
            case 'AUTHCID': $e = rawurlencode(auth_encode($a['cid'],$_time));break;
            case 'CDIR':    $e = $c['dir'];break;
            case 'CDIRS':   $e = $c['dirs'];break;

            case 'TIME':	$e = $a['pubdate'];break;
            case 'YY':		$e = get_date($a['pubdate'],'y');break;
            case 'YYYY':	$e = get_date($a['pubdate'],'Y');break;
            case 'M':		$e = get_date($a['pubdate'],'n');break;
            case 'MM':		$e = get_date($a['pubdate'],'m');break;
            case 'D':		$e = get_date($a['pubdate'],'j');break;
            case 'DD':		$e = get_date($a['pubdate'],'d');break;

            case 'NAME':    $e = rawurlencode($a['name']);break;
            case 'TITLE':   $e = rawurlencode($a['title']);break;
            case 'ZH_CN':	$e = ($a['name']?$a['name']:$a['title']);break;
            case 'TKEY':    $e = $a['tkey'];break;
            case 'LINK':    $e = $a['clink'];break;

            case 'TCID':	$e = $tc['tcid'];break;
            case 'TCDIR':	$e = $tc['dir'];break;

            case 'EXT':		$e = self::$config['ext'];break;
            case 'P':       $e = self::PAGE_SIGN;break;
            case '@':
                $args = explode(',', $_rule);
                if (in_array($args[0],array('random'))) {
                    return call_user_func_array($args[0], array_slice($args,1));
                }
            break;
            default:
                $key = strtolower($rule);
                $a[$key] && $e = $a[$key];
        }

        is_callable($substr_func) && $e = $substr_func($e, $start, $len);

        return $e;
    }
    public static function rule_data($C,$key) {
        if(empty($C['mode'])||$C['password']){
            return '{PHP}';
        }else{
            is_object($C['rule']) && $C['rule'] = (array)$C['rule'];
            is_array($C['rule'])  OR $C['rule'] = json_decode($C['rule'],true);
            $rule = $C['rule'][$key];
            // $rule OR $rule = $key;
            return $rule;
        }
    }
    public static function get($route,$a=array(),$type=null) {
        $i        = new stdClass();
        $default  = array();
        $category = array();
        $array    = (array)$a;

        $app = $route;
        if(strpos($route,':')!==false) list($app,$do) = explode(':', $route);

        $app_conf = self::$config['iurl'][$app];
        $type === null && $type = $app_conf['rule'];

        switch($type) {
            case '0':
                $i->href = $array['url'];
                $url     = $array['rule'];
            break;
            case '1'://分类
                $category = $array;
                $i->href  = $category['url'];
                $url      = self::rule_data($category,'index');
                $purl     = self::rule_data($category,'list');
                empty($purl) && $purl = rtrim($url,'/').'/index_{P}{EXT}';
            break;
            case '2'://内容
                $array    = (array)$a[0];
                $category = (array)$a[1];
                $i->href  = $array['url'];
                $url      = self::rule_data($category,$route);
            break;
            case '3'://标签
                $array     = (array)$a[0];
                $category  = (array)$a[1];
                $_category = (array)$a[2];
                $i->href   = $array['url'];
                $category && $url = self::rule_data($category,$app);
                if($_category['rule'][$app]){
                    $url = self::rule_data($_category,$app);
                }
            break;
            case '4'://自定义
                $array    = (array)$a[0];
                $category = (array)$a[1];
                $i->href  = $array['url'];
                $url      = self::rule_data($category,$route);
                $href     = 'index.php?app='.$app;
            break;
            default:
                $url  = '{PHP}';
                $href = 'index.php?app='.$app;
            break;
        }
        if(empty($url) && $array['rule']){
            $url = $array['rule'];
        }

        $default  = self::$config[$app];
        if($default){
            $router_dir = $default['dir'];
            $router_url = $default['url'];
            empty($url) && $url = $default['rule'];
        }
        empty($router_url) && $router_url = self::$config['url'];
        empty($router_dir) && $router_dir = self::$config['dir'];

        if(strpos($router_url,'*')!==false) {
            $router_url = str_replace('*', random(6), $router_url);
        }
        //[xxxxx]类自定链接优先
        if($array['clink']){
            preg_match('/\[(.+)\]/', $array['clink'], $match);
            isset($match[1]) && $url = $match[1];
        }
        if(self::$callback['url']['rule']){
            $url = self::$callback['url']['rule'];
        }
        if($url=='{PHP}'){
            $primary = $app_conf['primary'];
            empty($href) && $href = $app.'.php';
            $query = array();
            $do && $query['do']= $do;
            $primary && $query[$primary]= $array[$primary];
            $href = self::make($query,$href);
            if($app_conf['page']){
                $i->pageurl = self::make(array($app_conf['page']=>self::PAGE_SIGN),$href);;
                iFS::checkHttp($i->pageurl) OR $i->pageurl = rtrim($router_url,'/').'/'.$i->pageurl;
            }
            iFS::checkHttp($href) OR $href = rtrim($router_url,'/').'/'.$href;
            $i->href = $href;
        }else if(strpos($url,'{PHP}')===false) {
        	self::$ARRAY = array($array,$category,$_category);

            $category['htmlext'] && self::$config['ext'] = $category['htmlext'];

            $i = self::build($url,$router_dir,$router_url);

            if(strpos($i->href,self::PAGE_SIGN)!==false) {
                $purl = $i->href;
            }

            self::page_sign($i);

            if($purl){
                $ii = self::build($purl,$router_dir,$router_url);
                $i->pageurl  = $ii->href;
                $i->pagepath = $ii->path;
                unset($ii);
            }else{
                $pfile = $i->file;
                if(strpos($pfile,self::PAGE_SIGN)===false) {
                    $pfile = $i->name.'_'.self::PAGE_SIGN.$i->ext;
                }
                $i->pageurl  = $i->hdir.'/'.$pfile ;
                $i->pagepath = $i->dir.'/'.$pfile;
            }
            // call_user_func_array(self::$callback, array($app,$i,self::$ARRAY,$app_conf));
        }
        if($category['cid'] && self::$callback['domain']){
            $i = call_user_func_array(self::$callback['domain'], array($i,$category['cid'],$router_url));
        }
        if(self::$callback['device']){
            $d = call_user_func_array(self::$callback['device'], array($i));
            $i = (object)array_merge((array)$i,$d);
        }
        if(self::$callback['url']['data']){
            call_user_func_array(self::$callback['url']['data'], array(&$i));
        }
        $i->url = $i->href;
        return $i;
    }

    public static function build($url,$_dir,$_host=null,$_ext=null) {
        if(strpos($url,'{')!==false && strpos($url,'}')!==false){
            $url = preg_replace_callback("/\{(.*?)\}/",array(__CLASS__,'rule'),$url);
        }

        $i = new stdClass();
        $i->href = $url;
        if(strpos($_dir,'..')===false) {
            $i->href = $_dir.$url;
        }
        $i->href = ltrim(iFS::path($i->href),'/');
        $i->path = rtrim(iFS::path(iPATH.$_dir.$url),'/') ;

        if(iHttp::is_url($i->href)===false){
            $i->href = rtrim($_host,'/').'/'.$i->href;
        }
        $pathA = pathinfo($i->path);
        $i->hdir = pathinfo($i->href,PATHINFO_DIRNAME);
        $i->dir  = $pathA['dirname'];
        $i->file = $pathA['basename'];
        $i->name = $pathA['filename'];
        $i->ext  = '.'.$pathA['extension'];
        $i->name OR $i->name = $i->file;

        if(empty($i->file)||substr($url,-1)=='/'||empty($pathA['extension'])) {
            $i->name = 'index';
            $i->ext  = self::$config['ext'];
            $_ext && $i->ext = $_ext;
            $i->file = $i->name.$i->ext;
            $i->path = $i->path.'/'.$i->file;
            $i->dir  = dirname($i->path);
            $i->hdir = dirname($i->href.'/'.$i->file);
        }

        return $i;
    }
    public static function page_sign(&$i) {
        // $i->pfile = $i->file;
        // if(strpos($i->file,self::PAGE_SIGN)===false) {
        //     $i->pfile = $i->name.'_'.self::PAGE_SIGN.$i->ext;
        // }
        // $i->pageurl  = $i->hdir.'/'.$i->pfile ;
        // $i->pagepath = $i->dir.'/'.$i->pfile;
        $i->href = str_replace(self::PAGE_SIGN,1,$i->href);
        $i->path = str_replace(self::PAGE_SIGN,1,$i->path);
        $i->file = str_replace(self::PAGE_SIGN,1,$i->file);
        $i->name = str_replace(self::PAGE_SIGN,1,$i->name);
    }
    public static function page_num($path, $page = false) {
        $page === false && $page = $GLOBALS['page'];
        if ($page < 2) {
            return str_replace(array('_'.self::PAGE_SIGN, '&p='.self::PAGE_SIGN), '', $path);
        }
        return str_replace(self::PAGE_SIGN, $page, $path);
    }
    public static function page_url($iurl){
        return iPagination::url($iurl);
    }
    public static function make($QS=null,$url=null) {
        $url OR $url = $_SERVER["REQUEST_URI"];
        if(strpos($url,'router::')!==false) {
            $rkey = substr($url, 8);
            $url  = iURL::router($rkey);
        }
        $parse  = parse_url($url);
        parse_str($parse['query'], $query);
        is_array($QS) OR $QS = parse_url_qs($QS);
        foreach ($QS as $key => $value) {
            //这个null是字符
            if(strtolower($value)==='null'||$value===null){
                unset($QS[$key]);
                unset($query[$key]);
            }
        }
        $query = array_merge((array)$query,(array)$QS);
        $parse['query'] = http_build_query($query);

        $PAGE_SIGN = urlencode(self::PAGE_SIGN);
        if(strpos($parse['query'],$PAGE_SIGN)!==false) {
            $parse['query'] = str_replace($PAGE_SIGN,self::PAGE_SIGN, $parse['query']);
        }
        // if(strpos($parse['path'],'.php')===false) {
        //     $path = '';
        //     foreach ($query as $key => $value) {
        //         $path.= $key.'-'.$value;
        //     }
        //     $parse['path'].= $path.self::$config['ext'];
        // }
        $nurl = self::glue($parse);
        return $nurl?$nurl:$url;
    }
    public static function glue($parsed) {
        if (!is_array($parsed)) return false;

        $uri = isset($parsed['scheme']) ? $parsed['scheme'].':'.((strtolower($parsed['scheme']) == 'mailto') ? '':'//'): '';
        $uri.= isset($parsed['user']) ? $parsed['user'].($parsed['pass']? ':'.$parsed['pass']:'').'@':'';
        $parsed['host']    && $uri.= $parsed['host'];
        $parsed['port']    && $uri.= ':'.$parsed['port'];
        $parsed['path']    && $uri.= $parsed['path'];
        $parsed['query']   && $uri.= '?'.$parsed['query'];
        $parsed['fragment']&& $uri.= '#'.$parsed['fragment'];
        return $uri;
    }
    public static function URI($qs=null,$url=null){
        $url===null && $url = $_SERVER["REQUEST_URI"];
        $arr = parse_url($url);
        $arr["query"] = self::merge_query($arr["query"],$qs,true);
        return self::glue($arr);
    }
    public static function merge_query($q1=null,$q2=null,$build=false){
        is_string($q1) && $q1 = parse_url_qs($q1);
        is_string($q2) && $q2 = parse_url_qs($q2);
        $query = array_merge($q1,$q2);
        return $build?http_build_query($query):$query;
    }
}
