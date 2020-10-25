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
use \think\Db;
// 应用公共文件
/**
 * 获取后台菜单多维数组
 * @param array $where
 * @return false|PDOStatement|string|\think\Collection
 */
function getMenuList($where=array('pid'=>0)){
    $where['status']=1;
    $list_one=Db::name('admin_menu')->where($where)->order('sort ASC,id DESC')->select();
    if ($list_one){
        foreach ($list_one as $key=>$val){
            $list_one[$key]['iconfont']='&'.$val['iconfont'];
            $list_two=Db::name('admin_menu')->where(array('pid'=>$val['id'],'status'=>1))->order('sort ASC,id DESC')->select();
            if ($list_two){
                $list_one[$key]['sub']=$list_two;
                foreach ($list_two as $k=>$v){
                    $list_one[$key]['sub'][$k]['iconfont']='&'.$v['iconfont'];
                    $list_three=Db::name('admin_menu')->where(array('pid'=>$v['id'],'status'=>1))->order('sort ASC,id DESC')->select();
                    if ($list_three){
                        $list_one[$key]['sub'][$k]['sub']=$list_three;
                        foreach ($list_three as $kk=>$vv){
                            $list_one[$key]['sub'][$k]['sub'][$kk]['iconfont']='&'.$vv['iconfont'];
                        }
                    }
                }
            }

        }
    }
    return $list_one;
}
/**
 * 获取所有模块Service
 * @param string $name 指定service名
 * @return array
 */
function get_all_service($name,$method,$vars=array()){
    if(empty($name))return null;
    $apiPath = APP_PATH.'admin/service/'.$name.'.php';
    $apiList = glob($apiPath);
    if(empty($apiList)){
        return;
    }
    $appPathStr = strlen(APP_PATH);
    $method = 'get'.$method.$name;
    $data = array();
    $tmp=array();
    foreach ($apiList as $value) {
        $path = substr($value, $appPathStr,-4);
        $path = str_replace('\\', '/',  $path);
        $appName = explode('/', $path);
        $appName = $appName[0];
        $config = load_config($appName .'/config');
        if(!empty($config['APP_SYSTEM']) &&( !empty($config['APP_STATE']) || !empty($config['APP_INSTALL']))){
            continue;
        }
        $class = model($appName.'/'.$name,'service');
        if(method_exists($class,$method)){
            if (!empty($class->$method($vars))){
                $tmp = $class->$method($vars);
            }
        }
    }
    $data['data']['list']=$tmp;
    $data['status']=200;
    return $data;
}

/**
 * 获取菜单权限
 * $menu 所有菜单
 * $menu_purview 权限菜单
 */
function get_menu_purview($menu,$menu_purview){
    //print_r($menu);
    //print_r($menu_purview);exit;
    $menu_purview_arr=explode(',',$menu_purview);
    if ($menu){
        foreach ($menu as $key=>$val){//一级分类
            if (!in_array($val['id'],$menu_purview_arr)){
                unset($menu[$key]);
            }
            if (!empty($val['sub'])){
                foreach ($val['sub'] as $kk=>$vv){
                    if (!in_array($vv['id'],$menu_purview_arr)){
                        unset($menu[$key]['sub'][$kk]);
                    }
                    if (!empty($vv['sub'])){
                        foreach ($vv['sub'] as $kkk=>$vvv){
                            if (!in_array($vvv['id'],$menu_purview_arr)){
                                unset($menu[$key]['sub'][$kk]['sub'][$kkk]);
                            }
                        }
                    }
                }
            }
        }
    }
    $tmp['data']['list']=$menu;
    $tmp['status']=200;
    return $tmp;
}

/**
 * 获取页面类型
 */
function get_page_type(){
    return array(
        'article'=>array(
            'name'=>'文章',
            'listType'=>1,
            'order'=>0,
        ),
        'page'=>array(
            'name'=>'页面',
            'listType'=>0,
            'order'=>0,
        ),
    );
}
/**
 * 获取指定模块Service
 * @param string $name 指定service名
 * @return Service
 */
function service($appName,$name,$method,$vars=array()){
    $config = load_config($appName .'/config');
    if(!empty($config['APP_SYSTEM']) &&( !empty($config['APP_STATE']) || !empty($config['APP_INSTALL']))){
        return;
    }
    $class = model($appName.'/'.$name,'service');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}
/**
 * 读取模块配置
 * @param string $file 调用文件
 * @return array
 */
function load_config($file){
    $file = get_config_file($file);
    return require $file;
}
/**
 * 解析配置文件路径
 * @param string $file 文件路径或简写路径
 * @return dir
 */
function get_config_file($file){
    $name = $file;
    if(!is_file($file)){
        $str = explode('/', $file);
        $strCount = count($str);
        switch ($strCount) {
            case 1:
                //$app = APP_NAME;
                $app = 'admin';
                $name = $str[0];
                break;
            case 2:
                $app = $str[0];
                $name = $str[1];
                break;
        }
        $app = strtolower($app);
        if(empty($app)&&empty($file)){
            throw new \Exception("Config '{$file}' not found'", 500);
        }
        $file = APP_PATH . "{$app}/conf/{$name}.php";
        if(!file_exists($file)){
            throw new \Exception("Config '{$file}' not found", 500);
        }
    }
    return $file;
}

//ajaxReturn返回json数据
function ajaxReturn($code,$msg='操作成功',$url='',$data=array(array('name'=>'paco','url'=>'yikaiba.com')),$render=true){
    $tmp['status']=$code;
    $tmp['msg']=$msg;
    $tmp['url']=$url;
    $tmp['data']=$data;
    $tmp['render']=$render;
    return json($tmp);
}
//获取ip
function get_client_ip(){
    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
        $ip = getenv("HTTP_CLIENT_IP");
    else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
        $ip = getenv("HTTP_X_FORWARDED_FOR");
    else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
        $ip = getenv("REMOTE_ADDR");
    else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
        $ip = $_SERVER['REMOTE_ADDR'];
    else
        $ip = "unknown";
    return $ip;
}
/**
 * 调用指定模块的API
 * @param string $name  指定api名
 * @return Api
 */
