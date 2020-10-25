<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//  获取用户信息- 2016年9月20日 星期二
use think\Config;
use think\Loader;

// 加载extra中得配置文件
Config::load(APP_PATH.'extra/config.php');
function getUser($key=null){
    $data = isset($_SESSION['login'])? $_SESSION['login']:array();
    if($key){
        if(array_key_exists($key,$data)) return $data[$key];
        return '';
    }
    return $data;
}
//  输出调试信息 $print == die 时中断程序 -2016年9月20日 星期二
function debugOut($data=null,$print=false){
    $str = '';
    if(is_object($data) || is_array($data)){
        $data = is_object($data)? (array)$data:$data;
        $str = print_r($data,true);
    }elseif(empty($data)) $str = '';
    else $str = $data;
    if($print){
        header('content-type:text/html;charset=utf-8;');
        echo '<h4>('.date('Y-m-d H:i:s').')调试信息：</h4><pre>'.$str.'</pre>';
        if( !is_bool($print) && 'die' == $print) die;
        return;
    }
    $conf = brximl_conf();
    $dir = ROOT_PATH.'/'.$conf['debug_dir'];
    if(!is_dir($dir)) mkdir($dir);
    $file = $dir.'debug'.date('Y-m-d').'.log';
    $str = "\r\n--------------|| ".date('Y-m-d H:i:s')." ||------------->\r\n".$str;
    if(!is_file($file)) file_put_contents($file,$str);
    else{
        $fh = fopen($file,'a');
        fwrite($fh,$str);
        fclose($fh);
    }
}
// 不定参数- 打印
function println(){
    $numargs = func_num_args();
    $args = func_get_args();
    // 回调函数
    if($numargs>1 && is_callable($args[$numargs-1])){
        array_pop($args);
        $args[$numargs-1]($args);
    }
    // 输出中断
    elseif($numargs > 1 && is_string($args[$numargs-1]) && in_array($args[$numargs-1],['die','DIE'])){
        array_pop($args);
        if(count($args) == 1) debugOut($args[0],true);
        else debugOut($args,true);
        die;
    }
    elseif($numargs == 1) debugOut($args[0],true);
    elseif($numargs>0) debugOut($args,true);
}
// $month[int/string]
function sysdate($type=null,$month=null){
	date_default_timezone_set('PRC');
    if(is_string($type)) $type = strtolower($type);
	switch($type){
		case 'date':
			$time = date('Y-m-d');break;
		case 'time':
			$time = date('H:i:s');break;
		case 'first':// 当月第一天
			if(empty($month)) $time = date('Y-m-01');
			else{				
				$time = is_numeric($month)? date('Y-').$month.'-01':$month;
				//$time = date_create($time)->format('Y-m-d H:i:s');
				$time = date_format(date_create($time),'Y-m-01');
			}
			break;
		case 'last':// 当月最后一天
			$month = $month? $month:intval(date('n'));
			if($month == 12) $d = (intval(date('Y'))+1).'-01-01';
			elseif(is_numeric($month)) $d = date('Y').'-'.($month+1).'-01';
			else{// 输入日期字符串
				$date = date_create($month);
				$y = date_format($date,'Y');
				$m = date_format($date,'m');
				if($m == 12) $d = (intval($y)+1).'-01-01';
				else $d = $y.'-'.(intval($m)+1).'-01';
			}
			//$time = date_create($d)->add(new DateInterval('P10D'))->format('Y-m-d H:i:s');
			$date = date_create($d);
			date_add($date, date_interval_create_from_date_string('-1 days'));
			$time = date_format($date, 'Y-m-d');
			break;
		default:
			$time = date('Y-m-d H:i:s');
	}
	return $time;
}
// 日期相加天
function dateadd($date,$days){
    $dt = date_create($date);
    date_add($dt, date_interval_create_from_date_string($days.' days'));
    return date_format($dt, 'Y-m-d');
}
// 求日期差 - 默认当前年天数
function getDays($dt1=null,$dt2=null)
{
    $dt1 = trim($dt1)? $dt1:null;
    // $createDt2 = function($month){};  
    if($dt1){
        // 输入年月 -> 2018-12
        if(substr_count($dt1,'-') == 1 && strlen($dt1) > 4){
            $dt1 .= '-01';
            if(empty($dt2)){
                $date = new DateTime($dt1);
                $month = $date->format('m');
                if($month < 12) $nextMonth = ($date->format('Y')).'-'.(intval($month)+1).'-01';             
                elseif($month == 12) $nextMonth = (intval($date->format('Y'))+1).'-01-01'; 
                $date2 = date_create($nextMonth);
                date_add($date2, date_interval_create_from_date_string('-1 days'));
                $dt2 = date_format($date2, 'Y-m-d');
            }
        }
        // 输入年份
        elseif(substr_count($dt1,'-') == 0 && strlen($dt1) == 4){
            $year = date('Y',time());
            $dt1 = $year.'-01-01';
            if(empty($dt2)){
                $dt2 = (intval($year)+1).'-01-01';
                $date2 = date_create($dt2);
                date_add($date2, date_interval_create_from_date_string('-1 days'));
                $dt2 = date_format($date2, 'Y-m-d');
            }
        }
        // 输入月份 08/12
        elseif(substr_count($dt1,'-') == 0 && strlen($dt1) <3){
            $year = date('Y',time());
            if(empty($dt2)){
                if($dt1 == '12') $nextMonth = (intval($year)+1).'-01-01';
                else $nextMonth = $year.'-'.(intval($dt1)+1).'-01';
                $date2 = date_create($nextMonth); 
                date_add($date2, date_interval_create_from_date_string('-1 days'));
                $dt2 = date_format($date2, 'Y-m-d');
            }
            $dt1 = $year.'-'.$dt1.'-01';
        }
        // 全日期 - 2018-07-13
        else{
            if(empty($dt2)){
                $date = new DateTime($dt1);
                $month = $date->format('m');
                $year = $date->format('Y');
                if($month == 12) $nextMonth = (intval($year)+1).'-01-01';
                else $nextMonth = $year.'-'.(intval($month)+1).'-01';
                $date2 = date_create($nextMonth);
                date_add($date2, date_interval_create_from_date_string('-1 days'));
                $dt2 = date_format($date2, 'Y-m-d');
            }                        
        }
        return (new DateTime($dt1))->diff(new DateTime($dt2))->format('%a');
    }
    else{
        $year = date('Y',time());    
        $dt1 = $year.'-01-01';     
        // 计算当年终最后一天
        if(empty($dt2)){
            $dt2 = (intval($year)+1).'-01-01';
            $date = date_create($dt2);
            date_add($date, date_interval_create_from_date_string('-1 days'));
            $dt2 = date_format($date, 'Y-m-d');
        }   
        // $dt2 = (new DateTime($dt2))->add(new DateInterval('P1D'))->format('Y-m-d');  // 不知道怎么减
        return (new DateTime($dt1))->diff(new DateTime($dt2))->format('%a');
    }
}
// 用户信息
function uInfo($key=null)
{
    $key = strtolower($key);
    $skey = config('uSessin_key');
    $skey .= date('ymd');
    if($key == 'skey') return $skey;
    $data = isset($_SESSION[$skey])? $_SESSION[$skey]:array();
    //if(is_null($key)) return $data;// bug
    if(empty($key)) return $data;//debugOut([is_null($key),$key,is_null(null),is_null(NULL)],true);var_dump($key);
    if($key){
        if(array_key_exists($key,$data)) return $data[$key];
    }
    return '';
}
// base_json 组合加密
function bsjson($data){
    if(is_array($data)) return base64_encode(json_encode($data));
    else if(is_string($data)) return json_decode(base64_decode($data),true);
    return '';
}
//  私有配置文件 brximl_ 打头为私有函数区别于系统方法
function brximl_conf($key=null){
    $name = 'user';
    Config::load(APP_PATH.'conf.php','',$name);
    //  预处理配置解析常量
    Config::set('dir',str_replace('/app/','',APP_PATH),$name);
    return Config::get($key,$name);
}
// brximl_conf 获取常量的处理 ~ conf.php
function getBrxVar($key=null,$plus=""){
    static $_brxVars = null;
    if(empty($_brxVars)) $_brxVars = brximl_conf();
    if($key){
        $value = array_key_exists($key,$_brxVars)? $_brxVars[$key] : '';
        $value .= $plus;
        return $value;
    }
    return $_brxVars;
}


