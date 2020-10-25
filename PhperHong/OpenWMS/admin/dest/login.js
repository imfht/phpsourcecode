'use strict';
jQuery(function($) {
	
	
    var app = $('#app').val();
 	$('#go_reg').click(function(){
 		$('#login-box').removeClass('visible');
 		$('#signup-box').addClass('visible');
 	});
 	$('#back_login').click(function(){
 		$('#signup-box').removeClass('visible');
 		$('#login-box').addClass('visible');
 	});
 	$('#submint_login').click(function(){
 		login(); 
 	}); 
    function login(){
        var login_username = $('#login_username').val();
        var login_password = $('#login_password').val();
        if ($.trim(login_username) == '' || $.trim(login_password) == ''){
            $('#alert_box_login').jk_alert({ msg: '用户名或密码不能为空', type: 'danger', show_time: 3500 });
            return false;
        }
   
        $(this).button('loading');
        $.ajax({ 
            url: app+'?m=admin&c=Admin&a=check',  
            data:{'username': login_username, 'passwd': hex_md5(hex_md5(login_password))},
            dataType:'json',
            type:'POST',
            success: function(data){
                $('#submint_login').button('reset');
               
                if (data.ret == 1){

                    $('#alert_box_login').jk_alert({ msg: '登录成功，正在跳转', type: 'success', show_time: 3500 });
                    
                    window.location.href = data.data.url;
                }else{
                    $('#alert_box_login').jk_alert({ msg: data.msg, type: 'danger', show_time: 3500 });
                }
            }
        });
    }
    document.onkeydown = function(e){   
        var ev = document.all ? window.event : e; 
        if(ev.keyCode==13) {// 如（ev.ctrlKey && ev.keyCode==13）为ctrl+Center 触发 
            login();
        }
      }
    
    $('#login_username').focus();

    //注册
    var registerTimeout = 0;
    $('#getCode').click(function(){
        var phonereg = /^(13[0-9]|15[0-9]|18[0-9])\d{8}$/;
        var reg_phone = $('#reg_phone').val();

        if (!phonereg.test(reg_phone)){
            $('#alert_box_reg').jk_alert({ msg: '手机号码格式错误', type: 'danger', show_time: 3500 });
            return false;
        }
        $.ajax({ 
            url: app+'/Merchant/get_code',  
            data:{'phone': reg_phone},
            dataType:'json',
            type:'POST',
            success: function(d){
                if (d.ret == 1){
                    $('#alert_box_reg').jk_alert({ msg: d.msg, type: 'success', show_time: 3500 });
                    registerTimeout = 60;
                    $('#getCode').attr('disabled', true);
                    doClick();
                }else{
                    $('#alert_box_reg').jk_alert({ msg: d.msg, type: 'danger', show_time: 3500 });
                }
            }
        });

    });

    //倒计时
    var doClick = function(){ 

        if(registerTimeout == 0){
            $('#getCode').attr('disabled', false);
            $('#getCode').text('获取验证码');
        }else{
            registerTimeout--;
            $('#getCode').text(registerTimeout + '秒之后重新获取');
            setTimeout(function(){
                doClick();

            },1000);
        }
    }

    //注册
    $('#register').click(function(){
        var reg_username    = $('#reg_username').val();
        var reg_password    = $('#reg_password').val();
        var reg_cfpassword  = $('#reg_cfpassword').val();
        var reg_phone       = $('#reg_phone').val();
        var reg_code        = $('#reg_code').val();
        var reg_manager		= $('#reg_manager').val();
        var industry		= $('#industry').val();
        var province		= $('#province').val();
        var city			= $('#city').val();
        var area			= $('#area').val();
        if ($.trim(reg_username) == '' || reg_username.length<2 || reg_username.length>20){
            $('#alert_box_reg').jk_alert({ msg: '请填写用户名,长度范围为[2-20]', type: 'danger', show_time: 3500 });
            return false;
        }
        if ($.trim(reg_password) == '' || reg_password.length < 6 || reg_password.length > 20){
            $('#alert_box_reg').jk_alert({ msg: '请填写密码,长度范围为[6-20]', type: 'danger', show_time: 3500 });
            return false;
        }
        if ($.trim(reg_cfpassword) == '' || reg_cfpassword.length < 6 || reg_cfpassword.length > 20){
            $('#alert_box_reg').jk_alert({ msg: '请再次填写密码,长度范围为[6-20]', type: 'danger', show_time: 3500 });
            return false;
        }
        var phonereg = /^(13[0-9]|15[0-9]|18[0-9])\d{8}$/;
        if ($.trim(reg_phone) == '' || !phonereg.test(reg_phone)){
            $('#alert_box_reg').jk_alert({ msg: '手机号码格式错误', type: 'danger', show_time: 3500 });
            return false;
        }
        if ($.trim(reg_code) == ''){
            $('#alert_box_reg').jk_alert({ msg: '请填写短信验证码', type: 'danger', show_time: 3500 });
            return false;
        }
        if ($.trim(reg_manager) == '' || reg_manager.length<2 || reg_manager.length>20){
            $('#alert_box_reg').jk_alert({ msg: '请填写公司名称或者个人姓名,长度范围为[2-20]', type: 'danger', show_time: 3500 });
            return false;
        }
        if (industry == ''){
        	$('#alert_box_reg').jk_alert({ msg: '请选择一个行业', type: 'danger', show_time: 3500 });
            return false;
        }
        if (province == '' || city == ''){
        	$('#alert_box_reg').jk_alert({ msg: '请至少选择一个省份和城市', type: 'danger', show_time: 3500 });
            return false;
        }
        var postdata = {
            'username'  : reg_username,
            'password'  : hex_md5(hex_md5(reg_password)),
            'cfpassword': hex_md5(hex_md5(reg_cfpassword)),
            'phone'     : reg_phone,
            'code'      : reg_code,
            'manager'	: reg_manager,
            'industry'	: industry,
            'province'	: province,
            'city'		: city,
            'area'		: area
        };
        $.ajax({ 
            url: app+'/Merchant/reg_merchant',  
            data:postdata,
            dataType:'json',
            type:'POST',
            success: function(d){
                if (d.ret == 1){
                    $('#alert_box_reg').jk_alert({ msg: d.msg, type: 'success', show_time: 3500 });
                    $('#login_username').val(reg_username);
                    $('#login_password').val(reg_password);
                    login();
                }else{
                    $('#alert_box_reg').jk_alert({ msg: d.msg, type: 'danger', show_time: 3500 });
                }
            }
        });
    });

});