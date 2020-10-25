/*!
 * jQuery Validation Plugin 1.11.1
 *
 *自定义验证规则
 */
/*
 * 手机号码验证
 */
jQuery.validator.addMethod("mobile", function(value, element) {
	var length = value.length;
	var mobile = /^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/;
	return this.optional(element) || (length == 11 && mobile.test(value));
}, "手机号码格式错误");
// ------------------------------------------------------------------------
/*
 * 电话号码验证
 */
jQuery.validator.addMethod("phone", function(value, element) {
	var tel = /^(0[0-9]{2,3}\-)?([2-9][0-9]{6,7})+(\-[0-9]{1,4})?$/;
	return this.optional(element) || (tel.test(value));
}, "电话号码格式错误");
// ------------------------------------------------------------------------
/*
 * 邮政编码验证
 */
jQuery.validator.addMethod("zipCode", function(value, element) {
	var tel = /^[0-9]{6}$/;
	return this.optional(element) || (tel.test(value));
}, "邮政编码格式错误");
// ------------------------------------------------------------------------
/*
 * QQ号码验证
 */
jQuery.validator.addMethod("qq", function(value, element) {
	var tel = /^[1-9]\d{4,9}$/;
	return this.optional(element) || (tel.test(value));
}, "qq号码格式错误");
// ------------------------------------------------------------------------
/*
 * IP地址验证
 */
jQuery.validator
		.addMethod(
				"ip",
				function(value, element) {
					var ip = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
					return this.optional(element)
							|| (ip.test(value) && (RegExp.$1 < 256
									&& RegExp.$2 < 256 && RegExp.$3 < 256 && RegExp.$4 < 256));
				}, "Ip地址格式错误");
// ------------------------------------------------------------------------
/*
 * 字母和数字的验证
 */
jQuery.validator.addMethod("chrnum", function(value, element) {
	var chrnum = /^([a-zA-Z0-9]+)$/;
	return this.optional(element) || (chrnum.test(value));
}, "只能输入数字和字母(字符A-Z, a-z, 0-9)");
// ------------------------------------------------------------------------
/*
 * 只能包含中文、英文字母、数字、下划线、破折号
 */
jQuery.validator.addMethod("chinese", function(value, element) {
	// var chinese = /^([-a-z0-9_-])|([\u4e00-\u9fa5])+$/;
	var chinese = /^([\u4e00-\u9fa5-a-z0-9_-])+$/;
	return this.optional(element) || (chinese.test(value));
}, "只能包含中文、英文字母、数字、下划线、破折号");
// ------------------------------------------------------------------------
/*
 * 下拉菜单验证
 */
$.validator.addMethod("selectNone", function(value, element) {
    return value == "请选择"?false:true;
}, "必须选择一项");
// ------------------------------------------------------------------------
/*
 * 字节长度验证
 */
jQuery.validator.addMethod("byteRangeLength",
		function(value, element, param) {
			var length = value.length;
			for ( var i = 0; i < value.length; i++) {
				if (value.charCodeAt(i) > 127) {
					length++;
				}
			}
			return this.optional(element)
					|| (length >= param[0] && length <= param[1]);
		}, $.validator.format("请确保输入的值在{0}-{1}个字节之间(一个中文字算2个字节)"));
// ------------------------------------------------------------------------
/*
 * 文件夹名称验证
 */
$.validator.addMethod("filename", function(value, element) {
	var filename = /^([a-zA-Z0-9\.]+)$/;
	return this.optional(element) || (filename.test(value));
}, "只能输入.、字母、数字");
/*
 * 正整数
 */
$.validator.addMethod("positive_integer", function(value, element) {
	var positive_integer = /^[0-9]*[1-9][0-9]*$/;
	return this.optional(element) || (positive_integer.test(value));
}, "只能输入正整数");
// ------------------------------------------------------------------------
/*
 * 只能包含英文字母、数字、下划线、破折号
 */
$.validator.addMethod("icon_filename", function(value, element) {
	var icon = /^([-a-z0-9_-])+$/;
	return this.optional(element) || (icon.test(value));
}, "只能包含英文字母、数字、下划线、破折号");
// ------------------------------------------------------------------------
/*
 * 只能包含英文字母、数字、下划线、斜线
 *
 */
$.validator.addMethod("url_filename", function(value, element) {
	var url = /^([-a-z0-9_\/])+$/;
	return this.optional(element) || (url.test(value));
}, "只能包含英文字母、数字、下划线、斜线");
// ------------------------------------------------------------------------
/*
 *只能包含英文字母、数字、下划线、斜线、破折号
 */
$.validator.addMethod("alpha_dash_bias", function(value, element) {
    var password = /^([-a-z0-9_\/\\\-])+$/;
    return this.optional(element) || (password.test(value));
}, "只能包含英文字母、数字、下划线、斜线、破折号");
// ------------------------------------------------------------------------