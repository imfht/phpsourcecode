$(function() {
	// 得到验证码对应的字母code
	$("#loginForm").validate({
		rules: { 
			username: { 
				required: true		       
	        },
	        password: { 
	        	required: true				
	        },
	        captcha: {
	        	required: true
			}
		}, 
		messages: {
			username: { 
				required: "用户名必须填写"
	        },
	        password: { 
	        	required: "登录密码必须填写"
	        },
			captcha: {
				required: "验证码必须填写"
			}
		},
		showErrors : function(errorMap, errorList) {
			var errorContainer = $("#loginForm .error-message");
			if (errorList.length > 0) {
				errorContainer.text(errorList[0].message);
			}
		},		
    	onfocusout : false
	});
})