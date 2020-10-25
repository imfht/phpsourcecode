/**
 * Created by Administrator on 14-6-30.
 */
/**
 * 表单验证
 */
var obj;
var checkCan = new Array();
var patterns = new Object();
//匹配ip地址
patterns.Ip = /^(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])(\.(\d{1,2}|1\d\d|2[0-4]\d|25[0-5])){3}$/;
//匹配邮件地址
patterns.Email = /^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
//匹配日期格式2008-01-31，但不匹配2008-13-00
patterns.Date = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2]\d|3[0-1])$/;
/*匹配时间格式00:15:39，但不匹配24:60:00，下面使用RegExp对象的构造方法
 来创建RegExp对象实例，注意正则表达式模式文本中的“\”要写成“\\”*/
patterns.Time = new RegExp("^([0-1]\\d|2[0-3]):[0-5]\\d:[0-5]\\d$");
patterns.Num = /^[0-9]*$/;
patterns.DateAndTime = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|[1-2]\d|3[0-1])$/;
patterns.Phone =/^(1[3|4|5|8])[0-9]{9}$/ ;

$(function () {
    $('.form_check').after('<span class="glyphicon form-control-feedback"></span><div class=" show_info" ></div>');
    $('.form_check').click(function () {
    })
    $('.form_check').focus(function () {
    })
    $('.form_check').blur(function () {
        obj = $(this);
        var type = obj.attr('check-type');
        var isEmpty = checkEmpty();
        eval('var check = check' + type + '()');
        if (isEmpty && check.status) {
            var html = ' <span class="glyphicon glyphicon-ok form-control-feedback"></span>';
            obj.parent().find('.glyphicon').replaceWith(html);
            obj.parent().find('.show_info').html('');
            checkCan[obj.attr('name')] = 1;
        } else if (!isEmpty) {
            var html = ' <span class="glyphicon glyphicon-remove form-control-feedback"></span>';
            obj.parent().find('.glyphicon').replaceWith(html);
            showInfo('不能为空！');
            checkCan[obj.attr('name')] = 0;
        } else if (!check.status) {
            var html = ' <span class="glyphicon glyphicon-remove form-control-feedback"></span>';
            obj.parent().find('.glyphicon').replaceWith(html);
            showInfo(check.info);
            checkCan[obj.attr('name')] = 0;
        }

    })
    $('.form_check').change(function () {
        $(this).blur();
    })
    /**
     *
     */
    $(":submit").click(function(e){
        var canDubmit = true;
    for(var key in checkCan){
        canDubmit = canDubmit & checkCan[key];
    }
        if(!canDubmit){
            toast.error('请填写完整且正确的信息后再提交！')
            e.preventDefault();
            return false;
        }
    })

})
/**
 * 检查是否为空
 * @returns {boolean}
 */
var checkEmpty = function () {
    if (obj.val().length == 0) {
        return false;
    } else {
        return true;
    }
}
/**
 * 验证文本框
 * @returns {{status: number, info: string}}
 */
var checkText = function () {
    var res = {status: 1, info: ''}
    return res;
}
/**
 * 验证日期
 * @returns {{status: number, info: string}|{status: number, info: string}}
 */
var checkDate = function () {
    var str = obj.val();
    if (patterns['Date'].test(str)) {
        var res = {status: 1, info: ''}
    }
    else {
        var res = {status: 0, info: '请填写正确的格式！'}
    }
    return res;
}
/**
 * 验证数字
 * @returns {{status: number, info: string}|{status: number, info: string}}
 */
var checkNum = function () {
    var str = obj.val();
    if (patterns['Num'].test(parseInt(str))) {
        var res = {status: 1, info: ''}
    }
    else {
        var res = {status: 0, info: '请填写数字！'}
    }
    return res;
}


/**
 * 验证手机
 * @returns {{status: number, info: string}|{status: number, info: string}}
 */
var checkPhone = function () {
    var str = obj.val();
    if (patterns['Phone'].test(parseInt(str))) {
        var res = {status: 1, info: ''}
    }
    else {
        var res = {status: 0, info: '请填写手机号码！'}
    }
    return res;
}
/**
 * 显示提示信息
 * @param str
 */
var showInfo = function (str) {
    var html = '<div class="send"><div class="arrow"></div>' + str + '</div>';
    obj.parent().find('.show_info').html(html);

}