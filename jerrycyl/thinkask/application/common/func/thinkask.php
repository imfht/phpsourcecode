<?php 
use think\Cache;
use think\Config;
use think\Cookie;
use think\Db;
use think\Debug;
use think\exception\HttpException;
use think\exception\HttpResponseException;
use think\Lang;
use think\Loader;
use think\Log;
use think\Request;
use think\Response;
use think\Session;
use think\Url;
use think\View;
/**
 * [build description]
 * @Author   Jerry
 * @DateTime 2017-04-11T14:21:03+0800
 * @Example  eg:
 * @param    [type]                   $name [description]
 * @param    [type]                   $init [description]
 * @param    [type]                   $new  [description]
 * @return   [type]                         [description]
 */
function build($name){
	// show($name);
	return new \app\common\build\builder($name); // 返回全新对象
}

if (!function_exists('clear_js')) {
    /**
     * 过滤js内容
     * @param string $str 要过滤的字符串
     * @author 
     * @return mixed|string
     */
    function clear_js($str = '')
    {
        $search ="/<script[^>]*?>.*?<\/script>/si";
        $str = preg_replace($search, '', $str);
        return $str;
    }
}
if (!function_exists('load_static')) {
    /**
     * 加载静态资源
     * @param string $static 资源名称
     * @param string $type 资源类型
     * @author 
     * @return string
     */
    function load_static($static = '', $type = 'css')
    {
        $assets_list = config($static);
        $result = '';
        foreach ($assets_list as $item) {
            if ($type == 'css') {
                $result .= '<link rel="stylesheet" href="'.$item.'">'."\n";
            } else {
                $result .= '<script src="'.$item.'"></script>'."\n";
            }
        }
        return $result;
    }
}

if (!function_exists('load_static_default')) {
    /**
     * [load_static_default 加载默认的静态资源]
     * @Author   Jerry
     * @DateTime 2017-05-03T11:50:31+0800
     * @Example  eg:
     * @param    [type]                   $path         [description]
     * @param    string                   $publicStatic [description]
     * @return   [type]                                 [description]
     * // echo "当前模块名称是" . $request->module();
     *  // echo "当前控制器名称是" . $request->controller();
     *   // echo "当前操作名称是" . $request->action();
     *   {:load_static_default('/jpublic/blog/')}
     */
    function load_static_default($path,$publicStatic='global'){
        $request = Request::instance();
        $file = strtolower($request->module()."-".$request->controller()."-".$request->action());
        $files = [
            $publicStatic.".css",
            $publicStatic.".js",
            $file.".css",
            $file.".js",
        ];
        // show($files);
        //路径生成 $path+$controller+文件类型
        //先检查文件是否存在,存在才生成
        // 默认global文件处理
        $result = "";
        foreach ($files as $key => $v) {
                 switch (substr($v, -3)) {
                       case 'css':
                            if(file_exists(".".$path.'css/'.$v)){
                               $result .= '<link rel="stylesheet" href="'.$path.'css/'.$v.'">'."\n"; 
                            }
                           break;
                        case '.js':
                        if(file_exists(".".$path.'js/'.$v)){
                           $result .= '<script src="'.$path.'js/'.$v.'"></script>'."\n"; 
                        }
                        break;
                   }  
            
        }
        return $result;
    }    
}
if (!function_exists('minify')) {
    /**
     * 合并输出js代码或css代码 需要minify插件支付
     * @param string $type 类型：group-分组，file-单个文件，base-基础目录
     * @param string $files 文件名或分组名
     * @author 
     */
    function minify($type = '',$files = '')
    {
        $files = !is_array($files) ? $files : implode(',', $files);
        $url   = '/public/min/?';

        switch ($type) {
            case 'group':
                $url .= 'g=' . $files;
                break;
            case 'file':
                $url .= 'f=' . $files;
                break;
            case 'base':
                $url .= 'b=' . $files;
                break;
        }
        echo $url;
    }
}
if (!function_exists('parse_attr')) {
    /**
     * 解析配置
     * @param string $value 配置值
     * @return array|string
     */
    function parse_attr($value = '') {
        $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
        if (strpos($value, ':')) {
            $value  = array();
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k]   = $v;
            }
        } else {
            $value = $array;
        }
        return $value;
    }
}

if (!function_exists('get_file_path')) {
    /**
     * 获取附件路径
     * @param int $id 附件id
     * @author 
     * @return string
     */
    function get_file_path($id = 0)
    {
        $path = model('asset/attachment')->getFilePath($id);
        if (!$path) {
            return "/public/static/images/default_avatar/none.png";
        }
        return $path;
    }
}
if (!function_exists('get_file_name')) {
    /**
     * 根据附件id获取文件名
     * @param string $id 附件id
     * @author 
     * @return string
     */
    function get_file_name($id = '')
    {
        $name = model('asset/attachment')->getFileName($id);
        if (!$name) {
            return '没有找到文件';
        }
        return $name;
    }
}
if (!function_exists('get_thumb')) {
    /**
     * 获取图片缩略图路径
     * @param int $id 附件id
     * @author 
     * @return string
     */
    function get_thumb($id = 0)
    {
        $path = model('asset/attachment')->getThumbPath($id);
        if (!$path) {
            return "/public/static/images/default_avatar/none.png";
        }
        return $path;
    }
}
if (!function_exists('get_location')) {
    /**
     * 获取当前位置
     * @author 
     * @return mixed
     */
    function get_location()
    {
        // $location = model('common/node')->getLocation();
        // return $location;
    }
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook, $params = array()) {
	\think\Hook::listen($hook, $params);
}
/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * 
 */
