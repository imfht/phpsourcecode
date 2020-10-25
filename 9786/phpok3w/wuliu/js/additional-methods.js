//用户名验证，只能输入英文字母、数字、下划线
jQuery.validator.addMethod("usernameInputCheck", function(value, element) {
    var username = /^(?!\d+$)[_a-zA-Z0-9]{6,20}$/;       
    return this.optional(element) || username.test(value);  
}, "请输入6-20个包括英文字母、数字和下划线的字符");

//登录密码验证，只能输入英文字母、数字、下划线
jQuery.validator.addMethod("passwordInputCheck", function(value, element) {
    var username = /^(?!\d+$)[_a-zA-Z0-9]{6,16}$/;       
    return this.optional(element) || username.test(value);  
}, "请输入6-16个包含英文字母、数字和下划线的字符，密码不能为纯数字");

//真实姓名验证，只能输入中文
jQuery.validator.addMethod("chOnlyInputCheck", function(value, element) {
    return this.optional(element) || /^[\u4e00-\u9fa5]*$/.test(value);
}, "请输入中文");

//企业名称验证，只能输入中文，并且可以包含"()"
jQuery.validator.addMethod("corpnameInputCheck", function(value, element) {
    return this.optional(element) || /^[（|\(|\u4e00-\u9fa5|）|\)]*$/.test(value);
}, "请输入中文可以包含括号");

//手机号码验证，包括150,153,156,158,159，157，188，189,186
jQuery.validator.addMethod("mobileInputCheck", function(value, element) {
    return this.optional(element) || /^(13[0-9]|15[0-9]|18[0-9]|14[0-9])\d{8}$/.test(value);
}, "请输入正确的手机号码");

//固定电话验证, 例如0511-4405222 或 021-87888822
jQuery.validator.addMethod("telInputCheck", function(value, element) {
    return this.optional(element) || /^((((\d{3}[-]){0,1})|((\d{4}[-]){0,1})){0,1}\d{7,9})$/.test(value);
}, "请输入正确的固定电话，例如：0531-67750855或67750855");

//不能全部为数字
jQuery.validator.addMethod("isNumber", function(value, element){
    return this.optional(element) || !/^\d*$/.test(value);
}, "不能全部为数字");

//不能全部为字母
jQuery.validator.addMethod("isCharacter", function(value, element){
    return this.optional(element) || !/^[a-zA-Z]+$/.test(value);
}, "不能全部为字母");
//验证网址
jQuery.validator.addMethod("isUrl", function(value, element){
    return this.optional(element) || /^http(s)?:\/\/(www\.)?[\w-]+(\.[\w]+)*(\/(\/)?[\w-]*(\.[\w]+)*)*$/.test(value);
}, "请输入正确的网址，如：www.baidu.com 或  http://baidu.com");
/*
 * 时间比较，加在较晚的时间文本框上，参数为较早的时间文本框选择符
 */
jQuery.validator.addMethod("compareDate", function(value, element, param) {
    var later;
    if(window.ActiveXObject){
         later = new Date(Date.parse(value.replace("-", "/")));
    }else{
         later = new Date(value);
    }
    later.setHours(0, 0, 0, 0);
    if (param == "now") {
        var nowDate = new Date();
        nowDate.setHours(0, 0, 0, 0);
        return nowDate <= later;
    } else {
        var startDate = jQuery(param).val();
        var earlier;
        if(window.ActiveXObject){
            earlier = new Date(Date.parse(startDate.replace("-", "/")));
        }else{
            earlier = new Date(startDate);
        }
        earlier.setHours(0, 0, 0, 0);
        return earlier <= later;
    }
});
//发布联系人验证，包括150,153,156,158,159，157，188，189,186，或固话格式
jQuery.validator.addMethod("telAndMobileInputCheck", function(value, element) {
    return this.optional(element) || /^((13[0-9]|15[0-9]|18[0-9]|14[0-9])\d{8}$)|(\d{3}-\d{7,9})|(\d{4}-\d{7,9})$/.test(value);
}, "请输入正确的联系方式，可以为固定电话或手机号，固话格式如：0531-67750855");