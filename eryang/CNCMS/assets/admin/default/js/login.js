/**
 * 登录验证
 * @type {FormValidator}
 */
var validator = new FormValidator('login_form', [{
	name : 'username',
	display : '用户名',
	rules : 'required|alpha_dash'
}, {
	name : 'password',
	display : '密码',
	rules : 'required|min_length[6] | max_length[16]'
}], 'chinese', function(errors) {
	var SELECTOR_ERRORS = $('#alert_error');
	alert(error_alert);
	if (errors.length > 0) {
		$('#error_alert').css({
			display : 'none'
		});
		$('#success_alert').css({
			display : 'none'
		});
		SELECTOR_ERRORS.css({
			display : 'block'
		});
		SELECTOR_ERRORS.empty();
		for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
			SELECTOR_ERRORS.append(errors[i].message + '<br />');
		}
	} else {
		$('#error_alert').css({
			display : 'none'
		});
		$('#success_alert').css({
			display : 'none'
		});
		SELECTOR_ERRORS.css({
			display : 'none'
		});
	}
});

/**
 * 登录验证(验证码)
 * @type {FormValidator}
 */
var validator = new FormValidator('login_vali_form', [{
	name : 'username',
	display : '用户名',
	rules : 'required|alpha_dash'
}, {
	name : 'password',
	display : '密码',
	rules : 'required|min_length[6]|max_length[16]'
}, {
	name : 'valicode',
	display : '验证码',
	rules : 'required|alpha_numeric'
}], 'chinese', function(errors) {
	var SELECTOR_ERRORS = $('#alert_error');
	if (errors.length > 0) {
		$('#error_alert').css({
			display : 'none'
		});
		$('#success_alert').css({
			display : 'none'
		});
		SELECTOR_ERRORS.css({
			display : 'block'
		});
		SELECTOR_ERRORS.empty();
		for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
			SELECTOR_ERRORS.append(errors[i].message + '<br />');
		}
	} else {
		$('#error_alert').css({
			display : 'none'
		});
		$('#success_alert').css({
			display : 'none'
		});
		SELECTOR_ERRORS.css({
			display : 'none'
		});
	}
});

/**
 * 忘记密码
 * @type {FormValidator}
 */
var validator = new FormValidator('forgot_password_form', [{
    name : 'username',
    display : '用户名',
    rules : 'required|alpha_dash'
}], 'chinese', function(errors) {
    var SELECTOR_ERRORS = $('#alert_error');
    if (errors.length > 0) {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'block'
        });
        SELECTOR_ERRORS.empty();
        for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
            SELECTOR_ERRORS.append(errors[i].message + '<br />');
        }
    } else {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'none'
        });
    }
});

/**
 * 通过邮箱找回密码
 * @type {FormValidator}
 */
var validator = new FormValidator('check_username_form', [{
    name : 'register_email',
    display : '注册邮箱',
    rules : 'required|valid_email'
}], 'chinese', function(errors) {
    var SELECTOR_ERRORS = $('#alert_error');
    if (errors.length > 0) {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'block'
        });
        SELECTOR_ERRORS.empty();
        for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
            SELECTOR_ERRORS.append(errors[i].message + '<br />');
        }
    } else {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'none'
        });
    }
});

/**
 * 重置密码
 * @type {FormValidator}
 */
var validator = new FormValidator('reset_pwd_form', [{
    name : 'new_password',
    display : '新密码',
    rules : 'required|min_length[6] | max_length[16]'
},{
        name : 'new_password_fit',
        display : '确认新密码',
        rules : 'required|matches[new_password]'
 }], 'chinese', function(errors) {
    var SELECTOR_ERRORS = $('#alert_error');
    if (errors.length > 0) {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'block'
        });
        SELECTOR_ERRORS.empty();
        for (var i = 0, errorLength = errors.length; i < errorLength; i++) {
            SELECTOR_ERRORS.append(errors[i].message + '<br />');
        }
    } else {
        $('#error_alert').css({
            display : 'none'
        });
        $('#success_alert').css({
            display : 'none'
        });
        SELECTOR_ERRORS.css({
            display : 'none'
        });
    }
});