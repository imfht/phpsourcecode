function qq_login(){
	if(typeof(window.inApk)=='object'){	//套壳APP登录
		window.inApk.app_qq_login(jump_from_url);
	}else if(typeof(api)=="object"){	//仿原生APP登录
		apicloudQqLogin();
	}else{
		window.location.href = qq_login_url;
	}
}

//仿原生APP之QQ登录
function apicloudQqLogin(){
	var qq = api.require('QQPlus');
		qq.login(function(ret, err) {//alert(JSON.stringify(ret))
			if (ret.status) {
				//alert(ret.accessToken+"---"+ret.openId)
				layer.msg("请稍候,正在检验服务器数据...",{time:5000});
				var url = ck_qqlogin_url + "access_token=" + ret.accessToken + "&openid=" + ret.openId;
				$.get(url,function(res){
					if(res.code==1){
						layer.alert(res.msg);
					}else{	//检验资料正确,ajax跨域无法同步登录
						layer.msg("登录成功");
						setTimeout(function(){
							window.location.href = decodeURIComponent(jump_from_url);
						},500);
					}
				});
			} else {
				alert(JSON.stringify(err))
			}
		});

	/*
	var qiboBase = api.require('qiboBase');
	var param = {appParam:"Hello APICloud!"};
	var resultCallback = function(ret, err){
		layer.msg("请稍候,正在检验数据...",{time:5000});
		var url = ck_qqlogin_url + "access_token=" + ret.access + "&openid=" + ret.openid;
		$.get(url,function(res){
			if(res.code==1){
				layer.alert(res.msg);
			}else{	//检验资料正确,ajax跨域无法同步登录
				layer.msg("登录成功");
				setTimeout(function(){
					window.location.href = decodeURIComponent(jump_from_url);
				},500);
			}
		});
	}
	qiboBase.qqLogin(param,resultCallback);
	*/
}

function weixin_login(){
	if(typeof(window.inApk)=='object'){	//套壳APP登录
		window.inApk.app_weixin_login(jump_from_url);
	}else if(typeof(api)=="object"){	//仿原生APP登录
		if(app_login_type==2){	//原生微信APP登录
			apicloud_app_login();
		}else{	//APP借助小程序登录
			apicloud_xcx_login();
		}		
	}else{
		window.location.href = wx_login_url;
	}
}

//仿原生APP借助小程序登录
function apicloud_xcx_login(){
	var wxPlus = api.require('wxPlus');
		wxPlus.launchMiniProgram({
			apiKey: '',
			miniProgramType: 'release',
			userName: xcx_gh_id,
			path: 'pages/wap/login/index',
		}, function(ret, err) {	//alert( JSON.stringify(ret) );
				var token = ret.extMsg;
				if(token==""||token==undefined){
					layer.alert('登录失败');
				}else{
					layer.msg("请稍候,正在检验数据...",{time:5000});
					var url = ck_wxlogin_url + "token=" + token;
					$.get(url,function(res){
						if(res.code==1){
							layer.alert(res.msg);
						}else{	//检验资料正确,ajax跨域无法同步登录
							layer.msg("登录成功");
							setTimeout(function(){
								window.location.href = decodeURIComponent(jump_from_url);
							},500);
						}
					});
				}
		});
	/*
	var qiboBase = api.require('qiboBase');
	var param = {appParam:"Hello APICloud!"};
	var resultCallback = function(ret, err){
		layer.msg("请稍候,正在检验数据...",{time:5000});
		var url = ck_wxlogin_url + "token=" + ret.token;
		$.get(url,function(res){
			if(res.code==1){
				layer.alert(res.msg);
			}else{	//检验资料正确,ajax跨域无法同步登录
				layer.msg("登录成功");
				setTimeout(function(){
					window.location.href = decodeURIComponent(jump_from_url);
				},500);
			}
		});
	}
	qiboBase.wxLogin(param,resultCallback);
	*/
}

//原生微信APP登录
function apicloud_app_login(){
	var wxPlus = api.require('wxPlus');
	wxPlus.auth({
			apiKey: ''
		}, function(ret, err) {
			if (ret.status) {	//JSON.stringify(ret)
				var code = ret.code;
				$.get(app_login_url+'?code='+code, function(res) {
					if(res.code==0){
						layer.msg("登录成功");
						setTimeout(function(){
							window.location.href = decodeURIComponent(jump_from_url);
						},500);
					}else{
						layer.alert(res.msg);
					}
				});
			}else{
				alert(err.code);
			}
	});
}