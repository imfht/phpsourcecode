<?php
define('WEB_PUBLIC_PATH', __ROOT__.'/Public'); 

const ZS_VERSION = '2.6';
const ZS_ADDON_PATH = './Addons/';

function asyn_sendwx()
{

	
	  $domain=$_SERVER['HTTP_HOST'];

	$url=(is_ssl()?'https://':'http://').$_SERVER['HTTP_HOST'].'/'.C('WEB_DIR').'/'.U('Home/Weixin/sendwx');

	$par=time();

	$header="POST $url HTTP/1.0\r\n";

	$header.="Content-Type: application/x-www-form-urlencoded\r\n";

	$header.="Content-Length: ".strlen($par)."\r\n\r\n";

	$fp=@fsockopen ($domain,80,$errno,$errstr,30);

	fputs($fp,$header.$par);

	fclose($fp);  


}
function zs_get_contents($url) {

$ch = curl_init(); 
curl_setopt ($ch, CURLOPT_URL, $url); 
curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1); 
curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT,10); 
$dxycontent = curl_exec($ch); 

	return $dxycontent;
}
/**
 * 执行SQL文件
 */
function execute_sql_file($sql_path) {
	// 读取SQL文件
	$sql = file_get_contents ( $sql_path );
	$sql = str_replace ( "\r", "\n", $sql );
	$sql = explode ( ";\n", $sql );

	// 替换表前缀
	$orginal = 'zswin_';
	$prefix = C ( 'DB_PREFIX' );
	$sql = str_replace ( "{$orginal}", "{$prefix}", $sql );
	// 开始安装
	foreach ( $sql as $value ) {
		$value = trim ( $value );
		if (empty ( $value ))
			continue;

		$res = M ()->execute ( $value );
		
		
	}
}
function colorCallback() {
	
	$color = dechex(rand(0,16777215));
	
	$text = "style=\"color:#{$color};\"";
	
	return $text;
}
function colorbackCallback() {

	$color = dechex(rand(0,16777215));

	$text = "style=\"background:#{$color};\"";

	return $text;
}
function getdefaultid($id){
	
	if($id<1){
		return 'all';
	}else{
		return $id;
	}
	
}

function ZSU($str1,$str2,$arg,$flag=false){
	
	
	if(C('URL_MODEL') == 0){
		
		return U($str2,$arg);
	}else{
		return U($str1);
		
	}
	
	
	
}
function navactive($id, $type){
	$info=D('nav')->where(array('id'=>$id))->find();
	
	$url=$_SERVER['REQUEST_URI'];
	
	

	
	if($type==1||$type==0||$type==2){
		if($url==navurl($id,$type))
		{
			$active=1;
		}
		
		
		
		
	}
	
	
	
	if($type==3){
		if($url==$info['url'])
		{
			$active=1;
		}
		
		
		
	}
	
	
	
	
	
	
	return $active;
}
function navurl($id,$type){
	
	$info=D('nav')->where(array('id'=>$id))->find();
	
	if($type==0){
	
		$ctype=get_cate_typeByid($info['cid']);
		switch ($ctype){
		
			case 1:
				$url=ZSU('/artlist/'.$info['cid'],'Index/artlist',array('cid'=>$info['cid']));
				break;
			case 2:
				$url=ZSU('/musiclist/'.$info['cid'],'Index/musiclist',array('cid'=>$info['cid']));
				break;
			case 3:
				$url=ZSU('/grouplist/'.$info['cid'],'Index/grouplist',array('cid'=>$info['cid']));
				break;
		
		
		
		}
			
		
	}
	if($type==1){
		
		switch ($info['gid']){
	
			case 1:
				$url=ZSU('/artlist/all','Index/artlist');
				break;
			case 2:
				$url=ZSU('/musiclist/all','Index/musiclist');
				break;
			case 3:
				$url=ZSU('/grouplist/all','Index/grouplist');
				break;
	
	
	
		}
			
	}
	if($type==2){
	
		$url=U($info['controll'].'/'.$info['action']);
	}
	if($type==3){
		$url=$info['url'];
	}
	
	return $url;
	
	
}

