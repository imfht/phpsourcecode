/**
 * 处理表单提单按钮，显示loading，禁用，启用。
 * 
 * @return void
 */
function setformSubmitButton() {
    //form submit
    $(document).on('click', '.sys-btn-submit', function () {
        var isClick = false;
        var sysBtnSubmitObject = $(this);
        var has_Object_Form_Init = sysBtnSubmitObject.data('form-init') || false;
        var has_Object_Body_Init = sysBtnSubmitObject.data('body-init') || false;
        
        var oldText = sysBtnSubmitObject.find('.sys-btn-submit-str').html();

        //处理表单提交
        if( ! has_Object_Form_Init) {

            function initLoding() {
                var loading = sysBtnSubmitObject.attr('data-loading') || 'loading...';
                sysBtnSubmitObject.find('.sys-btn-submit-str').html(loading);
                sysBtnSubmitObject.attr('disabled', 'disabled');
            }

            sysBtnSubmitObject.closest('form').submit(function(){
                initLoding();
                isClick = true;
            });

            sysBtnSubmitObject.data('form-init', true);
        }

        //取消按钮锁定
        if( ! has_Object_Body_Init) {
            $("body").on('click', function(){
                if(isClick){
                    sysBtnSubmitObject.removeAttr('disabled');
                    sysBtnSubmitObject.find('.sys-btn-submit-str').html(oldText);
                }
                isClick = false;
            });
           sysBtnSubmitObject.data('body-init', true);
        }

        sysBtnSubmitObject.closest('form').submit();

        return false;
    });

}

/**
 * 自定义的confirm确认弹出窗口
 * 
 * @param  string   content  提示的内容
 * @param  function callback 回调函数
 * @return void
 */
function confirmNotic(content, callback) {
    var d = dialog({
        title: '提示',
        fixed: true,
        content: content,
        okValue: '确定',
        width: 250,
        ok: function () {
            if(typeof callback === 'function') {
                this.title('提交中…');
                callback();
            }
        },
        cancelValue: '取消',
        cancel: function () {}
    });
    d.showModal();
}

/**
 * 自定义的alert提示弹窗
 * 
 * @param  string content 提示的内容
 * @param  function callback 回调函数
 * @return void
 */
function alertNotic(content, callback) {
    var d = dialog({
        title: '提示',
        fixed: true,
        content: content,
        okValue: '确定',
        width: 250,
        ok: function () {
            if(callback && typeof callback === 'function') {
                callback();
            }
        }
    });
    d.showModal();
}

/**
 * 异步删除
 * 
 * @param  {string} url       执行的url
 * @param  {string} replaceID 用于刷新列表的容器id
 * @param  {string} notice    提示信息
 * @return {void}
 */
function ajaxDelete(url, replaceID, notice) {
    confirmNotic(notice, function() {
        $.ajax({
            type:     'GET', 
            url:      url,
            dataType: 'json', 
            success:  function(data) {
                if(data.result == 'success') {
                    $('#' + replaceID).wrap("<div id='tmpDiv'></div>");
                    $('#tmpDiv').load(document.location.href + ' #' + replaceID, function(){
                        $('#tmpDiv').replaceWith($('#tmpDiv').html());
                    });
                } else {
                    alertNotic(data.message);
                }
            },
            beforeSend: function() {
                loading();
            },
            complete: function() {
                unloading();
            }
        });
    });
}

/**
 * 显示loading，用于处理数据的时候显示
 * 
 * @return void
 */
function loading() {
    var loading_image = '<img src="'+SYS_DOMAIN+'/images/loading-icons/loading9.gif">';
    $.blockUI({
        message: loading_image,
        css: {
            border: 'none', 
            padding: '0px', 
            backgroundColor: 'none'
        }
    }); 
}

/**
 * 关闭loading
 * 
 * @return void
 */
function unloading() {
    $.unblockUI();
}

