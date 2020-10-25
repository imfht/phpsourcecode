<?php
/**@name 微信网页认证*/

$_user = new \user\model\_user();

if(empty($_var['op'])){
	require_once ROOTPATH.'/source/lib/QRcode.php';
	
	/**
	 * 用户登录默认使用微信扫码登录
	 * 微信用户认证有两种方式，一种是开放平台，一种是网页授权，我们使用第二种。
	 * 网页授权如有微信公众号接口，后台开启并将相关参数配置好，打开“网页授权认证”
	 */
	
	$salt = get_uuid();
	$_SESSION['_salt'] = $salt;
	
	if($wx_setting['WX_OPEN'] && $wx_setting['WX_AUTH']){
		\QRcode::png("{$setting[SiteHost]}auth.html?op=oauth2&salt={$salt}", '', QR_ECLEVEL_Q);
	}else{
		\QRcode::png("{$setting[SiteHost]}auth.html?op=error&salt={$salt}", '', QR_ECLEVEL_Q);
	}
}elseif($_var['op'] == 'error'){
	exit_html5('微信公众号未开启网页授权认证！');
}elseif($_var['op'] == 'check'){
	if(!$_SESSION['_salt']) exit_json_message('网页授权认证参数错误');
	
	$users = $_user->get_list(0, 1, "AND SALT = '{$_SESSION[_salt]}'");
	if(count($users) > 0){
		$user = $users[0];
		
		$_user->flash_state($user['USERID']);
		
		$_SESSION['_current'] = serialize($user);
		cookie_set('auth_member', str_encrypt($user['USERID'].'|'.$user['PASSWD'].'|'.$user['SALT']), time() + 86400 * 3000);
	}
	
	exit_json(array(
	'message' => '', 
	'success' => count($users) > 0, 
	'userid' => $users[0]['USERID'], 
	'username' => $users[0]['REALNAME'], 
	'photo' => $users[0]['PHOTO']
	));
}elseif($_var['op'] == 'oauth2'){
	if(empty($_var['gp_ref'])){
		if(!$_var['gp_salt'] || !is_uuid($_var['gp_salt'])) exit_html5('微信公众号未开启网页授权认证！');
		$redirect_url = urlencode("{$setting[SiteHost]}auth.html?op=oauth2&salt={$_var[gp_salt]}");
	}else{
		$redirect_url = urlencode("{$setting[SiteHost]}auth.html?op=oauth2&ref={$_var[gp_ref]}");
	}
	
	if(!check_weixin()) exit_html5('请打开微信，使用“扫一扫”访问！');
	
	if(empty($_var['gp_code'])){
		header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wx_setting[APPID]}&redirect_uri={$redirect_url}&response_type=code&scope=snsapi_userinfo&state={$_var[ac]}#wechat_redirect");
		exit(0);
	}
	
	if(empty($_var['gp_code'])){
		header("location:https://open.weixin.qq.com/connect/oauth2/authorize?appid={$wx_setting[APPID]}&redirect_uri={$redirect_url}&response_type=code&scope=snsapi_userinfo&state={$_var[ac]}#wechat_redirect");
		exit(0);
	}
	
	$_wx = new \wx\model\_wx();
	$_wx_fans = new \wx\model\_wx_fans();
	$_group = new \user\model\_group();
	$_third = new \user\model\_third();
	
	$rtn = $_wx->request("https://api.weixin.qq.com/sns/oauth2/access_token?appid={$wx_setting[APPID]}&secret={$wx_setting[APPSECRET]}&code={$_var[gp_code]}&grant_type=authorization_code");
	if(empty($rtn)) exit_html5('微信公众号参数错误！');
	
	$rtn = json_decode($rtn, 1);
	if($rtn['errcode']) exit_html5('同意授权后访问！');
	
	$apitxt = $_wx->request("https://api.weixin.qq.com/sns/userinfo?access_token={$rtn[access_token]}&openid={$rtn[openid]}&lang=zh_CN");
	if(empty($apitxt)) exit_html5('微信API参数错误！');
	
	$apitxt = json_decode($apitxt, 1);
	if(!$apitxt || !$apitxt['openid']) exit_html5('微信API参数错误！');
	
	$wx_fans = $_wx_fans->get_by_openid($apitxt['openid']);
	if(!$wx_fans){
		$wx_fans = array(
		'WXID' => 1, 
		'SUBSCRIBE' => $apitxt['subscribe'],
		'OPENID' => $apitxt['openid'],
		'NICKNAME' => str_replace("'", '’', $apitxt['nickname']),
		'SEX' => $apitxt['sex'],
		'CITY' => $apitxt['city'],
		'COUNTRY' => $apitxt['country'],
		'PROVINCE' => $apitxt['province'],
		'LANGUAGE' => $apitxt['language'],
		'HEADIMGURL' => $apitxt['headimgurl'],
		'SUBSCRIBE_TIME' => $apitxt['subscribe_time']
		);
		
		$wx_fansid = $_wx_fans->insert($wx_fans);
		$wx_fans['WX_FANSID'] = $wx_fansid;
		
		third_insert($apitxt['openid'], 'wx');
	}else{
		$_wx_fans->update($wx_fans['WX_FANSID'], array(
		'SUBSCRIBE' => $apitxt['subscribe'],
		'NICKNAME' => str_replace("'", '’', $apitxt['nickname']),
		'SEX' => $apitxt['sex'],
		'CITY' => $apitxt['city'],
		'COUNTRY' => $apitxt['country'],
		'PROVINCE' => $apitxt['province'],
		'LANGUAGE' => $apitxt['language'],
		'HEADIMGURL' => $apitxt['headimgurl'],
		'SUBSCRIBE_TIME' => $apitxt['subscribe_time']
		));
		
		$wx_fans['NICKNAME'] = str_replace("'", '’', $apitxt['nickname']);
		$wx_fans['HEADIMGURL'] = $apitxt['headimgurl'];
	}
	
	$user = $_user->get_by_fansid($wx_fans['WX_FANSID']);
	!$user && $user = $_user->get_by_name($wx_fans['OPENID']);
	
	if(!$user){
		$groups = array_values($_group->get_all());
		
		$user = array(
		'USERNAME' => $wx_fans['OPENID'], 
		'REALNAME' => $wx_fans['NICKNAME'] ? $wx_fans['NICKNAME'] : "微信会员({$wx_fans[WX_FANSID]})", 
		'EMAIL' => '', 
		'PASSWD' => md5($wx_fans['OPENID']), 
		'CREATETIME' => date('Y-m-d H:i:s'), 
		'COMMENT' => '微信登录', 
		'ISMANAGER' => 0, 
		'ISAUDIT' => 0, 
		'GROUPID' => count($groups) > 0 ? $groups[0]['GROUPID'] : 0, 
		'LOGINTIME' => date('Y-m-d H:i:s'), 
		'SALT' => $_var['gp_salt'], 
		'PHOTO' => $wx_fans['HEADIMGURL'], 
		'WX_FANSID' => $wx_fans['WX_FANSID']
		);
		
		$userid = $_user->insert($user);
		$user['USERID'] = $userid;
	}else{
		$user['USERNAME'] = $wx_fans['OPENID'];
		$user['REALNAME'] = $wx_fans['NICKNAME'] ? $wx_fans['NICKNAME'] : "微信会员({$wx_fans[WX_FANSID]})";
		$user['PHOTO'] = $wx_fans['HEADIMGURL'];
		
		$_user->update($user['USERID'], array(
		'USERNAME' => $user['USERNAME'], 
		'REALNAME' => $user['REALNAME'], 
		'PHOTO' => $user['PHOTO'],
		'SALT' => $_var['gp_salt']
		));
	}
	
	//第三方登录
	$third = $_third->get_by_id($wx_fans['OPENID'], 'wx');
	if(!$third) $_third->insert($wx_fans['OPENID'], 'wx');
	else $_third->update($wx_fans['OPENID'], 'wx', $user['USERID']);
	
	$refarr = explode('|', $_var['gp_ref']);
	
	//手机访问时跳转
	if(count($refarr) == 3){
		$_SESSION['_current'] = serialize($user);
		set_cookie('auth_member', str_encrypt($user['USERID'].'|'.$user['PASSWD'].'|'.$user['SALT']), time() + 86400 * 3000);
		
		header("location:{$refarr[0]}.html?op={$refarr[1]}&id={$refarr[2]}");
		exit(0);
	}
	
	include_once view('/tpl/_cms/view/pc_oauth2');
}
?>