function setmessread($uid,$type){
	$message['to_uid'] = $uid;
    $message['type'] = $type;
    
    $list=D('message')->where($message)->select();
    foreach ($list as $key =>$vo){
    	
    	if($vo['is_read']==0){
    	$rs = D('message')->where(array('id'=>$vo['id']))->setField('is_read',2);	
    	}
        if($vo['is_read']==2){
    	$rs = D('message')->where(array('id'=>$vo['id']))->setField('is_read',1);	
    	}
    	
    }
    
	 
        return $rs;
	
}

function sendMessage($to_uid, $from_uid = 0, $title = '', $content = '', $type = 0)
{
        $message['to_uid'] = $to_uid;
        $message['content'] = op_h($content,'link');
        $message['title'] = $title;
        $message['from_uid'] = $from_uid;
        $message['type'] = $type;
        $message['create_time'] = time();
        $message['is_read'] = 0;
        $message['status'] = 1;
        $rs = D('message')->add($message);
        return $rs;
    }

function getPosition($tj) {
	switch ($tj) {
		case 0 :
			$showText = '无';
			break;
		case 2 :
			$showText = '置顶';
			
			break;
		case 3 :
			$showText = '全局置顶';
			
			break;
		case 1 :
			$showText = '推荐';
			break;
		
	}
	return $showText;
}

function getImgs($content,$order='ALL'){
	$pattern="/<img.*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/i";
	preg_match_all($pattern,$content,$match);
	if(isset($match[1])&&!empty($match[1])){
		if($order==='ALL'){
			return $match[1];
		}
		if(is_numeric($order)&&isset($match[1][$order])){
			$match[1][$order]=str_replace('\\', '', $match[1][$order]);
			return $match[1][$order];
		}
	}
	return '';
}
function get_cover($cover_id, $field = null)
{
    if (empty($cover_id)) {
        return false;
    }
    $picture = M('Picture')->where(array('status' => 1))->getById($cover_id);

    if(is_bool(strpos($picture['path'],'http://'))){
        $picture['path']=fixAttachUrl($picture['path']);
    }

    return empty($field) ? $picture : $picture[$field];
}
function getpic($_content){
    preg_match ("<img.*src=[\"](.*?)[\"].*?>", $_content, $_match);
   
    return $_match[1]; 
}
function getpcomment($id){
	
	$map['id']=$id;
	$pid=M('LocalComment')->where($map)->getField('pid');
	$map1['id']=$pid;
	$content=M('LocalComment')->where($map1)->getField('content');
	return op_h($content);
}
function getartcount($id,$type='cid'){
	switch ($type){
		case 'tag':
			$map['tag']=array('like','%'.$id.'%');
			break;
		case 'search':
			$map['title']=array('like','%'.$id.'%');
			break;
		case 'cid':
			$astr=D('Home/Cate')->getChildrenId($id);
			if($astr!=null){
				$map['cid']=array('in', $astr);
			}
	       
			break;
	}
	

	
	$map['status']=1;
	
	$count = M('Article')->where($map)->count();
	
    if($count==''){
		
		$count=0;
	}
	return $count;
}
function getartview($id,$type='cid'){
	switch ($type){
		case 'tag':
			$map['tag']=array('like','%'.$id.'%');
			break;
		case 'search':
			$map['title']=array('like','%'.$id.'%');
			break;
		case 'cid':
			$astr=D('Home/Cate')->getChildrenId($id);
			if($astr!=null){
				$map['cid']=array('in', $astr);
			}
			break;
	}
	
	$map['status']=1;
	$view = M('Article')->where($map)->sum('view');
	
	if($view==''){
		
		$view=0;
	}
	return $view;
}



/**
 * t函数用于过滤标签，输出没有html的干净的文本
 * @param string text 文本内容
 * @return string 处理后内容
 */
function op_t($text)
{ 
    
    $text = nl2br($text);
    $text = real_strip_tags($text);
    $text = addslashes($text);
    $text = trim($text);
    $text = preg_replace('/(\[attach\])([\d]*)(\[\/attach\])/', '', $text);
    return $text;
}

/**
 * h函数用于过滤不安全的html标签，输出安全的html
 * @param string $text 待过滤的字符串
 * @param string $type 保留的标签格式
 * @return string 处理后内容
 */
