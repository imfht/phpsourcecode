$submitButton = $("#submit");
$checkCodeButton = $("#getSMSCode");
// 获取验证码
$('#getSMSCode').click(function(){
			email = $('#email').val();
			phone = $('#phone').val();
			if(phone.length !=11){
				layer.msg('请输入合法的电话号码');
				return false;
			}
			$thisbutton = $(this);
			  // 发送ajax 请求获取验证码
			$.ajax({
				url:'/checkcode/'+phone,
				data:'',
				dataType:'text',
				type:'get',
				before:function(){
					layer.msg('before');
				},
				sucess:function(data,status){
					layer.msg('验证码已发送');
					
				},
				error:function(){},
				complete:function(){
					// 使当前按钮 1 分钟内不可用
					var count = 60;
					CountDown();
		            var countdown = setInterval(CountDown, 1000);
		            function CountDown() {
		            	$thisbutton.attr("disabled", true);
		               // $('#getSMSCode').val("Please wait " + count + " seconds!");
		            	$thisbutton.value = "Please wait " + count + " seconds!";
		                if (count == 0) {
		                	$thisbutton.val("获取验证码");
		                	$thisbutton.removeAttr("disabled");
		                    clearInterval(countdown);
		                }
		                count--;
		            }
		            
		           
		            return false;
				},
			});
			
			return false;
		});
mylocation = window.location;
// 表单提交

$("#registerForm").ajaxForm({
	type : "post",
	dataType : "json",
	beforeSubmit : function (formData, jqForm, options) {
		var username = $(jqForm).find("input[name='name']");
		if(username.val().length < 2){
			layer.msg('姓名不能小于2个字符');
			return false;
		}
		var companyName = $(jqForm).find("input[name='companyName']");
		if(companyName.val().length < 2){
			layer.msg('公司名称不能小于2个字符');
			return false;
		}
		var email = $(jqForm).find("input[name='email']");
		if(email.val().length < 3){
			layer.msg('邮箱不能小于3个字符');
			return false;
		}
		var password = $(jqForm).find("input[name='password']");
		if(password.val().length < 6){
			layer.msg('密码长度不能小于6个字符');
			return false;
		}
		var checkCode = $(jqForm).find("input[name='checkCode']");
		if(checkCode.val().length < 5){
			layer.msg('验证码长度不正确');
			return false;
		}
		$submitButton.val('提交中 ...');
		$submitButton.attr("disabled", true);
		return true;
	},
	success : function (res, statusText, xhr, $form) {
		$submitButton.val('注册');
		$submitButton.removeAttr("disabled");
		if(res.errcode == 1) {
			alert(res.message);
		}else{
			$("html").html(res);
		}
		
	},
	error: function (xhr) {
        console.log(xhr.status);
    },
	complete: function(jqXHR){
	        console.log(jqXHR.status);
	        window.location.reload();
	        return false;
	    }
});