/*通过解析获取URL左/右邻值*/
function getUrlBind($name=null,$ogri=false,$position='LEFT'){
    //$url = $_SERVER["REQUEST_URI"];
    $url = $_REQUEST["s"];
    $arr = explode('/',$url);
    if(empty($name)) return $arr;
    $key = array_search($name,$arr);
    if(empty($key)) $key = array_search($name.'.html',$arr);
    if(empty($key)) return '';
    $key = $position == 'LEFT'? $key+1:$key-1;
    if(isset($arr[$key])){
        if($ogri) return $arr[$key];
        $tmpArr = explode('.',$arr[$key]);
        return $tmpArr[0]; 
    }
    return '';
}
/* 用户选择器*/
function user_select($opt=null){
    $page = isset($opt['page'])? $opt['page']:1;
    $num = isset($opt['num'])? $opt['num']:20;
    $xhtml = '';
    $user = Loader::model('User');
    $data = $user//->where('user_name,user_code')
            ->field('user_name,user_code')
            ->page($page,$num)
            ->select();
    foreach($data as $v){
        $xhtml .= '<li><a href="javascript:void(0);" code="'.$v['user_code'].'" onclick="TeC.select_user(this);">'.$v['user_name'].'</a></li>';
    }
    if($xhtml){
        $xhtml = '<ul>'.$xhtml.'</ul>';
        $count = $user->count();
        $xhtml .= getPageBar($page,$count,$num,'<<');
    }
    return $xhtml;
}
// 分页器
function getPageBar($page=1,$count,$num=20,$type=null){
    $xhtml = '';
    $all = ceil($count/$num);// 总页数
    if($all == 1) return $xhtml;
    if('<<' == $type){// << < 跳转 > >> 符号分法
        if($page>2) $xhtml .= '<a href="javascript:TeC.pageTo(\'1\')"><<</a>';
        if($page>1) $xhtml .= '<a href="javascript:TeC.pageTo(\''.($page-1).'\')"><</a>';

        if($page<$all) $xhtml .= '<a href="javascript:TeC.pageTo(\''.($page+1).'\')">></a>';
        if($page<$all-1) $xhtml .= '<a href="javascript:TeC.pageTo(\''.$all.'\')">>></a>';
    }else{// 默认十分法 
        $fmod = fmod($page,10);
        $min = $page-$fmod+1;
        $max = $page-$fmod+10;
        if($max>$all) $max = $all;
        if($min>10) $xhtml = '<a href="javascript:TeC.pageTo(\''.($min-1).'\')" class="page_bar_btn"><</a>';
        for($i=$min; $i<=$max; $i++){
            $xhtml .= '<a href="javascript:TeC.pageTo(\''.$i.'\')" class="'.($page == $i? 'page_bar_active':'page_bar_btn').'">'.$i.'</a>';
        }
        if($max<$all) $xhtml = '<a href="javascript:TeC.pageTo(\''.($max+1).'\')" class="page_bar_btn">></a>';
    }
    $xhtml .= '<span class="page_bar_info">'.$page.'/<span class="page_bar_max">'.$all.'</span>,获取到条'.$count.'数据</span>';
    return $xhtml;
}