function op_h($text, $type = 'html')
{
    // 无标签格式
    $text_tags = '';
    //只保留链接
    $link_tags = '<a>';
    //只保留图片
    $image_tags = '<img>';
    //只存在字体样式
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    //标题摘要基本格式
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    //兼容Form格式
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    //内容等允许HTML的格式
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    //专题等全HTML格式
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    //过滤标签
    $text = real_strip_tags($text, ${$type . '_tags'});
    // 过滤攻击代码
    if ($type != 'all') {
        // 过滤危险的属性，如：过滤on事件lang js
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
        while (preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }
    return $text;
}
function real_strip_tags($str, $allowable_tags = "")
{
    $str = html_entity_decode($str, ENT_QUOTES, 'UTF-8');
    return strip_tags($str, $allowable_tags);
}
/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
 // 分析枚举类型配置值 格式 a:名称1,b:名称2
function parse_config_attr($string) {
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}
 // 分析枚举类型字段值 格式 a:名称1,b:名称2
 // 暂时和 parse_config_attr功能相同
 // 但请不要互相使用，后期会调整
function parse_field_attr($string) {
    if(0 === strpos($string,':')){
        // 采用函数定义
        return   eval(substr($string,1).';');
    }
    $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
    if(strpos($string,':')){
        $value  =   array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k]   = $v;
        }
    }else{
        $value  =   $array;
    }
    return $value;
}




function handle_exception($exception)
{
    // 显示错误消息
    $message = $exception->getMessage();
    if (method_exists($exception, 'getExtra')) {
        $extra = $exception->getExtra();
    } else {
        $extra = array();
    }
    $extra['error_code'] = $exception->getCode();
    api_show_error($message, $extra);
}

function getRootUrl()
{
	
    if (__ROOT__ != '') {
        return __ROOT__ . '/';
    }
    if (C('URL_MODEL') == 2||C('URL_MODEL') == 1)
        return __ROOT__ . '/';
    return __ROOT__;
}
/**对于附件来修正其url，兼容urlmodel2,sae
 * @param $url
 * @return string
 * @auth 陈一枭
 */
function fixAttachUrl($url)
{
    if(!is_sae()){
        return getRootUrl() . substr($url, 1);
    }else{
        return $url;
    }

}

function IP($ip = '', $file = 'UTFWry.dat') {
	$_ip = array();
	if (isset($_ip [$ip])) {
		return $_ip [$ip];
	} else {
		import("ORG.Net.IpLocation");
		$iplocation = new IpLocation($file);
		$location = $iplocation->getlocation($ip);
		$_ip [$ip] = $location ['country'] . $location ['area'];
	}
	return $_ip [$ip];
}

function gettaginfo($id){
	
	
return	M('tags')->where(array('id'=>$id))->find();
	
	
}
function gettaginfobytitle($title){
	
	
return	M('tags')->where(array('title'=>$title))->find();
	
	
}

/**
 * 获取 IP  地理位置
 * 淘宝IP接口
 * @Return: array
 */
function get_city_by_ip($ip)
{
    $url = "http://ip.taobao.com/service/getIpInfo.php?ip=" . $ip;
    $ipinfo = json_decode(file_get_contents($url));
    if ($ipinfo->code == '1') {
        return false;
    }
    $city = $ipinfo->data->region . $ipinfo->data->city; //省市县
    $ip = $ipinfo->data->ip; //IP地址
    $ips = $ipinfo->data->isp; //运营商
    $guo = $ipinfo->data->country; //国家
    if ($guo == '中国') {
        $guo = '';
    }
    return $guo . $city . $ips . '[' . $ip . ']';

}

/* 解析列表定义规则*/

function get_list_field($data, $grid,$model){

	// 获取当前字段数据
    foreach($grid['field'] as $field){
        $array  =   explode('|',$field);
        $temp  =	$data[$array[0]];
        // 函数支持
        if(isset($array[1])){
            $temp = call_user_func($array[1], $temp);
        }
        $data2[$array[0]]    =   $temp;
    }
    if(!empty($grid['format'])){
        $value  =   preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data2){return $data2[$match[1]];}, $grid['format']);
    }else{
        $value  =   implode(' ',$data2);
    }

	// 链接支持
	if(!empty($grid['href'])){
		$links  =   explode(',',$grid['href']);
        foreach($links as $link){
            $array  =   explode('|',$link);
            $href   =   $array[0];
            if(preg_match('/^\[([a-z_]+)\]$/',$href,$matches)){
                $val[]  =   $data2[$matches[1]];
            }else{
                $show   =   isset($array[1])?$array[1]:$value;
                // 替换系统特殊字符串
                $href	=	str_replace(
                    array('[DELETE]','[EDIT]','[MODEL]'),
                    array('del?ids=[id]&model=[MODEL]','edit?id=[id]&model=[MODEL]',$model['id']),
                    $href);

                // 替换数据变量
                $href	=	preg_replace_callback('/\[([a-z_]+)\]/', function($match) use($data){return $data[$match[1]];}, $href);

                $val[]	=	'<a href="'.U($href).'">'.$show.'</a>';
            }
        }
        $value  =   implode(' ',$val);
	}
    return $value;
}


