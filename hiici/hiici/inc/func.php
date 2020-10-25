<?php

define('SYS_ENCODE', 'utf-8');

//info
function put_info($info) {
	$_SESSION['sys_info'] = $info;
}
function get_info() {
	if (!empty($_SESSION['sys_info'])) {
		print '<script> alert(\''.$_SESSION['sys_info'].'\') </script>';
		$_SESSION['sys_info'] = null;
	}
}
//遇到问题需要跳转
function info_jump($info, $url) { 
	put_info($info); 
	header('Location:'.$url); 
	die(); 
}
//上传文件
function do_upload_file($f, $dir, $type, $size = 1000000, $cover = false) {
	if (empty($f)) die('空文件');

	if (!in_array($f['type'], $type) || $f['size'] > $size) {
		echo '违规文件！';
		return false;
	}
	if ($f['error'] > 0) {
		echo 'Return Code: ' . $f['error'] . '<br />';
		return false;
	} 
	if (!$cover && file_exists($dir.$f['name'])) {
		echo $f['name'] . ' 已经存在！ ';
		return false;
	} 
	move_uploaded_file($f['tmp_name'], $dir.$f['name']);
	return true;
}
//删除非空文件夹
function do_rmdir($dir) {
	if (!file_exists($dir)) return true;

	$dh = opendir($dir);
	while ($file = readdir($dh)) {
		if ($file != "." && $file != "..") {
			$f_p = $dir . "/" . $file;
			if (!is_dir($f_p)) {
				unlink($f_p);
			} else {
				do_rmdir($f_p);
			}
		}
	}
	closedir($dh);
	if (!rmdir($dir)) return false;

	return true;
}
//格式化时间戳
function fmt_date($tm) {
	return date('Y-m-d H:i:s',$tm);
}
//系统信息
function msg_sys_add($msg_sys, $to_user_id) {
	$rs = dt_query("INSERT INTO msg_sys (content, to_user_id, c_at) VALUES ('$msg_sys', $to_user_id, ".time().")");
	if (!$rs) return false;
	$rs = dt_query("UPDATE user_info SET msg_sys_c = msg_sys_c + 1 WHERE id = $to_user_id");
	if (!$rs) return false;
	return true;
}
//限制句字数
function get_substr($tx, $len = 10) {
	$str = strip_tags($tx);
	if (empty($str)) return '...';
	if (preg_match("/^[\w\s\.\[\]\(\)\^%,:;]+$/", $str)) $len *= 2;		//全为英文字符长度加长

	if ($len >= mb_strlen($str, SYS_ENCODE)) return $str;

	return mb_substr($str, 0, $len, SYS_ENCODE).'...';
}
//限制数大小
function get_n($num, $max = 100) {
	if ($num < 100) return $num;
	return 'n';
}
//分页
function pagination($table, $cond, $page, $limit, $p_before, $p_after = null) {
	$page_c = ceil(dt_count($table, $cond) / $limit);
	$pages = array();
	for ($i = $page - 2; $i <= $page + 2; $i++) { if ($i >= 1 && $i <= $page_c) { $pages[$i] = $i; } }
?>
	<ul class="pagination">
		<li> <a href="<?php echo p_s_url($p_before.($page - 1).$p_after) ?>">上一页</a> </li>
		<?php if (!empty($pages)) { ?>
		<?php if (!in_array(1, $pages)) { ?> <li> <a href="<?php echo p_s_url($p_before.'1'.$p_after) ?>">1...</a> </li> <?php } ?>
		<?php foreach ($pages as $p) { ?>
		<li <?php if ($p == $page) { ?> class="active" <?php } else { ?> class="hidden-xs" <?php } ?> > <a href="<?php echo p_s_url($p_before.$p.$p_after) ?>"><?php echo $p ?></a> </li>
		<?php } ?>
		<?php if (!in_array($page_c, $pages)) { ?> <li class="hidden-xs"> <a href="<?php echo p_s_url($p_before.$page_c.$p_after) ?>">...<?php echo $page_c ?></a> </li> <?php } ?>
		<?php } ?>
		<li> <a href="<?php echo p_s_url($p_before.(($page + 1 > $page_c) ? $page_c : $page + 1).$p_after) ?>">下一页</a> </li>
	</ul>
<?php
	require_once('lib/pagination_key.html');
}
//url是否有效
function is_url($url) {
	return (@preg_grep('/200/', get_headers($url))) ? true : false;
}

/**
 * 验证Email
 * @param unknown $email
 * @return boolean
 */
