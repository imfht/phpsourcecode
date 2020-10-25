$(function(){
	    //ajax get请求
    $('.ajax-get').click(function () {
        var target;
        var that = this;
        if ($(this).hasClass('confirm')) {
            if (!confirm('确认要执行该操作吗?')) {
                return false;
            }
        }
        if ((target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            $.get(target).success(function (data) {
                if (data.code == 1) {
                    if (data.url) {
                        updateAlert(data.msg + ' 页面即将自动跳转~', 'success');
                    } else {
                        updateAlert(data.msg, 'success');
                    }
                    setTimeout(function () {
                        if (data.url) {
                            location.href = data.url;
                        } else if ($(that).hasClass('no-refresh')) {
                            $('#top-alert').find('button').click();
                        } else {
                            location.reload();
                        }
                    }, 3000);
                } else {
                    updateAlert(data.msg);
                    setTimeout(function () {
                        if (data.url) {
                            location.href = data.url;
                        } else {
                            $('#top-alert').find('button').click();
                        }
                    }, 15000);
                }
            });

        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function () {
        //show loading
        toast.showLoading();
        var target, query, form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm = false;
        if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
            form = $('.' + target_form);

            if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                form = $('.hide-data');
                query = form.serialize();
            } else if (form.get(0) == undefined) {
                updateAlert('没有可操作数据。','danger');
                return false;
            } else if (form.get(0).nodeName == 'FORM') {
                if ($(this).hasClass('confirm')) {
                    var confirm_info = $(that).attr('confirm-info');
                    confirm_info=confirm_info?confirm_info:"确认要执行该操作吗?";
                    if (!confirm(confirm_info)) {
                        toast.hideLoading();
                        return false;
                    }
                }
                if ($(this).attr('url') !== undefined) {
                    target = $(this).attr('url');
                } else {
                    target = form.get(0).action;
                }
                query = form.serialize();
            } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                form.each(function (k, v) {
                    if (v.type == 'checkbox' && v.checked == true) {
                        nead_confirm = true;
                    }
                })
                if (nead_confirm && $(this).hasClass('confirm')) {
                    var confirm_info = $(that).attr('confirm-info');
                    confirm_info=confirm_info?confirm_info:"确认要执行该操作吗?";
                    if (!confirm(confirm_info)) {
                        toast.hideLoading();
                        return false;
                    }
                }
                query = form.serialize();
            } else {
                if ($(this).hasClass('confirm')) {
                    var confirm_info = $(that).attr('confirm-info');
                    confirm_info=confirm_info?confirm_info:"确认要执行该操作吗?";
                    if (!confirm(confirm_info)) {
                        toast.hideLoading();
                        return false;
                    }
                }
                query = form.find('input,select,textarea').serialize();
            }
            if(query==''&&$(this).attr('hide-data') != 'true'){
                toast.hideLoading();
                updateAlert('请勾选操作对象!。','danger');
                return false;
            }
            $(that).addClass('disabled').attr('autocomplete', 'off').prop('disabled', true);
            $.post(target, query).success(function (data) {
                //隐藏加载框
                
                if (data.code == 1) {
                    if (data.url) {
                        updateAlert(data.msg + ' 页面即将自动跳转~', 'success');
                    } else {
                        updateAlert(data.msg, 'success');
                    }
                    setTimeout(function () {
                        if (data.url) {
                            location.href = data.url;
                        } else if ($(that).hasClass('no-refresh')) {
                            $('#top-alert').find('button').click();
                            $(that).removeClass('disabled').prop('disabled', false);
                        } else {
                            location.reload();
                        }
                    }, 1500);
                } else {
                    updateAlert(data.msg,'error');
                    setTimeout(function () {
                        if (data.url) {
                            location.href = data.url;
                        } else {
                            $('#top-alert').find('button').click();
                            $(that).removeClass('disabled').prop('disabled', false);
                        }
                    }, 1500);
                }
            });
            toast.hideLoading();
        }
        return false;
    });

});

/**
 * 处理ajax返回结果
 */
function handleAjax(data) {

    //如果需要跳转的话，消息的末尾附上即将跳转字样
    if (data.url) {
        data.msg += '，页面即将跳转～';
    }

    //弹出提示消息
    if (data.code==1) {
        updateAlert(data.msg, 'success');
    } else {
        updateAlert(data.msg, 'danger');
    }

    //需要跳转的话就跳转
    var interval = 1.5*1000;
    if (data.url == "refresh") {
        setTimeout(function () {
            location.href = location.href;
        }, interval);
    } else if (data.url) {
        setTimeout(function () {
            location.href = data.url;
        }, interval);
    }
}