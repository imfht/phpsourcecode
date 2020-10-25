<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Plugin_weixin extends CI_Controller {

	var $whitelist;

	function __construct() {
		$this->whitelist = "index,login,wxauth,openauth,loginauth";
		parent::__construct ();
		$this->load->model ( 'weixin_setting_model' );
		$this->load->model ( 'weixin_info_model' );
		$this->load->model ( 'user_model' );
	}

	function index() {
		exit ( "Access Denied" );
	}

	function login() {

		require FCPATH . 'lib' . DIRECTORY_SEPARATOR . 'php' . DIRECTORY_SEPARATOR . 'jssdk.php';
		$wx = $this->weixin_setting_model->get ();

		if (empty ( $wx ['appsecret'] ) || empty ( $wx ['appid'] )) {
			exit ( "公众号配置中 appid和appsecret没有填写，创建菜单必须认证公众号!" );
		}

		$appid = $wx ['appid'];
		$appsecret = $wx ['appsecret'];

		$weixin = new JSSDK ( $appid, $appsecret );

		if (isset ( $_GET ['code'] )) {
			$oauth2Url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=$appid&secret=$appsecret&code=" . $_GET ['code'] . "&grant_type=authorization_code";

			$oauth2 = $this->getJson ( $oauth2Url );

			//   $res = $weixin->https_request($url);


			//  $res=(json_decode($res, true));
			$access_token = $oauth2 ["access_token"];
			$refresh_token = $oauth2 ["refresh_token"];
			$openid = $oauth2 ['openid'];
			$get_user_info_url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=$access_token&openid=$openid&lang=zh_CN";
			// $row=$weixin->get_user_info($res['openid']);
			$userinfo = $this->getJson ( $get_user_info_url );

			if ($userinfo ['errcode'] == '40001' || $oauth2 ['errcode'] == '40029') {
				$refreshtoken = "https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=$appid&grant_type=refresh_token&refresh_token=$refresh_token ";
				$oauth2 = $this->getJson ( $refreshtoken );

				$access_token = $oauth2 ["access_token"];

				$openid = $oauth2 ['openid'];
				$get_user_info_url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN ";
				// $row=$weixin->get_user_info($res['openid']);
				$userinfo = $this->getJson ( $get_user_info_url );
			}
             
			if ($userinfo ['openid']) {
				$model = $this->weixin_info_model->f_get ( $userinfo ['openid'] );
				if ($model) {
					$this->weixin_info_model->f_update ( $userinfo );
				} else {
					$this->weixin_info_model->f_insert ( $userinfo );
				}
				//如果存在登录用户就直接绑定
                if($this->user['uid']){
                	$this->db->set ( 'openid',$userinfo['openid']  );
                	$this->db->where_in ( 'uid', $this->user ['uid'] );
                	$this->db->update ( 'user' );
                	$this->message ( '绑定成功!', 'user/mycategory' );
                	exit();
                }
				$_tmp_user = $this->user_model->get_by_openid ( $userinfo ['openid'] );

				$hduid = $_tmp_user ['uid'];
				if (! $_tmp_user) {
                  if($this->setting['weixinregset']){
						//如果开启了扫码直接注册
						$openid=$userinfo['openid'] ;
						$nickname = preg_replace_callback('/./u',function(array $match){
							return strlen($match[0]) >= 4 ? '' : $match[0];
						},$userinfo['nickname']);
							
							$nickname=trim($nickname);
							if(!empty($nickname)){
								$tempusernickname=$this->db->get_where('user',array('username'=>$nickname))->row_array();
								
								
							}
							
							$insertuser=array(
									'username'=>$nickname,
									'password'=>md5(strtolower(random(8))),
									'openid'=>$openid,
									'regtime'=>time(),
									'lastlogin'=>time(),
									'regip'=>getip()
								
									
							);
							$this->db->insert('user',$insertuser);
							$hduid=$this->db->insert_id();
							$this->credit ( $hduid, $this->setting ['credit1_register'], $this->setting ['credit2_register'] ); // 注册增加积分
							if(empty($nickname)){
								$nickname="用户".$hduid;
								$this->db->where(array('uid'=>$hduid))->update('user',array('username'=>$nickname));
							}
							if($tempusernickname){
								$nickname=$nickname.$hduid;
								$this->db->where(array('uid'=>$hduid))->update('user',array('username'=>$nickname));
							}
							$this->load->model ( "doing_model" );
							$this->doing_model->add ( $hduid, $nickname, 12, $hduid, "欢迎您注册了".$this->setting ['site_name'] );
							$hduid = intval ( $hduid );
							
							$avatardir = "/data/avatar/";
							$extname = 'jpg';
							$upload_tmp_file = FCPATH . '/data/tmp/user_avatar_' . $hduid . '.' . $extname;
							$hduid = abs ( $hduid );
							$hduid = sprintf ( "%09d", $hduid );
							$dir1 = $avatardir . substr ( $hduid, 0, 3 );
							$dir2 = $dir1 . '/' . substr ( $hduid, 3, 2 );
							$dir3 = $dir2 . '/' . substr ( $hduid, 5, 2 );
							(! is_dir ( FCPATH . $dir1 )) && forcemkdir ( FCPATH . $dir1 );
							(! is_dir ( FCPATH . $dir2 )) && forcemkdir ( FCPATH . $dir2 );
							(! is_dir ( FCPATH . $dir3 )) && forcemkdir ( FCPATH . $dir3 );
							
							$smallimg = $dir3 . "/small_" . $hduid . '.' . $extname;
							$smallimgdir = $dir3 . "/";
							// get_remote_image($userinfo['headimgurl'],FCPATH . $smallimgdir."small_" . $hduid . '.' . $extname);
							$this->getImage($userinfo['headimgurl'],"small_" . $hduid . '.' . $extname, FCPATH . $smallimgdir, array('jpg','jpeg','png', 'gif'));
							
					}else{
                    	$rurl = url("account/bindregister");

					header ( "Location:$rurl" );
                  }
				

		// $hduid=$_ENV['user']->weixinadd($userinfo['nickname'],'123456',$userinfo['openid']);
				}
	
				$cookietime = 2592000;
				$this->user_model->refresh ( $hduid, 1, $cookietime );

				$this->message ( '登陆成功!', 'index' );
			} else {
				$this->message ( '授权出错,请重新授权!' );
			}

		} else {

			$this->message ( '授权出错,没有CODE,请重新授权!' );
		}
	}
	//pc端扫码
	function openauth() {
		$appid = $this->setting['wechat_appid'];
		$url = url ( "plugin_weixin/loginauth" );
		$url = "https://open.weixin.qq.com/connect/qrconnect?appid=$appid&redirect_uri=$url&response_type=code&scope=snsapi_login&state=1&connect_redirect=1#wechat_redirect";

		header ( 'location:' . $url );
	}
	function loginauth() {
		$code = $_GET ['code'];
		$state = $_GET ['state'];
		//换成自己的接口信息
		$appid = $this->setting['wechat_appid'];
		$appsecret = $this->setting['wechat_appSecret'];
		if (empty ( $code ))
			$this->error ( '授权失败' );
		$token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=' . $appid . '&secret=' . $appsecret . '&code=' . $code . '&grant_type=authorization_code';
		$token = json_decode ( file_get_contents ( $token_url ) );
		if (isset ( $token->errcode )) {
			echo '<h1>错误：</h1>' . $token->errcode;
			echo '<br/><h2>错误信息：</h2>' . $token->errmsg;
			exit ();
		}
		$access_token_url = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=' . $appid . '&grant_type=refresh_token&refresh_token=' . $token->refresh_token;
		//转成对象
		$access_token = json_decode ( file_get_contents ( $access_token_url ) );
		if (isset ( $access_token->errcode )) {
			echo '<h1>错误：</h1>' . $access_token->errcode;
			echo '<br/><h2>错误信息：</h2>' . $access_token->errmsg;
			exit ();
		}
		$user_info_url = 'https://api.weixin.qq.com/sns/userinfo?access_token=' . $access_token->access_token . '&openid=' . $access_token->openid . '&lang=zh_CN';
		//转成对象
		//$user_info = json_decode ( file_get_contents ( $user_info_url ) );
		$user_info = $this->getJson ( $user_info_url );
//		if (isset ( $user_info->errcode )) {
//			echo '<h1>错误：</h1>' . $user_info->errcode;
//			echo '<br/><h2>错误信息：</h2>' . $user_info->errmsg;
//			exit ();
//		}
//
//		$rs = json_decode ( json_encode ( $user_info ), true ); //返回的json数组转换成array数组

        //var_dump($user_info);exit();
		if ($user_info['openid']) {
			$model = $this->weixin_info_model->f_getbyuninid ( $user_info['unionid'] );
			if ($model) {
				$this->weixin_info_model->f_update ( $user_info );
			} else {
				$this->weixin_info_model->f_insert ( $user_info );
			}

			$_tmp_user = $this->user_model->get_by_wechatopenid ( $user_info['openid']);

			$hduid = $_tmp_user ['uid'];
			if (! $_tmp_user) {
              if($this->setting['weixinregset']){
						//如果开启了扫码直接注册
						$openid=$user_info['openid'] ;
						$nickname = preg_replace_callback('/./u',function(array $match){
							return strlen($match[0]) >= 4 ? '' : $match[0];
						},$user_info['nickname']);
							
							$nickname=trim($nickname);
							if(!empty($nickname)){
								$tempusernickname=$this->db->get_where('user',array('username'=>$nickname))->row_array();
								
								
							}
							
							$insertuser=array(
									'username'=>$nickname,
									'password'=>md5(strtolower(random(8))),
									'openid'=>$user_info['openid'],
									'regtime'=>time(),
									'lastlogin'=>time(),
									'regip'=>getip()
								
									
							);
							$this->db->insert('user',$insertuser);
							$hduid=$this->db->insert_id();
							$this->credit ( $hduid, $this->setting ['credit1_register'], $this->setting ['credit2_register'] ); // 注册增加积分
							if(empty($nickname)){
								$nickname="用户".$hduid;
								$this->db->where(array('uid'=>$hduid))->update('user',array('username'=>$nickname));
							}
							if($tempusernickname){
								$nickname=$nickname.$hduid;
								$this->db->where(array('uid'=>$hduid))->update('user',array('username'=>$nickname));
							}
							$this->load->model ( "doing_model" );
							$this->doing_model->add ( $hduid, $nickname, 12, $hduid, "欢迎您注册了".$this->setting ['site_name'] );
							$hduid = intval ( $hduid );
							
							$avatardir = "/data/avatar/";
							$extname = 'jpg';
							$upload_tmp_file = FCPATH . '/data/tmp/user_avatar_' . $hduid . '.' . $extname;
							$hduid = abs ( $hduid );
							$hduid = sprintf ( "%09d", $hduid );
							$dir1 = $avatardir . substr ( $hduid, 0, 3 );
							$dir2 = $dir1 . '/' . substr ( $hduid, 3, 2 );
							$dir3 = $dir2 . '/' . substr ( $hduid, 5, 2 );
							(! is_dir ( FCPATH . $dir1 )) && forcemkdir ( FCPATH . $dir1 );
							(! is_dir ( FCPATH . $dir2 )) && forcemkdir ( FCPATH . $dir2 );
							(! is_dir ( FCPATH . $dir3 )) && forcemkdir ( FCPATH . $dir3 );
							
							$smallimg = $dir3 . "/small_" . $hduid . '.' . $extname;
							$smallimgdir = $dir3 . "/";
							// get_remote_image($userinfo['headimgurl'],FCPATH . $smallimgdir."small_" . $hduid . '.' . $extname);
							$this->getImage($user_info['headimgurl'],"small_" . $hduid . '.' . $extname, FCPATH . $smallimgdir, array('jpg','jpeg','png', 'gif'));
							
					}else{
                $token_arr ['access_token'] = $token;
				$token_arr ['uid'] = $user_info ['openid'];
				$token_arr ['username'] = $user_info ['nickname'];
				$token_arr ['gender'] = $user_info ['sex'];
				$token_arr ['type'] = 'weixin';
				session_start();
				unset ( $_SESSION ['authinfo'] );
				$_SESSION ['authinfo'] = $token_arr;
				header ( "Location:" . url ( 'user/login' ) . "?oauth_provider=WeixinProvider&code=" . time () );
				exit ();
                
              }
				
			}
			$hduid = intval ( $hduid );
			$cookietime = 2592000;
			$this->user_model->refresh ( $hduid, 1, $cookietime );

			$this->message ( '登陆成功!', 'index' );
			exit();
		}

		//打印用户信息
		echo '<pre>';
		echo '</pre>';
	}
	function wxauth() {
		$wx = $this->weixin_setting_model->get ();

		if (empty ( $wx ['appsecret'] ) || empty ( $wx ['appid'] )) {
			exit ( "公众号配置中 appid和appsecret没有填写，创建菜单必须认证公众号!" );
		}

		$appid = $wx ['appid'];
		$appsecret = $wx ['appsecret'];
		$rurl = url("plugin_weixin/login");
		$url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=$appid&redirect_uri=$rurl&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";

		header ( "Location:$url" );
	}
	function getImage($url, $filename = '', $dirName, $fileType, $type = 0) {
		if ($url == '') {
			return false;
		}
		//获取文件原文件名
		$defaultFileName = basename ( $url );
		
		
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		$file = curl_exec($ch);
		curl_close($ch);
		
		if (! file_exists ( $dirName )) {
			mkdir ( $dirName, 0777, true );
		}
		
		
		$resource = fopen($dirName . $filename, 'a');
		fwrite($resource, $file);
		fclose($resource);
	}
	function getJson($url) {
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 500 );
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。


		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt ( $curl, CURLOPT_URL, $url );

		$res = curl_exec ( $curl );
		curl_close ( $curl );

		return json_decode ( $res, true );
	}

}

?>