/**
 * 上传弹出窗口
 * 
 * @param  {string} uploadid   dialog插件的ID
 * @param  {string} title      dialog插件的标题
 * @param  {string} itemId     回调函数用到的html ID
 * @param  {function} funcName 回调函数
 * @param  {string} args       附带的参数
 * @param  {string} authkey    当前上传窗口的签名token,防止篡改
 * @param  {string} upload_url 处理上传的接口
 * @return {void}
 */
function uploaddialog(uploadid, title, itemId, funcName, args, authkey, upload_url) {
    var args = args ? '&args=' + args : '';
    var setting = '&authkey=' + authkey;
    var d = dialog({
        title: title,
        id: uploadid,
        url: upload_url+'?_=' + Math.random() + args + setting,
        width: '500',
        height: '420',
        padding: 0,
        okValue: '确定',
        ok: function () {
            this.title('提交中…');
            if (funcName) {
                funcName.apply(this, [uploadid, itemId]);
            }
            this.close().remove();
            removeDialogIframe(uploadid);
            return false;
        },
        cancelValue: '取消',
        cancel: function () {
            this.close().remove();
            removeDialogIframe(uploadid);
            return false;
        }
    });
    d.showModal();
}

/**
 * artdialog关闭后还会有一个iframe，删除它
 * @param  {[string]} uploadid dialog插件的id
 * @return {[void]}
 */
function removeDialogIframe(uploadid) {
    $('body').find('iframe[name="'+uploadid+'"]').remove();
}

/**
 * onload的时候改变菜单的高度
 *
 * @return {void}
 */
function changeLeftMenuHeight() {
    var divContent = $('div.content');
    var winHeight = $(window).height();
    if(winHeight < 800) winHeight = 800;
    divContent.css('min-height', winHeight);
    var contentHeight = divContent.height();
    $('div.sidebar-nav').css('min-height', contentHeight+15);
}

/**
 * 简单的a标签ajax提交
 * @param {string} url       所要提交的地址
 * @param {object} paramObj  传递的参数
 * @param {string} ajaxType  post|get
 * @param {object} selectObj 当前按钮的选择器
 */
function Atag_Ajax_Submit(url, paramObj, ajaxType, selectObj, replaceID, showSuccessMsg) {
    //ajax submit
    var _oldstr = selectObj.find('.sys-btn-submit-str').html();
    $.ajax({
        type: ajaxType, 
        url: url,
        data: paramObj,
        dataType: 'json',
        success:  function(data) {
            if(data.result == 'success' && replaceID) {
                $('#' + replaceID).wrap("<div id='tmpDiv'></div>");
                $('#tmpDiv').load(document.location.href + ' #' + replaceID, function(){
                    $('#tmpDiv').replaceWith($('#tmpDiv').html());
                });
                if(showSuccessMsg) {
                    alertNotic(data.message);
                }
            } else {
                alertNotic(data.message);
            }
        },
        beforeSend: function() {
            var loading = selectObj.attr('data-loading') || 'loading...';
            selectObj.find('.sys-btn-submit-str').html(loading);
            selectObj.attr('disabled', 'disabled');
        },
        complete: function() {
            selectObj.removeAttr('disabled');
            selectObj.find('.sys-btn-submit-str').html(_oldstr);
        }
    });
}

/**
 * 批量操作的时候取得checkbox的值
 *
 * @param {string} _class 即css的class名
 * @return {array}
 */
function plSelectValue(_class) {
    var c = _class || 'ids';
    var ids = new Array();
    var current_var;
    $('input.'+c+':checked').each(function(i, n){
        current_var = $(n).val();
        ids.push(current_var);
    });
    return ids;
}

/**
 * select 下拉选择的自适应
 */
function formSelectWidth() {
    var _f = function(){
        var _w = $(window).width();
        var o = $('.zdy-form-select-obj');
        if(_w <= 751) {
            o.removeClass('zdy-form-select');
        } else {
            o.addClass('zdy-form-select');
        }
    };
    $(window).resize(_f);
    $(window).ready(_f);
}

/**
 * 初始化
 */
$(document).ready(function(){
    setformSubmitButton();
    changeLeftMenuHeight();
    formSelectWidth();
});