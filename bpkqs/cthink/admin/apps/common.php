<?php
/**
 * 随机生成字符串
 */
function rand_string($len = 6, $type = '', $addChars = '') {
	$str = '';
	switch ($type) {
		case 0 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		case 1 :
			$chars = str_repeat ( '0123456789', 3 );
			break;
		case 2 :
			$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ' . $addChars;
			break;
		case 3 :
			$chars = 'abcdefghijklmnopqrstuvwxyz' . $addChars;
			break;
		default :
			$chars = 'ABCDEFGHIJKMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789' . $addChars;
			break;
	}
	if ($len > 10) {
		$chars = $type == 1 ? str_repeat ( $chars, $len ) : str_repeat ( $chars, 5 );
	}
	if ($type != 4) {
		$chars = str_shuffle ( $chars );
		$str = substr ( $chars, 0, $len );
	} else {
		for($i = 0; $i < $len; $i ++) {
			$str .= msubstr ( $chars, floor ( mt_rand ( 0, mb_strlen ( $chars, 'utf-8' ) - 1 ) ), 1 );
		}
	}
	return $str;
}


//发送post请求
function curl_post($url, $post_data){
	$ch = curl_init();
	curl_setopt($ch,CURLOPT_URL,$url);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch,CURLOPT_POST,1);
	curl_setopt($ch,CURLOPT_POSTFIELDS,$post_data);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  // 从证书中检查SSL加密算法是否存在
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

/**
 * 通过CURL方式获取html页面内容
 * @param $url
 * @return mixed
 */
function curl_get($url){
	//初始化
	$ch = curl_init();
	//设置选项，包括URL
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);  // 从证书中检查SSL加密算法是否存在
	//执行并获取HTML文档内容
	$output = curl_exec($ch);
	//释放curl句柄
	curl_close($ch);
	return $output;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) { 
    // 创建Tree
    $tree = [];
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
 * 全局md5加密方法
 */
function cthink_md5($str){
	$key = 'cthink';
	if(config('enstring')){
		$key = config('enstring');
	}
	return '' === $str ? '' : md5(sha1($str) . $key);
}

/**
 * 获取客户端IP地址
 */
function get_client_ip($type=0) {
	global $ip; 
	if (getenv("HTTP_CLIENT_IP")) 
		$ip = getenv("HTTP_CLIENT_IP"); 
	else if(getenv("HTTP_X_FORWARDED_FOR")) 
		$ip = getenv("HTTP_X_FORWARDED_FOR"); 
	else if(getenv("REMOTE_ADDR")) 
		$ip = getenv("REMOTE_ADDR"); 
	else 
		$ip = "0.0.0.0";
	// IP地址合法验证
	$long = sprintf("%u",ip2long($ip));
	$ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
	return $ip[$type];
}

/**
 * 基于tp5 extend 扩展的插件方法
 */
function addons($aoname,$params){
	return \think\Hook::exec("addons\\{$aoname}\\Addons",'run',$params,$aoname);
}

/**
 * 图片截取处理,宽为0的时候为不截取，寬为0高度设置失效
 * @param int $attach_id:资源id
 * @param int $w 要截取图片的宽度
 * @param int $h 要截取图片的高度
 * @return string 要返回截取后的图片路径
 */
function get_img_url($attach_id,$w = 0,$h = 0){
	$url = '';
	$static_domain = config('url_domain');
	if($attach_id){
		$result = model('Attach')->getAttachById($attach_id);
		if($result ){
			$prefix_url = 'public/uploads/';
			$save_path = $result['save_path'];
			$save_name = $result['save_name'];
			if($w > 0){
				$crop_image = './'.$prefix_url.$save_path.'/'.$save_name;
				$save_image_path = './'.$prefix_url.'/'.$save_path.'/'.$w.'x'.$h.'-'.$save_name;
				//判断要截取的图片是否存在，存在直接返回地址
				if(file_exists($save_image_path)){
					$url = $static_domain.'/'.$prefix_url.$save_path.'/'.$w.'x'.$h.'-'.$save_name;
				}else{
					//判断要截取的图是否存在
					if(file_exists($crop_image)){
						$image = \think\image::open($crop_image);
						if($image->thumb($w, $h,\think\Image::THUMB_FILLED)->save($save_image_path)){
							$url = $static_domain.'/'.$prefix_url.$save_path.'/'.$w.'x'.$h.'-'.$save_name;
						}
					}else{
						$default_image = './public/default/'.$w.'x'.$h.'-default.jpg';
						if(!file_exists($default_image)){
							$image = \think\image::open('./public/default/default.jpg');
							$image->thumb($w, $h,\think\Image::THUMB_FILLED)->save($default_image);
							$url = $static_domain.'/public/default/'.$w.'x'.$h.'-default.jpg';
						}else{
							$url = $static_domain.'/public/default/'.$w.'x'.$h.'-default.jpg';
						}
					}
				}
			}else{
				$url = $static_domain.'/'.$prefix_url.$save_path.'/'.$save_name;
			}
		}else{
			if($w > 0){
				$default_image = './public/default/'.$w.'x'.$h.'-default.jpg';
				if(!file_exists($default_image)){
					$image = \think\image::open('./public/default/default.jpg');
					$image->thumb($w, $h,\think\Image::THUMB_FILLED)->save($default_image);
					$url = $static_domain.'/public/default/'.$w.'x'.$h.'-default.jpg';
				}else{
					$url = $static_domain.'/public/default/'.$w.'x'.$h.'-default.jpg';
				}
			}else{
				$url = $static_domain.'/public/default/default.jpg';
			}
		}
	}
	return $url;
}

/**
 * 判断是否已经登录
 */
function is_login(){
	$return = false;
	$session = session('user_auth');
    if (!empty($session)) {
        $return = $session;
    }
	return $return;
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === config('user_administrator'));
}

/**
 * 菜单管理->获取上级菜单
 */
function get_up_menu($id){
	$up_tree_name = '根菜单';
	if(intval($id) > 0){
		$res = model('Menu')->getFindOne($id);
		$up_tree_name = $res['title'];
	}
	return $up_tree_name;
}

/**
 * 用过管理员id获取用户信息
 */
function get_admin_info($uid,$field = '*'){
	$return = false;
	$admin = model('AuthMember')->getFindOne($uid,$field);
	if($field == '*'){
		$return = $admin;
	}else{
		$return = $admin['nickname'];
	}
	return $return;
}

/**
 * 通过角色id获取角色基本信息
 */
function get_group_info($group_id){
	return model('AuthGroup')->getFindOne($group_id);
}