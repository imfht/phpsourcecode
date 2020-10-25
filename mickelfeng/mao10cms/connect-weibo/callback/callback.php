<?php
session_start();
include_once( THINK_PATH.'../connect-weibo/config.php' );
include_once( THINK_PATH.'../connect-weibo/saetv2.ex.class.php' );

$o = new SaeTOAuthV2( WB_AKEY , WB_SKEY );

if (isset($_REQUEST['code'])) {
	$keys = array();
	$keys['code'] = $_REQUEST['code'];
	$keys['redirect_uri'] = WB_CALLBACK_URL;
	try {
		$token = $o->getAccessToken( 'code', $keys ) ;
	} catch (OAuthException $e) {
	}
}

if ($token) {
	$_SESSION['token'] = $token;
	setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
	
	$c = new SaeTClientV2( WB_AKEY , WB_SKEY , $_SESSION['token']['access_token'] );
	$ms  = $c->home_timeline(); // done
	$uid_get = $c->get_uid();
	$uid = $uid_get['uid'];
	$user_message = $c->show_user_by_id( $uid);//根据ID获取用户等基本信息
	$page_id = M('meta')->where("meta_key='user_wboid' AND meta_value='".$uid."' AND type='user'")->getField('page_id');
	if($page_id) :
		$user_name = mc_get_meta($page_id,'user_name',true,'user');
		$user_pass_true = mc_get_meta($page_id,'user_pass',true,'user');
		cookie('user_name',$user_name,36000000000);
		cookie('user_pass',$user_pass_true,36000000000);
		$this->success('登陆成功',mc_option('site_url').'?m=user&c=index&a=edit&id='.mc_user_id());
	else :
		function mc_check_user_name($name) {
			$user_login = M('meta')->where("meta_key='user_login' AND type ='user'")->getField('meta_value',true);
		    if(in_array($name, $user_login)) :
		    	return true;
			else :
				return false;
		    endif;
		};
	    do {
			$user_name_test = $uid.rand(1000,9999);
		}
		while (mc_check_user_name($user_name_test));
		if($user_message['screen_name']) {
			$user_id = M('page')->where("title='".$user_message['screen_name']."' AND type ='user'")->getField('id',true);
		    if($user_id) :
		    	$user['title'] = $user_name_test;
			else :
				$user['title'] = $user_message['screen_name'];
		    endif;
		} else {
			$user['title'] = $user_name_test;
		}
		$user['content'] = '';
		$user['type'] = 'user';
		$user['date'] = strtotime("now");
		$result = M("page")->data($user)->add();
		if($result) :
			mc_add_meta($result,'user_name',$user_name_test,'user');
			$user_pass = md5($uid.mc_option('site_key'));
			mc_add_meta($result,'user_pass',$user_pass,'user');
			mc_add_meta($result,'user_wboid',$uid,'user');
			mc_add_meta($result,'user_level','1','user');
		    cookie('user_name',$user_name_test,36000000000);
			cookie('user_pass',$user_pass,36000000000);
			$this->success('登陆成功',mc_option('site_url').'?m=user&c=index&a=edit&id='.mc_user_id());
		else :
			$this->error('登陆失败');
		endif;
	endif;
} else {
?>
授权失败。
<?php
}
?>
