<?php
/*
*	检测登录
*	位置:AdminBase/_initialize
*/
function is_login(){
	if($admin = session('admin_info')){
		$uid = $admin['id'];
	}elseif($token = cookie('admin_token')){
		$uid = D('Admin')->adminToken('check');
	}else{
        return false;
    }
	return intval($uid);
}
/*
*	检测是否为超级管理员
*	位置:AdminBase/_initialize
*/
function is_super($uid){
	if(!isset($uid) && !is_int($uid)) return false;
	return ($uid === C('ADMIN_SUPER'));
}

//检测Auth中允许访问的节点
function check_auth($rule,$type=1){
    static $Auth    =   null;
    if (!$Auth) {
        $Auth       =   new \Think\Auth();
    }
    if(!$Auth->check($rule,UID,$type)){
        return false;
    }
    return true;
}
/**
 * [action_log 通过动态配置开启关闭日志功能]
 * @param  string  $action [操作]
 * @param  integer $type   [状态]
 */
function action_log($action='',$type=0,$uid=0){
	$log_status=array(
        'login'=>true,
    );
	if($log_status[$action]){
		D('AdminLog')->log($action,$type,$uid);
	}
	//错误操作相关人员提醒
	if(!$type){

	}
	return true;
}



/*
*   删除指定目录下所有文件
*   位置:Index/delCache
*/
function del_dir($dir){
    if (!is_dir($dir)){
        return false;
    }
    $handle = opendir($dir);
    while (($file = readdir($handle)) !== false){
        if ($file != "." && $file != ".."){
            is_dir("$dir/$file")?del_dir("$dir/$file"):@unlink("$dir/$file");
        }
    }
    if (readdir($handle) == false){
        closedir($handle);
    }
    return true;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}
