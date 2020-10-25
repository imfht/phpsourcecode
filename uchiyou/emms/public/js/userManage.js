(function (win) {
	var $base_btn = $("#info_update_submit");
	var $pass_btn = $("#password_update_submit");
	var formError = $("#formError");
	
    $("#baseInfoForm").ajaxForm({
    	type : "post",
    	dataType : "json",
    	beforeSubmit : function (formData, jqForm, options) {
    		
    		$base_btn.button('提交中 ...');
    		return true;
    	},
    	success : function (res, statusText, xhr, $form) {
    		$base_btn.button('reset')
    		if(res.errcode == 0) {
    			formError.text(res.message);
    		}else{
    			formError.text('重置失败');
    		}
    	}
    });
    $("#passwordForm").ajaxForm({
    	type : "post",
    	dataType : "json",
    	beforeSubmit : function (formData, jqForm, options) {
    		var newPassword = $(jqForm).find("input[name='newPassword']").val();
    		var confirmPassword = $(jqForm).find("input[name='confirmPassword']").val();
    		if(newPassword != confirmPassword){
    			formError.text("两次密码不一致");
    			return false;
    		}
    		
    		$pass_btn.button('提交中...');
    		return true;
    	},
    	success : function (res, statusText, xhr, $form) {
    		$pass_btn.button('reset')
    		if(res.errcode == 0) {
    			formError.text(res.message);
    		}else{
    			formError.text(res.message);
    		}
    	}
    });
    

})(window);