function api($appName,$name,$method,$vars=array()){
    header("Content-type: text/html; charset=utf-8");
    $config = load_config($appName .'/config');
    if(!$config['APP_SYSTEM'] &&( !$config['APP_STATE'] || !$config['APP_INSTALL'])){
        return;
    }
    $class = model($appName.'/'.$name,'api');
    if(method_exists($class,$method)){
        return $class->$method($vars);
    }
}
/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}
/**
 * html代码输入
 */
function html_in($str){
    $str=htmlspecialchars($str);
    if(!get_magic_quotes_gpc()) {
        $str = addslashes($str);
    }
    return $str;
}

/**
 * html代码输出
 */
function html_out($str){
    if(function_exists('htmlspecialchars_decode')){
        $str=htmlspecialchars_decode($str);
    }else{
        $str=html_entity_decode($str);
    }
    $str = stripslashes($str);
    return $str;
}
/**
 * 站点设置信息调取
 */
function get_site($name='site_title'){
    $config_info=model('admin/Config')->getInfo();
    return $config_info[$name];
}

/**
 * 删除目录及目录下所有文件或删除指定文件
 * @param str $path   待删除目录路径
 * @param int $delDir 是否删除目录，1或true删除目录，0或false则只删除文件保留目录（包含子目录）
 * @return bool 返回删除状态
 */
function delDirAndFile($path, $delDir = FALSE) {
    $handle = opendir($path);
    if ($handle) {
        while (false !== ( $item = readdir($handle) )) {
            if ($item != "." && $item != "..")
                is_dir("$path/$item") ? delDirAndFile("$path/$item", $delDir) : unlink("$path/$item");
        }
        closedir($handle);
        if ($delDir)
            return rmdir($path);
    }else {
        if (file_exists($path)) {
            return unlink($path);
        } else {
            return FALSE;
        }
    }
}



/**
 * 过滤数组元素前后空格 (支持多维数组)
 * @param $array 要过滤的数组
 * @return array|string
 */
function trim_array_element($array){
    if(!is_array($array))
        return trim($array);
    return array_map('trim_array_element',$array);
}

/**
 * 获取url 中的各个参数  类似于 pay_code=alipay&bank_code=ICBC-DEBIT
 * @param type $str
 * @return type
 */
function parse_url_param($str){
    $data = array();
    $parameter = explode('&',end(explode('?',$str)));
    foreach($parameter as $val){
        $tmp = explode('=',$val);
        $data[$tmp[0]] = $tmp[1];
    }
    return $data;
}
/**
 * 中文字符串截取
 */
function len($str, $len=0)
{
    if(!empty($len)){
        return \org\Util::msubstr($str, 0, $len);
    }else{
        return $str;
    }
}
/**
 * 写文件
 */
function write_text($content,$payType='weixin'){
    $path = $payType."_log/";
    if (!is_dir($path)){
        mkdir($path,0777);  // 创建文件夹test,并给777的权限（所有权限）
    }
    $file = $path."weixin_log.txt";    // 写入的文件
    $fp = fopen($file, 'a+b');
    fwrite($fp, var_export($content, true));
}
/**
 * 写文件
 */
function write_lang_file($file){
    $content=<<<EOF
<?php
/**
 * 语言
 */
return [
    "test" => "测试语言",
];
EOF;
    
    file_put_contents($file,$content);
}
/**
 * 获取语言信息
 */
function get_lang_info($lang_id,$field='*'){
    if (empty($lang_id)){
        return ;
    }
    $info = Db::name('lang')->field($field)->where('lang_id',$lang_id)->find();
    if ($field!='*'){
        return $info[$field];
    }else{
        return $info;
    }
}
/**
 * 获取当前语言id
 */
function get_lang_id(){
    if (cookie('think_var')){
        $where['lang']=cookie('think_var');
        $info=model('admin/lang')->getWhereInfo($where);
        if ($info){
            return $info['lang_id'];
        }
    }
    return false;
}
/**
 * 自适应URL规则
 * @param string $str URL路径
 * @param string $params 自动解析参数
 * @param string $mustParams 必要参数
 * @return url
 */
function match_url($str,$params = array(), $mustParams = array()){
    $newParams = array();
    $keyArray = array_keys($params);
    if(config('REWRITE_ON')){
        //获取规则文件
        $config = config('REWRITE_RULE');
        $configArray = array_flip($config);
        $route = $configArray[$str];
        if($route){
            preg_match_all('/<(\w+)>/', $route, $matches);
            foreach ($matches[1] as $value) {
                if($params[$value]){
                    $newParams[$value] = $params[$value];
                }
            }
        }else{
            if(!empty($keyArray)){
                $newParams[$keyArray[0]] = current($params);
            }
        }
    }else{
        if(!empty($keyArray)){
            $newParams[$keyArray[0]] = current($params);
        }
    }
    //语言
    $lang_arr=array();
    /*if (get_lang_id()){
        $lang_arr=array('lang_id'=>get_lang_id());
    }*/
    $newParams = array_merge((array)$newParams,(array)$mustParams,(array)$lang_arr);
    $newParams = array_filter($newParams);
    return url($str, $newParams);
}
/*******************************验证规则开始**********************************/
function is_url($str){
    return preg_match("/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\’:+!]*([^<>\"])*$/", $str);
}
/*******************************验证规则结束**********************************/