// 生成跨端口请求地址
function getPortUrl($url,$port=null){
    return 'http://'.request()->ip().(is_numeric($port)? ':'.$port:'').$url;
}
function utf8(){header('content-type:text/html;charset=utf-8;');}
/************** 互联网APIs begin ***********************/
/*
	参考： http://httpbin.org/,http://www.niubb.net/a/2015/05-01/368660.html
*/
function curNet()
{
	//1.获取当前的IP
	$ipArr = json_decode(trim(getStr('http://httpbin.org/ip')),true);
	$url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$ipArr['origin'];/*测试curl方式下最稳定*/
	//$url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js&ip='.$ipArr['origin'];
	//$url = 'http://www.niubb.netsmartresult-xml/search.s?type=ip&q='.$ipArr['origin'];
	//$url = 'http://whois.pconline.com.cn/?ip='.$ipArr['origin'];

	//2.调用淘宝API
	$arr = getStr($url,true);
	if($arr['code'] == 1){//数据获取失败
		$arr = array();
		$iplookup = getStr('http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=js');//未知IP省获取位置
		$arr['data'] = json_decode(str_replace('var remote_ip_info = ','',rtrim(trim($iplookup),';')),true);//var remote_ip_info = {"ret":1,"start":-1,"end":-1,"country":"\u4e2d\u56fd","province":"\u9ed1\u9f99\u6c5f","city":"\u54c8\u5c14\u6ee8","district":"","isp":"","type":"","desc":""};
		$arr['ip'] = $ipArr['origin'];
	}
	$arr['stamp'] = date('Y-m-d H:i:s');//timestamp时间戳
	return $arr;
}
/*函数库
    CURL
*/
function getStr($url,$type=null,$data=null)
{
	if(!extension_loaded('curl')){
		ini_set('curl',true);
	}
	$ch = curl_init();
	curl_setopt ( $ch, CURLOPT_URL, $url );
	if($data){//post 数据
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0');
	}
	//curl_setopt ( $ch, CURLOPT_HEADER, 0 );
	curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );

	$res = curl_exec ( $ch );
	if(empty($res)){
		return curl_error($ch);
	}
	curl_close ($ch);
	$resed = @iconv("", "UTF-8", $res);//PHP_EOL;
	if($type) $resed = json_decode($resed,true);
	if(empty($resed)){//数据处理失败时>> 返回初始结果
		return $res;
	}
	return $resed;
}
// 页面跳转
function go($url){header('location:'.$url);die;}
/*
    页面调转-弥补框架无法调整到不同的模块 $url=> {模块:控制/操作} $param => {__get:k=>v} ?? / {k=>v}
    :           => index:index/index
    :/login     => index:index/login
    !           => 不调整的前缀
    .           => 当前模块 .:index
    +           => 全URL-> http://ip/+
*/    
function urlBuild($url=null,$param=null)
{
    $primary = $url;    
    $suffix = '.html';$default = 'index';
    static $baseUrl = null;         // 静态化处理，减少基础URL的运算次数
    if(empty($baseUrl)){
        $pref = 's=/';
        $requestUrl = isset($_SERVER["REDIRECT_URL"])? $_SERVER['REDIRECT_URL']:$_SERVER['REQUEST_URI'];
        $requestStr = substr_count($_SERVER["QUERY_STRING"],'&') >0? substr($_SERVER["QUERY_STRING"],0,strpos($_SERVER["QUERY_STRING"],'&')):$_SERVER["QUERY_STRING"];
        $baseUrl = str_replace(str_replace($pref,'',$requestStr),'',$requestUrl);
    }
    // println($baseUrl,$requestStr,'die-',$_SERVER["QUERY_STRING"]);
    $signer = '!';$httpSigner = '+';
    $redirect = true;
    if(!empty($url) && substr_count($url,$signer) > 0){
        $url = str_replace($signer,'',$url);
        $redirect = false;
    }
    $httpAdd = (!empty($url) && substr_count($url,$httpSigner) > 0)? true:false;
    if($httpAdd){
        $url = str_replace($httpSigner,'',$url);
        $httpAdd = $_SERVER["REQUEST_SCHEME"].'://'.$_SERVER["SERVER_NAME"].($_SERVER["SERVER_PORT"] == '80'?'':$_SERVER["SERVER_PORT"]);
        // http://localhost/conero/index/test/php
    }
    $url = $url? $url:'';    
    $url = preg_replace('/\s/','',$url);
    // 算法优化
    // 模块名称获取 {module: = module:index/index => ~module.html}    
    if(substr_count($url,':') > 0){
        $module = substr($url,0,strpos($url,':'));
        $module = $module? $module:'index';
        $url = substr($url,strpos($url,':')+1); 
        // println($module,$url);
    }
    // {. = module:index/index => ~module.html}
    elseif(substr_count($url,'.') > 0){
        $module = request()->module();
        $url = substr($url,strpos($url,'.')+1);
    }
    else{
        $module = 'index';
    }
    $isFullUrl = false;
    if(empty($url)) $url = $module;     // ~module.html
    else{
        $url = $module.'/'.$url;
        if(substr_count($url,'/') >0 ) $isFullUrl = true;
    }
    $url = $baseUrl.$url;
    //  println($url);
    if(!empty($param)){
        if(is_array($param)){
            // 非全路径时 默认为 __get
            if($isFullUrl == false && !isset($param['__get'])) $param = ['__get'=>$param];
            if(isset($param['__get'])){
                $tmpArr = [];
                $param = $param['__get'];
                foreach($param as $k=>$v){
                    $tmpArr[] = $k.($v? '='.$v:'');
                }
                if(!empty($tmpArr)) $url .= $suffix.'?'.implode('&',$tmpArr);
                else $url .= $suffix;
                //println($url,implode('&',$tmpArr));
            }
            else{
                $tmpArr = [];
                foreach($param as $k=>$v){
                    $tmpArr[] = $k.(($v == '_' || empty($v))? '':'/'.$v);
                }
                if(!empty($tmpArr)) $url .= '/'.implode('/',$tmpArr);
                $url .= $suffix;
            }
        }
        elseif(is_string($param)){
            if(substr_count($param,'?') > 0){   // __get 字符串
                $url .= (substr_count($url,$suffix)>0? '':$suffix).(substr_count($param,$suffix)>0? str_replace($suffix,'',$param):$param);        
            }
            else{
                if($isFullUrl == false) $url .= $param.$suffix;
                $url .= $default;
            }
        }

    }
    else $url .= $suffix;
    if($redirect){
        go($url);return;
    }
    if($httpAdd) $url = $httpAdd.$url;
    return $url;
}
/*获取网页内容的另外一种方式
*/
function getContent($opt)
{
    $result = '';
    $post = isset($opt['data'])? $opt['data']:null;
    $url = is_string($opt)? $opt:$opt['url'];
    if(empty($url)) return '';
    if($post){// POST-data
        if(!is_array($post)) $post = json_decode(trim($post),true);
        if(is_array($post)){
            $postStr =  http_build_query($post);
            $opts = ['http' =>
                [
                    'method'  => 'POST',
                    'header'  => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postStr
                ]
            ];
            $context  = stream_context_create($opts);
            $result = file_get_contents($url, false, $context);
        }
    }else{
        $result = file_get_contents($url);
    }
    return $result;
}
/*  单字符串提取器=> preg_match
 *  示例： '/Conero/finance/fevent/{:list_no}/other.html' => list_no
 */
 function str_getKey($str,$d1='{:',$d2='}'){
    $start = strpos($str,$d1)+2;
    $len = strpos($str,$d2)-$start;
    $value = substr($str,$start,$len);
    $key = $d1.$value.$d2;
    return [$key=>$value];
 }
 /*  字符串提取器=> preg_match
 *  示例： '/Conero/finance/fevent/{:list_no}/other.html/{:lisa}' => list_no
 */
 function str_engin($str,$pattern=null,$limiter=null){
     $pattern = $pattern? $pattern:'/\{:[a-zA-Z_]*\}/';
     $limiter = $limiter? $limiter:['{:','}'];
     preg_match_all($pattern,$str,$data);
     $data = isset($data[0])? $data[0]:array();
     $retArr = array();
     foreach($data as $v){
         $retArr[$v] = str_replace($limiter[1],'',str_replace($limiter[0],'',$v));
     }
     return $retArr;
 }
 /* 2016年12月10日 星期六 - 文本匹配/ 找到数字
  * 
  */