function is_email($email) {
	return (preg_match('/^[A-Za-z0-9]+([._\-\+]*[A-Za-z0-9]+)*@([A-Za-z0-9-]+\.)+[A-Za-z0-9]+$/' ,$email)) ? true : false;
}
//发电子邮件
function send_email($email, $subject, $body) {
	require_once('inc/phpmailer/PHPMailerAutoload.php');
	$mail = new PHPMailer;

	global $config;
	$mail->isSMTP();                                      // Set mailer to use SMTP
	$mail->Host = $config['mail_host'];  // Specify main and backup SMTP servers
	$mail->SMTPAuth = true;                               // Enable SMTP authentication
	$mail->Username = $config['mail_username'];                 // SMTP username
	$mail->Password = $config['mail_password'];                           // SMTP password
	$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

	$mail->CharSet = 'utf-8'; 	//字符集
	$mail->From = $config['mail_from'];
	$mail->FromName = $config['mail_fromname'];
	$mail->addAddress($email);               // Name is optional

	$mail->WordWrap = 50;                                 // Set word wrap to 50 characters
	$mail->isHTML(true);                                  // Set email format to HTML

	$mail->Subject = $subject;
	$mail->Body    = $body;

	if(!$mail->send()) {
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		return false;
	}

	return true;
}
//内部支付函数
function do_topic_up_pay($user_id, $des) {
	global $config;
	if (!$config['t_u_pay']) return true;

	if ($_SESSION['auth']['id'] == $user_id) return false;
		
	require_once('forum/inc/forum_city.php');
	if (!do_account_pay($user_id, $config['t_u_pay'], $des, 1, $forum_city)) return false; 
	return true;
}
function account_pay_pre_check($password, $pay) {
	$auth = @$_SESSION['auth'];
	if (empty($auth)) die('用户未登录！');

	if (1 > dt_count('account', "WHERE id = ".$auth['id']." AND password = '".$password."'")) return -1;
	if (1 > dt_count('account', "WHERE id = ".$auth['id']." AND $ >= $pay")) return -2;

	return 1;
}
function account_pay($pay, $des, $i_o, $city = null) {
	$pay = doubleval($pay);
	$des = filter_var($des, FILTER_SANITIZE_STRING);
	$i_o = intval($i_o);
	$city = intval($city);

	$auth = $_SESSION['auth'];
	if (empty($auth)) die('用户未登录！');

	if (!do_account_pay($auth['id'], $pay, $des, $i_o, $city)) return false; 

	return true;
}
function do_account_pay($user_id, $pay, $des, $i_o, $city = 0) {
	$rs = dt_query("UPDATE account SET $ = $ ".((1 == $i_o) ? '+' : '-')." $pay, m_at = ".time()." WHERE id = ".$user_id);
	if (!$rs) return false; 

	$rs = dt_query("INSERT INTO account_log (user_id, m_$, r_$, des, i_o, city, c_at) VALUES ($user_id, $pay, (SELECT $ FROM account WHERE id = $user_id), '$des', $i_o, $city, ".time().")");
	if (!$rs) return false; 

	return true;
}
//构造伪静态URL
function p_s_url($s_u) {
	if (preg_match('/\?c=/', $s_u)) return s_url($s_u);
	return $s_u;
}
function s_url($s_u) {
	global $config;
	if ($config['s_url']) {
		$s_u = preg_replace('/\?c=|a=/', '', $s_u);
		$s_u = preg_replace('/&|=/', '-', $s_u, 7);
		$s_u = 'http://'.$_SERVER['HTTP_HOST'].'/'.$s_u.'.htm';
	} 

	return $s_u;
}
//获取内容中第一个图片链接
function get_img_url($content) {
	if (!preg_match('/<img[^>]*src="(http:\/\/[^"]*)"[^>]*>/i', $content, $m)) return false;
	return filter_var($m[1], FILTER_SANITIZE_STRING);
}
//给图片加水印
function img_water_mark($bg_img_f, $w_img_f) {
	require_once('inc/lib/water_mark.php');
	return water_mark($bg_img_f, $w_img_f);
}
//跳到登录页
function to_login($b_url = null) { 
	if (null != $b_url) setcookie('login_jump', $b_url, time()+365, '/'); 
	header('Location:'.s_url('?c=user&a=login')); 
	die(); 
}
//判断资金账户收货地址完整
function check_account_addr($user_id) {
	if (!dt_query_one("SELECT id FROM account_addr WHERE name !='' AND addr != '' AND p_code != '' AND phone != '' AND id = $user_id LIMIT 1")) return false;
	return true;
}
//重置网页标题 For SEO
function reset_title($title, $desc = null) {
	$ob_c = ob_get_contents();
	ob_clean();

	if (null != $desc) $ob_c = preg_replace('/<meta name="Description" content="[^"]*" \/>/i', '<meta name="Description" content="'.$desc.'" \/>', $ob_c);
	else $ob_c = preg_replace('/<meta name="Description" content="[^"]*" \/>/i', '<meta name="Description" content="'.$title.'" \/>', $ob_c);

	$ob_c = preg_replace('/<meta name="Keywords"  content="[^"]*" \/>/i', '<meta name="Keywords"  content="'.$title.'" \/>', $ob_c);
	echo preg_replace('/<title>[^<]*<\/title>/i', '<title>'.$title.'</title>', $ob_c);
}	
//清除记住密码
function clean_remember_password() {
	//setcookie('user_username', '', time()-3600, '/');
	setcookie('user_password', '', time()-3600, '/');
}
//随机颜色
function rand_color() {
	$c_a = array('B40404', 'DF7401', '868A08', '088A4B', '084B8A', '4B088A', '8A0886', '424242', '0B0B0B');
	return $c_a[mt_rand(0, 8)];
}
//发布说说
function shuo_add($content, $user_id, $user_name) {
	if (!dt_query("INSERT INTO shuo (content, user_id, user_name, c_at) VALUES ('$content', '$user_id', '$user_name', ".time().")")) return false;
	if (!dt_query("UPDATE user_info SET shuo_c = shuo_c + 1 WHERE id = $user_id")) return false;
	return true;
}
//是否为QQ用户
function is_qq_user($user_id) {
	if (dt_query_one("SELECT id from user WHERE email is null AND password = '".sha1('QQ')."' AND id = ".$user_id)) return true;
	return false;
}
//设置结束点
function set_end($p, $c) {
	return preg_replace($p, '`', preg_replace('/`/', '"', $c));
}
//随机IP
function file_get_contents_rand_ip($url, $hd = null) {
	$c_ip = mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255).".".mt_rand(1, 255);
	return file_get_contents($url, false, stream_context_create(array('http'=>array('header'=>$hd."X-FORWARDED-FOR:$c_ip\r\nCLIENT-IP:$c_ip\r\nX-Real-IP:$c_ip\r\n"))));
}
