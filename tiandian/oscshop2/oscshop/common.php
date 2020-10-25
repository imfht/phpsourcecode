<?php
require_once 'helper.php';
use think\Db;
use think\captcha\Captcha;

//取得url中加密的id
function hashids_decode($id){
	$get_id=hashids()->decode($id);	
	if(empty($get_id)){
		return null;
	}
	return $get_id[0];
}
//接口调试
function osc_test($info){
	Db::name('test')->insert(['info'=>$info,'create_time'=>date('Y-m-d H:i:s',time())]);
}

//驼峰命名法转下划线风格
function to_under_score($str){
		
	$array = array();
	for($i=0;$i<strlen($str);$i++){
		if($str[$i] == strtolower($str[$i])){
			$array[] = $str[$i];
		}else{
			if($i>0){
				$array[] = '_';
			}
			$array[] = strtolower($str[$i]);
		}
	}
	
	$result = implode('',$array);
	return $result;
}

//验证(验证码)
function check_verify($code, $id = 1){
    $verify = new Captcha((array)Config('captcha'));
    return $verify->check($code, $id);
}

/**
 * 读取支付配置
 * @param string $code 支付方式code
 * @return array 
 */
function payment_config($code){
	
	$payment_code=$code.'_cache';
	
	if (!$config = cache($payment_code)) {
		$list=Db::name('config')->where(array('extend_value'=>$code,'status'=>1))->select();
		$config=[];
		foreach ($list as $k => $v) {
			$config[$v['name']]=$v['value'];
		}
		cache($payment_code,$config);		
	}	
	return $config;
}
/**
 * 通过id取得订单状态名称
 * 
 */
function get_order_status_name($order_status_id){
	if (!$order_status = cache('order_status_list')) {
		
		$list=Db::name('order_status')->select();	
			
		foreach ($list as $k => $v) {
			$o_status[$v['order_status_id']]=$v;
		}
		cache('order_status_list', $o_status);
		
		$order_status=$o_status;
	}
	if(!isset($order_status[$order_status_id]['name'])){
		return null;
	}
	return $order_status[$order_status_id]['name'];		
}
//取得货运方式名称
function get_shipping_name($id){
	if (!$shipping_list = cache('shipping_list')) {
		
		$list=Db::name('transport')->select();
		
		foreach ($list as $k => $v) {
			$shipping[$v['id']]=$v;
		}
		cache('shipping_list', $shipping);	
		$shipping_list=$shipping;
	}
	if(!isset($shipping_list[$id]['title'])){
		return null;
	}
	return $shipping_list[$id]['title'];	
}
//通过地区的id取地区的名称
function get_area_name($area_id){
	
	if (!$area_list = cache('area_list')) {
		
		$list=Db::name('area')->field('area_id,area_name')->select();
		
		foreach ($list as $k => $v) {
			$area[$v['area_id']]=$v;
		}
		cache('area_list', $area);	
		
		$area_list=$area;
	}
	if(!isset($area_list[$area_id]['area_name'])){
		return null;
	}
	return $area_list[$area_id]['area_name'];
	
}

//通过id取重量的名称
function get_weight_name($weight_id){
	if (!$weight_list = cache('weight_list')) {
		
		$list=Db::name('weight_class')->select();
		
		foreach ($list as $k => $v) {
			$weight[$v['weight_class_id']]=$v;
		}
		cache('weight_list', $weight);	
		
		$weight_list=$weight;
	}
	if(!isset($weight_list[$weight_id]['title'])){
		return null;
	}
	return $weight_list[$weight_id]['title'];	
}

//获取网站会员信息
function member($key){
	
	$member=('session'==config('member_login_type'))?session('member_user_auth'):cookie('member_user_auth');
	
	if(empty($member)){
		return null;
	}
	
	if(!isset($member[$key])&&$member['uid']){
		
		$user=Db::name('member')->where('uid',$member['uid'])->find();
	
		if(isset($user[$key])){
			return $user[$key];
		}
		
		return null;
		
	}
	
	return $member[$key];
	
}
//逗号切割重组字符串
function explode_build_string($string){		
	if(empty($string)){
		return ;
	}		
	$arr=explode(',', $string);
	$str=null;
	foreach ($arr as $k => $v) {
			if($v!=end($arr)){
				$str.=(int)$v.',';
			}else{
				$str.=(int)$v;
			}
		}	
	return $str;
}

