// +----------------------------------------------------------------------
// | RXThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2017-2019 http://rxthink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 牧羊人 <rxthink@gmail.com>
// +----------------------------------------------------------------------

/**
 * @name 后台登录模块
 */
layui.define(['larry','form','larryms'],function(exports){
	"use strict";
	var $ = layui.$,
            layer = layui.layer,
            larryms = layui.larryms,
            form = layui.form;
    
	//larryms.success('用户名：admin 密码：admin 无须输入验证码，输入正确后直接登录后台!','后台帐号登录提示',20);
	
    function supersized() {
        $.supersized({
            // 功能
            slide_interval: 3000,
            transition: 1,
            transition_speed: 1000,
            performance: 1,
            // 大小和位置
            min_width: 0,
            min_height: 0,
            vertical_center: 1,
            horizontal_center: 1,
            fit_always: 0,
            fit_portrait: 1,
            fit_landscape: 0,
            // 组件
            slide_links: 'blank',
            slides: [{
                image: '/static/admin/images/login/desktop.jpg'
            }]
        });
    }
    larryms.plugin('jquery.supersized.min.js',supersized); 
    //模拟登录(2.08会重写构建前后端分离验证模块)
    
    form.on('submit(submit)', function(data) {

    	$.post("/Login/login", data.field, function(data){
			if (data.success) {
				layer.msg('登录成功', {
	                icon: 1,
	                time: 1000
	            });
				
				//延迟1秒
				setTimeout(function() {
	                window.location.href = "/Index/index";
	            }, 1000);
				
				return false ;
			}else{
				layer.msg(data.msg, {
	                icon: 2,
	                time: 1000
	            });
				
				layer.tips(data.msg, $("#"+data.data), {
	                tips: [3, '#FF5722']
	            });
			}
		}, 'json');
    	
        return false;
    });
    exports('login', {}); 
});

//获取验证码
function flushYzm(){
	var url = layui.$('#verify_img').attr('src');
	layui.$('#verify_img').attr('src',"/Login/verify?rand="+Math.round(Math.random()*100));
}