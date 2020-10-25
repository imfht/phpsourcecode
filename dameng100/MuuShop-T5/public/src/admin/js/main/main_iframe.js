
//dom加载完成后执行的js
;$(function () {

    //全选的实现
    $(".check-all").click(function () {
        $(".ids").prop("checked", this.checked);
    });
    $(".ids").click(function () {
        var option = $(".ids");
        option.each(function (i) {
            if (!this.checked) {
                $(".check-all").prop("checked", false);
                return false;
            } else {
                $(".check-all").prop("checked", true);
            }
        });
    });


    // 独立域表单获取焦点样式
    $(".text").focus(function () {
        $(this).addClass("focus");
    }).blur(function () {
        $(this).removeClass('focus');
    });
    $("textarea").focus(function () {
        $(this).closest(".textarea").addClass("focus");
    }).blur(function () {
        $(this).closest(".textarea").removeClass("focus");
    });
});

var admin_image ={
    /**
     *
     * @param obj
     * @param attachId
     */
    removeImage: function (obj, attachId) {
        // 移除附件ID数据
        this.upAttachVal('del', attachId, obj);
        obj.parents('.each').remove();

    },
    /**
     * 更新附件表单值
     * @return void
     */
    upAttachVal: function (type, attachId,obj) {
        var $attach_ids = obj.parents('.controls').find('.attach');
        var attachVal = $attach_ids.val();
        var attachArr = attachVal.split(',');
        var newArr = [];
        for (var i in attachArr) {
            if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
                newArr.push(attachArr[i]);
            }
        }
        type === 'add' && newArr.push(attachId);
        $attach_ids.val(newArr.join(','));
        return newArr;
    }
}

/**
* 消息提示
* @param  {[type]} text [description]
* @param  {[type]} c    [description]
* @return {[type]}      [description]
*/
window.updateAlert = function (text, c) {

if(typeof c !='undefined')
{
    var msg = $.zui.messager.show(text, {placement: 'bottom',type:c});
}else {
    var msg =  $.zui.messager.show(text, {placement: 'bottom'})
}
msg.show();
};


var moduleManager = {
    'install': function (id) {
        $.post(Url('admin/module/install'),{id:id},function(msg){
            handleAjax(msg);
        })
    },
    'uninstall': function (id) {
        $.post(Url('admin/module/uninstall'),{id:id},function(msg){
            handleAjax(msg);
        })

    }
}

/**
 * 模拟Url函数
 * @param url
 * @param params
 * @returns {string}
 * @constructor
 */
function Url(url, params, rewrite) {

    var website = '/index.php';
    url = url.split('/');
    if (url[0] == '' || url[0] == '@')
        url[0] = APPNAME;
    if (!url[1])
        url[1] = 'Index';
    if (!url[2])
        url[2] = 'index';
    website = website + '?s=/' + url[0] + '/' + url[1] + '/' + url[2];
    if (params) {
        params = params.join('/');
        website = website + '/' + params;
    }
    if (!rewrite) {
        website = website + '.html';
    }
    return website;
}

/**
 * 操纵toastor的便捷类
 * @type {{success: success, error: error, info: info, warning: warning}}
 */
var toast = {
    /**
     * 成功提示
     * @param text 内容
     * @param title 标题
     */
    success: function (text) {
        toast.show(text, {placement: 'center', type: 'success',close: true});
    },
    /**
     * 失败提示
     * @param text 内容
     * @param title 标题
     */
    error: function (text) {
        toast.show(text, {placement: 'center', type: 'danger',close: true});
    },
    /**
     * 信息提示
     * @param text 内容
     * @param title 标题
     */
    info: function (text) {
        toast.show(text, {placement: 'center', type: 'info',close: true});
    },
    /**
     * 警告提示
     * @param text 内容
     * @param title 标题
     */
    warning: function (text, title) {
        toast.show(text, {placement: 'center',type:'warning',close: true});
    },

    show: function (text, option) {
        var zui = $.zui;
        if (zui) {
            $.zui.messager.show(text, option);
        }else{
            $.messager.show(text, option);
        }
    },
    /**
     *  显示loading
     * @param text
     */
    showLoading: function () {
        $('body').append('<div class="big_loading"><img src="/static/common/images/big_loading.gif"/></div>');
    },
    /**
     * 隐藏loading
     * @param text
     */
    hideLoading: function () {
        $('div').remove('.big_loading');
    }
}