require_once(APP_PATH . '/Common/Common/api.php');//api相关调用
require_once(APP_PATH . '/Common/Common/user.php');//用户登录、用户信息相关
require_once(APP_PATH . '/Common/Common/mail.php');//邮件相关
require_once(APP_PATH . '/Common/Common/addons.php');//插件、钩子相关
require_once(APP_PATH . '/Common/Common/string.php');//数组处理、字符串处理，过滤转义字符等
require_once(APP_PATH . '/Common/Common/safe.php');//加密解密认证相关
require_once(APP_PATH . '/Common/Common/DirFile.php');//目录与文件操作相关
require_once(APP_PATH . '/Common/Common/date.php');//日期时间处理相关
require_once(APP_PATH . '/Common/Common/cate.php');//分类相关
require_once(APP_PATH . '/Common/Common/thumb.php');//缩略图相关
require_once(APP_PATH . '/Common/Common/pagination.php');//缩略图相关
function tox_addons_url($url, $param)
{
    // 拆分URL
    $url = explode('/', $url);
    $addon = $url[0];
    $controller = $url[1];
    $action = $url[2];

    // 调用u函数
    $param['_addons'] = $addon;
    $param['_controller'] = $controller;
    $param['_action'] = $action;
    return U("Home/Addons/execute", $param);
}
/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 */
function get_nav_url($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 * @param $url 检测当前url是否被选中
 * @return bool|string
 */
function get_nav_active($url)
{
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
            if (strtolower($url) === strtolower($_SERVER['HTTP_REFERER'])) {
                return 1;
            }
        case '#' === substr($url, 0, 1):
            return 0;
            break;
        default:
            $url_array = explode('/', $url);
            if ($url_array[0] == '') {
                $MODULE_NAME = $url_array[1];
            } else {
                $MODULE_NAME = $url_array[0]; //发现模块就是当前模块即选中。

            }
            if (strtolower($MODULE_NAME) === strtolower(MODULE_NAME)) {
                return 1;
            };
            break;

    }
    return 0;
}

// 获取模型名称
function get_model_by_id($id){
    return $model = M('Model')->getFieldById($id,'title');
}
// 获取属性类型信息
function get_attribute_type($type=''){
    // TODO 可以加入系统配置
    static $_type = array(
        'num'       =>  array('数字','int(10) UNSIGNED NOT NULL'),
        'string'    =>  array('字符串','varchar(255) NOT NULL'),
        'textarea'  =>  array('文本框','text NOT NULL'),
        'datetime'  =>  array('时间','int(10) NOT NULL'),
        'bool'      =>  array('布尔','tinyint(2) NOT NULL'),
        'select'    =>  array('枚举','char(50) NOT NULL'),
    	'radio'		=>	array('单选','char(10) NOT NULL'),
    	'checkbox'	=>	array('多选','varchar(100) NOT NULL'),
    	'editor'    =>  array('编辑器','text NOT NULL'),
    	'picture'   =>  array('上传图片','int(10) UNSIGNED NOT NULL'),
    	'file'    	=>  array('上传附件','int(10) UNSIGNED NOT NULL'),
    );
    return $type?$_type[$type][0]:$_type;
}
function getattachname($id){
	$map['id']=$id;
	return M('File')->where($map)->getField('name');
}
function getattachsize($id){
	$map['id']=$id;
	return M('File')->where($map)->getField('size');
}
function getattachdnum($id){
	$map['id']=$id;
	return M('File')->where($map)->getField('download');
}
function getqnattachname($id){
	$map['id']=$id;
	return M('Qiniu')->where($map)->getField('name');
}
function getqnattachsize($id){
	$map['id']=$id;
	return M('Qiniu')->where($map)->getField('size');
}
function getqnattachdnum($id){
	$map['id']=$id;
	return M('Qiniu')->where($map)->getField('download');
}