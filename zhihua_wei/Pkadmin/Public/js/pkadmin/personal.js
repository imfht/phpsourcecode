/**
 * ==========================================
 * Created by Pocket Knife Technology.
 * Author: ZhiHua_W <zhihua_wei@foxmail.com>
 * Date: 2016/11/09 0017
 * Time: 下午 3:10
 * Project: Pkadmin后台管理系统
 * Version: 1.0.0
 * Power: personal.js
 * ==========================================
 */

//表单提交验证
var Script = function() {

	// 手机号码验证
	jQuery.validator.addMethod("isMobile", function(value, element) {
		var length = value.length;
		var mobile = /^(13[0-9]{9})|(18[0-9]{9})|(14[0-9]{9})|(17[0-9]{9})|(15[0-9]{9})$/;
		return this.optional(element) || (length == 11 && mobile.test(value));
	}, "请正确填写您的手机号码");

	'use strict';

	$.validator.setDefaults({
		submitHandler: function(form) {
			form.submit();
		}
	});
	$().ready(function() {
		$("#personalForm").validate({
			rules: {
				mobile: {
					required: true,
					isMobile: true
				},
				qq: {
					required: true,
					maxlength: 20
				},
				email: {
					required: true,
					email: true
				},
			},
			messages: {
				mobile: "请输入正确的手机号！",
				email: "请输入正确的邮箱号！",
				qq: {
					required: "请输入qq号码！",
					maxlength: "您的qq号码不能超过20个字符长！"
				},
			}
		});

		$("#changepwdForm").validate({
			rules: {
				oldpwd: {
					required: true,
					minlength: 5
				},
				password: {
					required: true,
					minlength: 5
				},
				confirm_password: {
					required: true,
					minlength: 5,
					equalTo: "#password"
				},
			},
			messages: {
				oldpwd: {
					required: "请输入旧密码！",
					minlength: "您的密码必须至少有5个字符长"
				},
				password: {
					required: "请输入新密码！",
					minlength: "您的密码必须至少有5个字符长"
				},
				confirm_password: {
					required: "请再次输入新密码！",
					minlength: "您的密码必须至少有5个字符长",
					equalTo: "您输入的确认密码不正确，请重新输入！"
				},
			}
		});
	});

	$().ready(function() {
		$("#admineditForm").validate({
			rules: {
				username: {
					required: true,
				},
				password: {
					minlength: 5,
				},
				mobile: {
					required: true,
					isMobile: true
				},
				qq: {
					required: true,
					maxlength: 20
				},
				email: {
					required: true,
					email: true
				},
			},
			messages: {
				username: "用户名不能为空！",
				password: {
					minlength: "您的密码必须至少有5个字符长"
				},
				mobile: "请输入正确的手机号！",
				email: "请输入正确的邮箱号！",
				qq: {
					required: "请输入qq号码！",
					maxlength: "您的qq号码不能超过20个字符长！"
				},
			}
		});
	});
	
	$().ready(function() {
		$("#adminaddForm").validate({
			rules: {
				username: {
					required: true,
				},
				password: {
					required: true,
					minlength: 5,
				},
				mobile: {
					required: true,
					isMobile: true
				},
				qq: {
					required: true,
					maxlength: 20
				},
				email: {
					required: true,
					email: true
				},
			},
			messages: {
				username: "用户名不能为空！",
				password: {
					required: "请输入登录密码！",
					minlength: "您的密码必须至少有5个字符长"
				},
				mobile: "请输入正确的手机号！",
				email: "请输入正确的邮箱号！",
				qq: {
					required: "请输入qq号码！",
					maxlength: "您的qq号码不能超过20个字符长！"
				},
			}
		});
	});
}();