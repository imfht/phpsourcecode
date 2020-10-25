$(function() {

    /*
	// 钉元素
	$("#left_menu").pin({
		containerSelector : "#container_menu",
		minWidth : 1400
	});
	// ------------------------------------------------------------------------
    */

	// 回到顶部
	$.scrollUp({
		scrollName : 'scrollUp', // Element ID
		topDistance : '300', // Distance from top before showing element (px)
		topSpeed : 300, // Speed back to top (ms)
		animation : 'fade', // Fade, slide, none
		animationInSpeed : 200, // Animation in speed (ms)
		animationOutSpeed : 200, // Animation out speed (ms)
		scrollText : '', // Text for element
		activeOverlay : false
	});
	// ------------------------------------------------------------------------

	// 获取当前时间
	var date = new Date();
	$('#local_time').html(date.toLocaleString());

	// ------------------------------------------------------------------------

	// 一秒请求一次
	if ($('#local_time').length > 0) {
		window.setInterval('get_time()', 1000);
	}

	// ------------------------------------------------------------------------
	$.ajaxSetup({
		type : "POST",
		cache : false
	});
	// ------------------------------------------------------------------------
});

/**
 * 公共设置==>文件名设置 表单验证
 */
$("#set_filename_form").validate({
	rules : {
		resources : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
		css : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
		js : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
        img : {
            required : true,
            maxlength : 50,
            chrnum : "只能输入字母、数字"
        },
		editor : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
		art : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
		valicode : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		},
		uploads : {
			required : true,
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		}
	},
	messages : {
		resources : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		css : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		js : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
        img : {
            required : "<i class='icon-remove'></i>请输入文件名",
            maxlength : "<i class='icon-remove'></i>最大长度50",
            chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
        },
		editor : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		art : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		valicode : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		uploads : {
			required : "<i class='icon-remove'></i>请输入文件名",
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 公共设置==>上传大小 表单验证
 */
$("#set_upload_size_form").validate({
	rules : {
		upload_image_size : {
			maxlength : 10,
			positive_integer : "只能输入正整数"
		},
		upload_flash_size : {
			maxlength : 10,
			positive_integer : "只能输入正整数"
		},
		uploads_media_size : {
			maxlength : 10,
			positive_integer : "只能输入正整数"
		},
		upload_file_size : {
			maxlength : 10,
			positive_integer : "只能输入正整数"
		}
	},
	messages : {
		upload_image_size : {
			maxlength : "<i class='icon-remove'></i>最大长度10",
			positive_integer : "<i class='icon-remove'></i>只能输入正整数"
		},
		upload_flash_size : {
			maxlength : "<i class='icon-remove'></i>最大长度10",
			positive_integer : "<i class='icon-remove'></i>只能输入正整数"
		},
		uploads_media_size : {
			maxlength : "<i class='icon-remove'></i>最大长度10",
			positive_integer : "<i class='icon-remove'></i>只能输入正整数"
		},
		upload_file_size : {
			maxlength : "<i class='icon-remove'></i>最大长度10",
			positive_integer : "<i class='icon-remove'></i>只能输入正整数"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 公共设置==>主题 表单验证
 */
$("#set_theme_form").validate({
	rules : {
		theme : {
			maxlength : 50,
			chrnum : "只能输入字母、数字"
		}

	},
	messages : {
		theme : {
			maxlength : "<i class='icon-remove'></i>最大长度50",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 公共设置==>密钥 表单验证
 */
$("#set_encryption_key_form").validate({
	rules : {
		encryption_key_begin : {
			required : true,
			maxlength : 20,
			chrnum : "只能输入字母、数字"
		},
		encryption_key_end : {
			required : true,
			maxlength : 20,
			chrnum : "只能输入字母、数字"
		}

	},
	messages : {
		encryption_key_begin : {
			required : "<i class='icon-remove'></i>请输入开始密钥",
			maxlength : "<i class='icon-remove'></i>最大长度20",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		},
		encryption_key_end : {
			required : "<i class='icon-remove'></i>请输入结束密钥",
			maxlength : "<i class='icon-remove'></i>最大长度20",
			chrnum : "<i class='icon-remove'></i>只能输入字母、数字"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 公共设置==>邮件设置表单验证
 */
$("#set_email_form").validate({
    rules : {
        email_smtp : {
            maxlength : 20
        },
        email_port : {
            positive_integer:"只能输入正整数",
            maxlength : 2
        },email_user:{
            email : true,
            maxlength : 50
        },email_password:{
            maxlength : 50
        },email_title:{
            maxlength : 50
        },email_username:{
            maxlength : 50
        }
    },
    messages : {
        email_smtp : {
            maxlength : "<i class='icon-remove'></i>最大长度20"
        },
        email_port : {
            positive_integer:"<i class='icon-remove'></i>只能输入正整数",
            maxlength : "<i class='icon-remove'></i>最大长度2"
        },
        email_user : {
            email:"<i class='icon-remove'></i>请输入正确的邮箱地址",
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        email_password : {
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        email_title : {
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        email_username : {
            maxlength : "<i class='icon-remove'></i>最大长度50"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 公共设置==>邮件测试表单验证
 */
$("#set_email_test_form").validate({
    rules : {
        email_to_user:{
            required:true,
            email : true,
            maxlength : 50
        }
    },
    messages : {
        email_to_user : {
            required:"<i class='icon-remove'></i>必须填写",
            email:"<i class='icon-remove'></i>请输入正确的邮箱地址",
            maxlength : "<i class='icon-remove'></i>最大长度50"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 站点设置==>基本设置 表单验证
 */
$("#set_web_basic_form").validate({
	rules : {
		name : {
			required : true,
			maxlength : 50
		},
		logo : {
			maxlength : 50,
			filename : "只能输入.、字母、数字"
		},
		icp : {
			maxlength : 50
		},
		keywords : {
			maxlength : 200
		},
		description : {
			maxlength : 200
		}
	},
	messages : {
		name : {
			required : "<i class='icon-remove'></i>站点名称必须填写",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		logo : {
			maxlength : "<i class='icon-remove'></i>最大长度50",
			filename : "<i class='icon-remove'></i>只能输入.、字母、数字"
		},
		icp : {
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		keywords : {
			maxlength : "<i class='icon-remove'></i>最大长度200"
		},
		description : {
			maxlength : "<i class='icon-remove'></i>最大长度200"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 站点设置==>站点状态 表单验证
 */
$("#set_web_status_form")
		.validate(
				{
					rules : {
						close_reason : {
							maxlength : 200
						},
						status : {
							required : true
						}
					},
					messages : {
						close_reason : {
							maxlength : "<i class='icon-remove'></i>最大长度200"
						},
						status : {
							required : "<i class='icon-remove'></i>必须选择"
						}
					},
					errorElement : "span",
					highlight : function(element, errorClass) {// element出错时触发
						$(element).parent().parent().removeClass('success');
						$(element).parent().parent().addClass('error');
					},
					unhighlight : function(element, errorClass) {// element通过验证时触发
						$(element).parent().parent().removeClass('error');
						$(element).parent().parent().addClass('success');
					},
					errorPlacement : function(error, element) {
						if (element.is(":radio")) {// parent是父元素，next是下一个同级元素
							// error.appendTo(element.parent().parent().parent().next().next().next());
							element.parent().parent().parent().parent().effect(
									'shake', {
										times : 2
									}, 100);
						} else {
							$(error[0]).html(
									"<i class='icon-remove'></i>"
											+ $(error[0]).text());
							error.appendTo(element.next());
							element.parent().parent().effect('shake', {
								times : 2
							}, 100);
						}
					},
					submitHandler : function(form) {
						form.submit();
					}
				});

// ------------------------------------------------------------------------

/**
 * 后台设置==>基本设置 表单验证
 */
$("#set_admin_basic_form").validate({
	rules : {
		name : {
			maxlength : 50
		},
		logo : {
			maxlength : 50,
			filename : "只能输入.、字母、数字"
		}
	},
	messages : {
		name : {
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		logo : {
			maxlength : "<i class='icon-remove'></i>最大长度50",
			filename : "<i class='icon-remove'></i>只能输入.、字母、数字"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 弹出修改密码模拟窗口
 */
$('#btn_change_password a').click(function(e) {
	e.preventDefault();
	$('#myModal').modal('show');
	$('#change_password_form')[0].reset();
	$('#myModal_success').hide();
	$('#alert_error').hide();
});
// ------------------------------------------------------------------------

/**
 * 删除确认弹出框
 */
function show_delete_confirm(title, content, url) {
	$.dialog({
		title : title,
		content : content,
		lock : true,
		fixed : true,
		ok : function() {
			$.post(url, function(data) {
				var message_data = $.parseJSON(data);
				if (message_data['msg'] == 1) {
					show_dialog(message_data['info']);
					setTimeout('top.window.location.reload()', 1000);
				} else {
					show_dialog(message_data['info']);
				}
			});
		},
		okValue : '确定',
		cancel : true,
		cancelValue : '关闭'
	});

}

// ------------------------------------------------------------------------
/**
 * 弹出信息框
 */
function show_dialog(content) {
	$.dialog({
		content : "<h4>" + content + "</h4>",
		title : '消息',
		lock : true,
		fixed : true,
		time : 1000
	});
}

// ------------------------------------------------------------------------

/**
 * 权限管理==>权限表单验证
 */
$("#power_form").validate({
	rules : {
		name : {
			required : true,
			chinese : "只能包含中文、英文字母、数字、下划线、破折号",
			maxlength : 50
		},
		icon : {
			maxlength : 50,
			icon_filename : "只能包含英文字母、数字、下划线、破折号"
		},
		url : {
			url_filename : "只能包含英文字母、数字、下划线、斜线",
			maxlength : 150
		},
		rank : {
			digits : "只能输入正整数",
			maxlength : 11
		}
	},
	messages : {
		name : {
			required : "<i class='icon-remove'></i>必须填写",
			chinese : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		icon : {
			icon_filename : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、破折号",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		url : {
			url_filename : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线",
			maxlength : "<i class='icon-remove'></i>最大长度150"
		},
		rank : {
			digits : "<i class='icon-remove'></i>只能输入正整数",
			maxlength : "<i class='icon-remove'></i>最大长度11"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		form.submit();
	}
});

// ------------------------------------------------------------------------

/**
 * 角色管理==>角色表单验证
 */
$("#role_form").validate({
    rules : {
		name : {
			required : true,
			chinese : "只能包含中文、英文字母、数字、下划线、破折号",
			maxlength : 50
		},
		introduce : {
			required : true,
			maxlength : 50
		}
	},
	messages : {
		name : {
			required : "<i class='icon-remove'></i>必须填写",
			chinese : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
		introduce : {
			required : "<i class='icon-remove'></i>必须填写",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		}
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
	submitHandler : function(form) {
		if ($('input[name="powers[]"]:checked').length == 0) {
			show_dialog('请选择一项权限');
			return false;
		} else {
			form.submit();
		}
	}
});

// ------------------------------------------------------------------------

/**
 * 管理员管理==>管理员表单验证
 */
$("#manager_form").validate({
	rules : {
        role_id:{
            required:true,
            selectNone:"必须选择一项"
        },
		username : {
			required : true,
            chrnum : "只能包含英文字母、数字",
            minlength : 6,
			maxlength : 50
		},
        password : {
            alpha_dash_bias : "只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : 6,
			maxlength : 16
		},
        password_confirm : {
            alpha_dash_bias : "只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : 6,
            maxlength : 16,
            equalTo:"#password"
        },
        nickname:{
            chinese : "只能包含中文、英文字母、数字、下划线、破折号",
            minlength : 4,
            maxlength : 50
        }
        ,
        phone:{
            mobile : "手机号码有误",
            maxlength : 11
        }
        ,
        email:{
            required : true,
            email : true,
            maxlength : 50
        }

	},
	messages : {
        role_id:{
            required : "<i class='icon-remove'></i>必须填写",
            selectNone : "<i class='icon-remove'></i>必须选择一项"
        },
        username : {
			required : "<i class='icon-remove'></i>必须填写",
            chrnum : "<i class='icon-remove'></i>只能包含英文字母、数字",
            minlength : "<i class='icon-remove'></i>最小长度6",
			maxlength : "<i class='icon-remove'></i>最大长度50"
		},
        password : {
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度6",
            maxlength : "<i class='icon-remove'></i>最大长度16"
		},
        password_confirm : {
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度6",
            maxlength : "<i class='icon-remove'></i>最大长度16",
            equalTo:"<i class='icon-remove'></i>密码和确认密码不一致"
        },
        nickname:{
            chinese : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度4",
            maxlength :"<i class='icon-remove'></i>最大长度50"
        },
        phone:{
            mobile : "<i class='icon-remove'></i>手机号码有误",
            maxlength :"<i class='icon-remove'></i>最大长度11"
        }
        ,
        email:{
            required : "<i class='icon-remove'></i>必须填写",
            email : "<i class='icon-remove'></i>电子邮箱有误",
            maxlength :"<i class='icon-remove'></i>最大长度50"
        }
	},
	errorElement : "span",
	highlight : function(element, errorClass) {// element出错时触发
		$(element).parent().parent().removeClass('success');
		$(element).parent().parent().addClass('error');
	},
	unhighlight : function(element, errorClass) {// element通过验证时触发
		$(element).parent().parent().removeClass('error');
		$(element).parent().parent().addClass('success');
	},
	errorPlacement : function(error, element) {
		$(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
		error.appendTo(element.next());
		element.parent().parent().effect('shake', {
			times : 2
		}, 100);
	},
    submitHandler : function(form) {
			form.submit();
	}
});

// ------------------------------------------------------------------------


/**
 * 管理员管理==>更换密码表单验证
 */
$("#manager_pwd_form").validate({
    rules : {
        password:{
            required: true,
            alpha_dash_bias : "只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : 6,
            maxlength : 16
        },
        new_password : {
            required: true,
            alpha_dash_bias : "只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : 6,
            maxlength : 16
        },
        new_password_confirm : {
            required: true,
            alpha_dash_bias : "只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : 6,
            maxlength : 16,
            equalTo:"#new_password"
        }
    },
    messages : {
        password:{
            required : "<i class='icon-remove'></i>必须填写",
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度6",
            maxlength : "<i class='icon-remove'></i>最大长度16"
        },
        new_password : {
            required : "<i class='icon-remove'></i>必须填写",
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度6",
            maxlength : "<i class='icon-remove'></i>最大长度16"
        },
        new_password_confirm : {
            required : "<i class='icon-remove'></i>必须填写",
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含英文字母、数字、下划线、斜线、破折号",
            minlength : "<i class='icon-remove'></i>最小长度6",
            maxlength : "<i class='icon-remove'></i>最大长度16",
            equalTo:"<i class='icon-remove'></i>新密码和确认新密码不一致"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 类别管理==>类别表单验证
 */
$("#category_form").validate({
    rules : {
        name:{
            required: true,
            chinese : "只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : 50
        }
    },
    messages : {
        name:{
            required : "<i class='icon-remove'></i>必须填写",
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : "<i class='icon-remove'></i>最大长度50"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 模块管理==>模块表单验证
 */
$("#mode_form").validate({
    rules : {
        name:{
            required: true,
            chinese : "只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : 20
        },
        remark:{
            maxlength : 200
        },
        status:{
            required: true,
            positive_integer:'只能输入正整数'
        },
        rank:{
            positive_integer:'只能输入正整数'
        }
    },
    messages : {
        name:{
            required : "<i class='icon-remove'></i>必须填写",
            alpha_dash_bias : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : "<i class='icon-remove'></i>最大长度20"
        },
        remark:{
            maxlength : "<i class='icon-remove'></i>最大长度200"
        },
        status:{
            required : "<i class='icon-remove'></i>必须选择",
            positive_integer : "<i class='icon-remove'></i>只能输入正整数"
        },
        rank:{
            positive_integer : "<i class='icon-remove'></i>只能输入正整数"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 首页幻灯片管理==>模块表单验证
 */
$("#slide_form").validate({
    rules : {
        title:{
            required: true,
            chinese : "只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : 50
        },
        url:{
            maxlength : 50
        },
        thumb:{
            maxlength : 100
        },
        rank:{
            positive_integer:'只能输入正整数',
            maxlength : 6
        },
        remark:{
            maxlength : 200
        },
        mode_id:{
            required: true
        }
    },
    messages : {
        name:{
            required : "<i class='icon-remove'></i>必须填写",
            chinese : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        url:{
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        thumb:{
            maxlength : "<i class='icon-remove'></i>最大长度100"
        },
        rank:{
            positive_integer : "<i class='icon-remove'></i>只能输入正整数",
            maxlength : "<i class='icon-remove'></i>最大长度6"
        },
        remark:{
            maxlength : "<i class='icon-remove'></i>最大长度200"
        },
        mode_id:{
            required : "<i class='icon-remove'></i>必须填写"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});

// ------------------------------------------------------------------------

/**
 * 友情链接管理==>模块表单验证
 */
$("#link_form").validate({
    rules : {
        name:{
            required: true,
            chinese : "只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : 50
        },
        url:{
            required: true,
            url:true,
            maxlength : 200
        },
        thumb:{
            maxlength : 100
        },
        rank:{
            positive_integer:'只能输入正整数',
            maxlength : 6
        }
    },
    messages : {
        name:{
            required : "<i class='icon-remove'></i>必须填写",
            chinese : "<i class='icon-remove'></i>只能包含中文、英文字母、数字、下划线、破折号",
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        url:{
            maxlength : "<i class='icon-remove'></i>最大长度50"
        },
        thumb:{
            maxlength : "<i class='icon-remove'></i>最大长度100"
        },
        rank:{
            positive_integer : "<i class='icon-remove'></i>只能输入正整数",
            maxlength : "<i class='icon-remove'></i>最大长度6"
        },
        remark:{
            maxlength : "<i class='icon-remove'></i>最大长度200"
        },
        mode_id:{
            required : "<i class='icon-remove'></i>必须填写"
        }
    },
    errorElement : "span",
    highlight : function(element, errorClass) {// element出错时触发
        $(element).parent().parent().removeClass('success');
        $(element).parent().parent().addClass('error');
    },
    unhighlight : function(element, errorClass) {// element通过验证时触发
        $(element).parent().parent().removeClass('error');
        $(element).parent().parent().addClass('success');
    },
    errorPlacement : function(error, element) {
        $(error[0]).html("<i class='icon-remove'></i>" + $(error[0]).text());
        error.appendTo(element.next());
        element.parent().parent().effect('shake', {
            times : 2
        }, 100);
    },
    submitHandler : function(form) {
        form.submit();
    }
});


/*
 * 对话框调用
 * 
 * function show_dialog(title, content, type, action_url, action_obj) { type =
 * typeof (type) == 'undefined' ? '' : type; action_url = typeof (action_url) ==
 * 'undefined' ? '' : action_url; action_obj = typeof (action_obj) ==
 * 'undefined' ? '' : action_obj; switch (type) { case 'del': $.dialog({ title :
 * title, content : content, lock : true, fixed : true, ok : function() {
 * this.shake(); $.get(action_url, function(msg) { if (msg == 1) {
 * show_dialog('删除提示', '删除成功'); $('#' + action_obj).fadeOut(3000, function() {
 * $(this).remove(); }); } else { show_dialog('删除提示', '删除失败或未找到记录'); } }); },
 * okValue : '确定', cancel : true, cancelValue : '关闭'
 * 
 * }); break; } if (type != 'del') { $.dialog({ content : "<h4>" + content + "</h4>",
 * title : '消息', lock : true, fixed : true, time : 2000 }); } } //
 * ------------------------------------------------------------------------
 */
