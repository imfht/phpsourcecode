// +----------------------------------------------------------------------
// | SentCMS [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.tensent.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: molong <molong@tensent.cn> <http://www.tensent.cn>
// +----------------------------------------------------------------------

// 当前资源URL目录
var baseRoot = (function () {
	var scripts = document.scripts, src = scripts[0].src;
	return src.substring(0, src.lastIndexOf("/") - 9) + '/';
})();

// 配置参数
require.config({
	waitSeconds: 60,
    packages: [{
        name: 'moment',
        location: 'plugins/moment',
        main: 'moment'
    }
    ],
	baseUrl: baseRoot,
	map: {'*': {css: baseRoot + 'plugins/require/require.css.js'}},
	paths: {
		'sent': ['common/js/sent'],
		'message': ['plugins/messager/messager'],

		'layer': ['plugins/layer/layer'],
		// jQuery
		'jquery': ['plugins/jquery/jquery.min'],
		'json': ['plugins/jquery/json2.min'],
		// bootstrap
		'bootstrap': ['plugins/bootstrap/js/bootstrap.min'],
	},
	shim: {
		'message': {deps: ['jquery', 'css!'+'plugins/messager/css/style.css']},
		// bootstrap
		'bootstrap':{deps: ['jquery']},
		'layer': {deps: ['jquery', 'css!'+baseRoot+'plugins/layer/theme/default/layer.css']},
	},
	deps: ['json'],
	// 开启debug模式，不缓存资源
	urlArgs: "ver=" + (new Date()).getTime()
});

// 注册jquery到require模块
require(['jquery', 'bootstrap', 'message', 'sent'], function ($, bootstrap, message, sent) {
	$('[name="password"]').on('focus', function () {
		$('#left-hander').removeClass('initial_left_hand').addClass('left_hand');
		$('#right-hander').removeClass('initial_right_hand').addClass('right_hand')
	}).on('blur', function () {
		$('#left-hander').addClass('initial_left_hand').removeClass('left_hand');
		$('#right-hander').addClass('initial_right_hand').removeClass('right_hand')
	});

	//表单提交
	$(document).ajaxStart(function(){
		$("button:submit").addClass("log-in").attr("disabled", true);
	}).ajaxStop(function(){
		$("button:submit").removeClass("log-in").attr("disabled", false);
	});

	$("form").submit(function(){
		var self = $(this);
		$.ajax({
			data: self.serialize(),
			type: 'post',
			success: function(data){
				if(data.code){
					sent.msg(data.msg, 'success');
					setTimeout(function(){
						window.location.href = data.url;
					}, 2000);
				} else {
					sent.msg(data.msg, 'error');
					setTimeout(function(){
						//刷新验证码
						$(".reloadverify").click();
					}, 2000);
				}
			},
			error: function(res){
				var data = res.responseJSON;
				sent.msg(data.message, 'error');
				setTimeout(function(){
					//刷新验证码
					$(".reloadverify").click();
				}, 2000);
			},
			dataType: 'json'
		})
		return false;
	});
	//初始化选中用户名输入框
	$("#itemBox").find("input[name=username]").focus();
	//刷新验证码
	var verifyimg = $(".verifyimg").attr("src");
	$(".reloadverify").click(function(){
		if( verifyimg.indexOf('?')>0){
			$(".verifyimg").attr("src", verifyimg+'&random='+Math.random());
		}else{
			$(".verifyimg").attr("src", verifyimg.replace(/\?.*$/,'')+'?'+Math.random());
		}
	});
});