//生成唯一订单号
function build_order_no(){
    return date('Ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}
/**
 * 清空缓存
 */
function clear_cache() {  
    $dirtool = new \oscshop\Dir();   
    $dirtool->delDir(ROOT_PATH.'runtime/cache/');  
}

/**
 * 检查模块是否已经安装
 * @param string $moduleName 模块名称
 * @return boolean
 */
function is_module_install($moduleName) {
	
	if (!$module = cache('module')) {
		
		$list=Db::name('module')->field('module')->where('disabled',1)->select();
		
		$m=[];
		
		foreach ($list as $k => $v) {
			$m[$v['module']]=$v;
		}
		cache('module', $m);	
		
		$module=$m;
	}
	
	if(in_array($moduleName,array('admin'))){	
		return true;
	}elseif(isset($module[$moduleName])) {
        return true;
    }
    return false;
}

/**
 * 读取配置
 * @return array 
 */
function load_config(){
	$list=Db::name('config')->where('status',1)->select();
	$config=[];
	foreach ($list as $k => $v) {
		$config[trim($v['name'])]=$v['value'];
	}
	return $config;
}

/**
 * 保存用户行为，前台用户和后台用户
 * @param int $uid 用户id
 * @param string $name 用户名
 * @param string $type config('FRONTEND_USER')/config('BACKEND_USER')
 * @param string $info 行为描述
 */
function storage_user_action($uid,$name,$type,$info){
	if(config('storage_user_action')==true){
		Db::name('user_action')->insert([
		'type'=>$type,
		'user_id'=>$uid,
		'uname'=>$name,
		'add_time'=>time(),
		'info'=>$info]);
	}
}


/**
 * 自动生成新尺寸 的图片
 * @param string $filename 文件名
 * @param int $width 新图片宽度
 * @param int $height 新图片高度(如果没有填写高度，把高度等比例缩小)
 */
function resize($filename, $width, $height=null) {		
	if (!is_file(DIR_IMAGE . $filename)) {
			return;
		}
		//如果没有填写高度，把高度等比例缩小
		if($height==null){			
			$info = getimagesize(DIR_IMAGE . $filename);			
			if($width>$info[0]){//如果缩小后宽度尺寸大于原图尺寸，使用原图尺寸
				$width=$info[0];
				$height=$info[1];
			}else{
				$height=floor($info[1]*($width/$info[0]));
			}			
		}		
		$extension = pathinfo($filename, PATHINFO_EXTENSION);
		$old_image = $filename;
		$new_image = 'cache/' . mb_substr($filename, 0, mb_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;
		if (!is_file(DIR_IMAGE . $new_image)) {
			$path = '';
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}
			list($width_orig, $height_orig) = getimagesize(DIR_IMAGE . $old_image);
			if ($width_orig != $width || $height_orig != $height) {
				$image = new \oscshop\Image(DIR_IMAGE . $old_image);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $new_image);
			} else {
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
			}
		}			
	return 'public/uploads/' . $new_image;			
}



/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
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
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 (单位:秒)
 * @return string 
 */
function think_ucenter_encrypt($data, $key, $expire = 0) {
	$key  = md5($key);
	$data = base64_encode($data);
	$x    = 0;
	$len  = strlen($data);
	$l    = strlen($key);
	$char =  '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x=0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	$str = sprintf('%010d', $expire ? $expire + time() : 0);
	for ($i = 0; $i < $len; $i++) {
		$str .= chr(ord(substr($data,$i,1)) + (ord(substr($char,$i,1)))%256);
	}
	return str_replace('=', '', base64_encode($str));
}

/**
 * 系统解密方法
 * @param string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param string $key  加密密钥
 * @return string 
 */
function think_ucenter_decrypt($data, $key){
	$key    = md5($key);
	$x      = 0;
	$data   = base64_decode($data);
	$expire = substr($data, 0, 10);
	$data   = substr($data, 10);
	if($expire > 0 && $expire < time()) {
		return '';
	}
	$len  = strlen($data);
	$l    = strlen($key);
	$char = $str = '';
	for ($i = 0; $i < $len; $i++) {
		if ($x == $l) $x = 0;
		$char  .= substr($key, $x, 1);
		$x++;
	}
	for ($i = 0; $i < $len; $i++) {
		if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
			$str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
		}else{
			$str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
		}
	}
	return base64_decode($str);
}

/**
 * 获取客户端IP地址
 * @param integer $type 返回类型 0 返回IP地址 1 返回IPV4地址数字
 * @param boolean $adv 是否进行高级模式获取（有可能被伪装） 
 * @return mixed
 */
function get_client_ip($type = 0,$adv=false) {
    $type       =  $type ? 1 : 0;
    static $ip  =   NULL;
    if ($ip !== NULL) return $ip[$type];
    if($adv){
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr    =   explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos    =   array_search('unknown',$arr);
            if(false !== $pos) unset($arr[$pos]);
            $ip     =   trim($arr[0]);
        }elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip     =   $_SERVER['HTTP_CLIENT_IP'];
        }elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip     =   $_SERVER['REMOTE_ADDR'];
        }
    }elseif (isset($_SERVER['REMOTE_ADDR'])) {
        $ip     =   $_SERVER['REMOTE_ADDR'];
    }
    // IP地址合法验证
    $long = sprintf("%u",ip2long($ip));
    $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = 'children', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 创建数据表
 * @param  $module 模块
 */
function create_tables($module)
{
	$sql_file=APP_PATH.$module.'/install/install.sql';
	
	//没有安装文件	
	if(!is_file($sql_file)){
		return ['fail'=>'失败'];   
	}
	
    //读取SQL文件
    $sql = file_get_contents($sql_file);
    $sql = str_replace("\r", "\n", $sql);
    $sql = explode(";\n", $sql);
    //替换表前缀
    $orginal = 'osc_';
	$prefix=config('database.prefix');
    $sql = str_replace(" `{$orginal}", " `{$prefix}", $sql);
    //开始安装 
    foreach ($sql as $value) {
        $value = trim($value);
        if (empty($value)) continue;
		//创建数据表
        if (substr($value, 0, 12) == 'CREATE TABLE') {          
            if (false== Db::execute($value)) {           
            	return ['fail'=>'失败'];          
            }
        } else {
            Db::execute($value);
        }
    }
	return ['success'=>'安装成功'];
}

/**
 * 数组写入文件
 * $path 路径 string
 * $data 数据 array 
 */
function write_to_file($path,$data=array()){
	file_put_contents($path,var_export($data,true));
}