function textMatchNumber($txt)
{
    try{
        //$pattern = '/[\d]+[-\.\+\=\/#\*,\s\d]+[\d]*+[-\.\+\=\/#\*,\s\d]+[\d]+/';
        $pattern = '/([\d]+)([-\.\+\=\/#\*,\s\d])*[\d\sa-zA-Z\$]+/';
        $data = null;
        preg_match_all($pattern,$txt,$data);
        //debugOut($data);
        $data = isset($data[0])? $data[0]:array();
        foreach($data as $v){
            $txt = str_replace($v,'<ins class="text-primary">'.$v.'</ins>',$txt);
        }   
    }catch(Exception $e){debugOut($e->getTraceAsString());}
    return $txt;
}
// 用户类型描述
function adminDescrp($key=null){
    $tpl = [// 权限一次递增
        'USE' => '普通用户',    // USER        
        'MNG' => '系统管理员',  // MANAGE
        'DEV' => '系统开发者'   // DEVELOPER
    ];
    if(empty($key)) return $tpl;
    if(array_key_exists($key,$tpl)) return $tpl[$key];
    return '';
}
// logic 类调用工具
function ulogic($name){
    $class = $name;
    $file = ROOT_PATH.'app/Server/'.$class.'.php';
    if(is_file($file)) $class = str_replace('/','\\','/app/Server/'.$class);
    if(class_exists($class)) return new $class();
}
// 模式快速提取 测试
function model_feek($md,$callback){
    $md = is_object($md)? $md: model($md);
    return is_callable($callback)? $callback($md) : '';
}
/************** 互联网APIs end ***********************/

