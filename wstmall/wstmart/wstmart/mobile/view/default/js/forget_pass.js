jQuery.noConflict();
function forgetPwd(){
    var params = {};
    var step = $('#step').val();
    params.modes = modes;
    params.step = step;
    params.loginName = $.trim($('#loginName').val());
    params.Checkcode = $('#checkCode').val();
    params.verifyCode = $('#loginVerfy').val();
    if(params.loginName=='' && step==1){
    	WST.msg('请输入用户名','info');
    	WST.getVerify("#verifyImg1")
    	return;
    }
    $.post(WST.U('mobile/users/findPass'),params,function(data,textStatus){
      var json = WST.toJson(data);
      if(json.status=='1'){
	        	if(step==1){
		        	location.href=WST.U('mobile/users/forgetPasst','',true);
	        	}else if(step==2){
	        		if(modes==1){
	        			location.href=json.url;
	        		}else{
						WST.msg('邮件已发送，请注意查收');
	        			disabledBtn();
	        		}
	        	}
      }else{
			WST.msg(json.msg,'info');
			WST.getVerify("#verifyImg1");
		    $('#loginVerfy').val('');
      }
    });
}

$(function(){
	// 弹出层
   $("#frame").css('top',0);
});

//弹框
function dataShow(type){
	if(type=="email"){
		modes = 0;
		$('#contentTitle').html('通过邮箱找回密码');
		$('.emailBox').show();
		$('.phoneBox').hide();
	}else if(type=="phone"){
		modes = 1;
		$('#contentTitle').html('通过手机找回密码');
		$('.emailBox').hide();
		$('.phoneBox').show();
	}else{
		return;
	}
    jQuery('#cover').attr("onclick","javascript:dataHide();").show();
    jQuery('#frame').animate({"right": 0}, 500);
}
function dataHide(){
    var dataHeight = $("#frame").css('height');
    var dataWidth = $("#frame").css('width');
    jQuery('#frame').animate({'right': '-'+dataWidth}, 500);
    jQuery('#cover').hide();
}



/* 手机找回 */
function forgetPhone(){
	var code = $.trim($('#checkCode').val());
	if(code!=''){
		forgetPwd()
	}else{
		WST.msg('请输入手机校验码','info');
		return false;
	}
}
/* 发送邮箱校验码 */
function forgetEmail(){
	modes = 0;
	var code = $.trim($('#loginVerfy').val());
	if(code != ''){
		forgetPwd();
	}else{
		WST.msg('请输入验证码','info');
		return false;
	}	
}
/* 提交邮箱验证码-重置密码 */
function resetByEmail(){
	var code = $.trim($('#emailCode').val());
	if(code == ''){
		WST.msg('请输入校验码','info');
		return false;
	}else{
		$.post(WST.U('mobile/users/forgetPasss'),{secretCode:code},function(data){
			var json = WST.toJson(data);
			if(json.status==-1){
				WST.msg('校验码错误','info');
				WST.getVerify("#verifyImg1")
				return false;
			}else{
				location.href=WST.U('mobile/users/resetPass','',true);
			}
		})
	}	
}

/*重置密码*/
function resetPwd(){
	var loginPwd = $('#loginPwd').val();
	var repassword = $('#repassword').val();
    if(window.conf.IS_CRYPTPWD==1){
        var public_key=$('#key').val();
        var exponent="10001";
   	    var rsa = new RSAKey();
        rsa.setPublic(public_key, exponent);
        var loginPwd = rsa.encrypt(loginPwd);
        var repassword = rsa.encrypt(repassword);
    }
	var params = {};
	params.step = $('#step').val();
	params.loginPwd = loginPwd;
	params.repassword = repassword;
	$('.button').attr('disabled',true);
	$.post(WST.U('mobile/users/findPass'),params,function(data){
		var json = WST.toJson(data);
		if(json.status==1){
			WST.msg('重置成功');
			setTimeout(function(){
				location.href=WST.U('mobile/users/login');
			},1000)
		}else{
			$('.button').attr('false',true);
			WST.msg(json.msg,'info');
			return false;
		}
	})
}



var modes = '';

/** 校验码 **/
function phoneVerify2(){
	if(WST.conf.SMS_VERFY==1){
		if(!$('#smsVerify').val()){
			WST.msg('请输入验证码','info');
			return false;
		}
    }
	modes = $('#modes').val();
	var time = 0;
	var isSend = false;
	var smsVerfy = $('#smsVerify').val();
	$.post(WST.U('mobile/users/getfindPhone'),{smsVerfy:smsVerfy},function(data,textStatus){
		var json = WST.toJson(data);
		if(isSend )return;
		isSend = true;
		if(json.status!=1){
			WST.msg(json.msg, 'info');
			WST.getVerify('#verifyImg2');
			$('#smsVerify').val('');
			time = 0;
			isSend = false;
		}if(json.status==1){
			WST.msg('短信已发送，请注意查收');
			time = 120;
			var href = $('.send').attr('href');
			$('.send').html('重新获取(120)');
			var task = setInterval(function(){
				time--;
				$('.send').html('重新发送('+time+")");
				if(time==0){
					isSend = false;						
					clearInterval(task);
					$('.send').attr('href',href).html("重新获取");
				}
			},1000);
		}
	});
}
function disabledBtn(){
	time = 120;
	var href = $('.sendEmail').attr('href');
	$('.sendEmail').attr('href','javascript:void(0)').html('发送验证邮件(120)');
	var task = setInterval(function(){
		time--;
		$('.sendEmail').html('重新发送('+time+")");
		if(time==0){
			isSend = false;						
			clearInterval(task);
			$('.sendEmail').attr('href',href).html("发送验证邮件");
		}
	},1000);
}