function addons_url($url, $param = array()) {
	$url        = parse_url($url);
	$case       = config('URL_CASE_INSENSITIVE');
	$addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
	$controller = $case ? parse_name($url['host']) : $url['host'];
	$action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

	/* 解析URL带的参数 */
	if (isset($url['query'])) {
		parse_str($url['query'], $query);
		$param = array_merge($query, $param);
	}

	/* 基础参数 */
	$params = array(
		'mc' => $addons,
		'op' => $controller,
		'ac' => $action,
	);
	$params = array_merge($params, $param); //添加额外参数

	return \think\Url::build('index/addons/execute', $params);
}
/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name) {
	$class = "\\addons\\" . strtolower($name) . "\\{$name}";
	return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name) {
	$class = get_addon_class($name);
	if (class_exists($class)) {
		$addon = new $class();
		return $addon->getConfig();
	} else {
		return array();
	}
}
//默认上传图片
function default_upload_img($var){
	return $var?$var:"/public/static/images/icon/Upload.png";
	
}
//默认图像
function default_avatar_img($var){
	return $var?$var:"/public/static/images/default_avatar/avatar-mid-img.png";
}

/**
 * [send_mail description]
 * @Author   Jerry
 * @DateTime 2017-04-17T14:01:53+0800
 * @Example  eg:
 * @param    [type]                   $customsmail [description]
 * @param    [type]                   $data        [subject,content ...]
 * @param    [type]                   $template    [description]
 * @return   [type]                                [description]
 */
 function send_mail($customsmail,$data,$template='public'){
 		$res = action('common/send/mail',['customsmail'=>$customsmail,'data'=>$data,'template'=>$template]);
 		return $res;
} 
/**
 * [formatArr 格式化数组,让适应tmake的SELECT]
 * @Author   Jerry
 * @DateTime 2017-04-21T18:11:42+0800
 * @Example  eg:
 * @param    [type]                   $arry [description]
 * @return   [type]                         [description]
 */
function formatArr($array,$key_name,$value_name,$default=[]){
    $formatArr = is_array($default)?$default:[];
    foreach ($array as $k => $v) { 
        $formatArr[$v[$key_name]] = $v[$value_name];
    }
    return $formatArr;
}

if (!function_exists('returnJson')) {
    /**
     * 获取\think\response\Json对象实例
     * @param mixed   $data 返回的数据
     * @param integer $code 状态码
     * @param array   $header 头部
     * @param array   $options 参数
     * @return \think\response\Json
     */
        function returnJson($status=0,$data = [],$msg='',$code = 200, $header = [], $options = [])
        {   
            $datas['status'] = $status;
            $datas['msg'] = $msg;
            $datas['data'] = $data;
            die(json_encode($datas));
        }
}
if (!function_exists('systemLog')) {
    /**
     * [systemLog 系统日志]
     * @Author   Jerry
     * @DateTime 2017-05-02T09:56:00+0800
     * @Example  eg:
     * @param    [type]                   $uid      [description]
     * @param    [type]                   $username [description]
     * @param    [type]                   $log      [description]
     * @param    string                   $remark   [description]
     * @return   [type]                             [description]
     */
    function systemLog($uid,$user_name,$log,$tag,$remark=""){
        $data = [
        'uid'               =>$uid,
        'ip'                =>fetch_ip(),
        'log'               =>$log,
        'url'               =>$_SERVER['REQUEST_URI'],
        'create_date'       =>date('Y-m-d H:i:s',time()),
        'user_name'         =>$user_name,
        'remark'            =>$remark,
        'http_user_agent'   =>$_SERVER['HTTP_USER_AGENT'],
        'http_accept'       =>$_SERVER['HTTP_ACCEPT'],
        'http_host'         =>$_SERVER['HTTP_HOST'],
        'tag'               =>$tag,
        ];
        model('base')->getadd('system_log',$data);
    }

}

if (!function_exists('turl')) {
    /**
     * turl(thinkask URL生成器)生成 
     * @param string        $url 路由地址
     * @param string|array  $vars 变量
     * @param bool|string   $suffix 生成的URL后缀
     * @param bool|string   $domain 域名
     * @return string
     */
    function turl($url = '', $vars = '', $suffix = true, $domain = false)
    {
        $config = config('multi_route_modules');
         $module = strtolower(current($arr = explode("/", trim($url,'/'))));
         $url = trim($url);

        foreach ($config as $k => $v) {
           if(array_search($module, $v)!==false){
            ##绑定的顶级域名
              return config('domain_agreement').str_replace(config('domain_agreement'), "", trim($k)).Url::build($url, $vars, $suffix, $domain);
           }else{
            ##绑定的其它域名
             return config('domain_agreement').str_replace(config('domain_agreement'), "", config('root_domain')).Url::build($url, $vars, $suffix, $domain);
           }
        }
       
    }
}
