<?php
// 应用公共文件

/**
 * 函数：单图上传
 * @param string            表单名
 * @param number            宽度
 * @param number            高度
 * @param string            高度
 * @return string           显示图的URL
 * @return string           上传图的URL
 */
function UploadImage($name="image",$width=100,$height=100,$url=''){
	echo '<iframe scrolling="no" frameborder="0" border="0" onload="this.height=this.contentWindow.document.body.scrollHeight;this.width=this.contentWindow.document.body.scrollWidth;" width='.$width.' height="'.$height.'"  src="'.url('admin/upload/uploadpic',['name'=>$name,'width'=>$width,'height'=>$height,'url'=>base64_encode($url)]).'"></iframe><input type="hidden" name="'.$name.'" id="'.$name.'">';
}

/**
 * 函数：多图上传
 * @param string            表单名
 * @return string           显示图的URL
 * @return string           上传图的URL
 */
function UploadImages($name="image",$url=""){
    echo '<iframe scrolling="no" frameborder="0" border="0" onload="this.height=this.contentWindow.document.body.scrollHeight;this.width=this.contentWindow.document.body.scrollWidth;" src="'.url('admin/upload/uploadpics',['name'=>$name,'url'=>base64_encode($url)]).'"></iframe><input type="hidden" name="'.$name.'" id="'.$name.'">';
}

/**
 * 函数：加密
 * @param string            密码
 * @return string           加密后的密码
 */
function password($password){
	/*
	*后续整强有力的加密函数
	*/
	return md5('~Chun!'.$password.'!Yan~');

}

/**
 * 随机字符
 * @param number $length 长度
 * @param string $type 类型
 * @param number $convert 转换大小写
 * @return string
 */
function random($length=6, $type='string', $convert=0){
    $config = array(
        'number'=>'1234567890',
        'letter'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string'=>'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all'=>'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );
    
    if(!isset($config[$type])) $type = 'string';
    $string = $config[$type];
    
    $code = '';
    $strlen = strlen($string) -1;
    for($i = 0; $i < $length; $i++){
        $code .= $string[mt_rand(0, $strlen)];
    }
    if(!empty($convert)){
        $code = ($convert > 0)? strtoupper($code) : strtolower($code);
    }
    return $code;
}

/**
 * 函数：格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '') {
	$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
	for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
	return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 函数：性别获取
 * @param  number $sex      性别
 * @return string           返回性别
 */
function sex($sex=0) {
	if($sex==0){
		return '保密';
	}
	if($sex==1){
		return '男';
	}
	if($sex==2){
		return '女';
	}
}