/**
 * 多级目录生成器
 * @param $path 生成目录名称
 * @param $isfle 指定$path 参数为文件
 * @return bool
 */
 function mkMutilDirs($path,$isfle=false)
 {
     try{
        static $_mkdirs_cur = null;
        $path = empty($_mkdirs_cur)? str_replace('\\','/',$path) : $path;
        if($isfle) $path = pathinfo($path)['dirname']; 
        if(!is_dir($path)){            
            if(empty($_mkdirs_cur)){
                // 兼容 mkdir 函数
                if(is_dir(dirname($path))){
                    return mkdir($path);
                }
                $_mkdirs_cur = $path;
            }
            mkMutilDirs(dirname($path));
        }
        else{
            if($_mkdirs_cur){
                $firstDir = $_mkdirs_cur;
                $_mkdirs_cur = null;
                $_basedir = $path;
                $firstDir = str_replace($_basedir,'',$firstDir);
                if(strpos($firstDir,'/') === 0) $firstDir = substr($firstDir,1);
                foreach(explode('/',$firstDir) as $v){
                    if(!is_dir($_basedir.'/'.$v)){
                        mkdir($_basedir.'/'.$v);
                        $_basedir = $_basedir.'/'.$v;
                    }
                }
            }
        }
     }catch(Exception $e){}
 }