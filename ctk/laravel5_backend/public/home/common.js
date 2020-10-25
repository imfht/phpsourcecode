//处理表单提单按钮，显示loading，禁用，启用。
function setformSubmitButton() {
    //模拟submit
    $(document).on('click', '.sys-btn-submit', function () {
        var isClick = false;
        var sysBtnSubmitObject = $(this);
        var has_Object_Form_Init = sysBtnSubmitObject.data('form-init') || false;
        var has_Object_Body_Init = sysBtnSubmitObject.data('body-init') || false;
        
        var oldText = sysBtnSubmitObject.find('.sys-btn-submit-str').html();

        //处理表单提交
        if( ! has_Object_Form_Init) {
            sysBtnSubmitObject.closest('form').submit(function(){
                var loading = sysBtnSubmitObject.attr('data-loading') || 'loading...';
                sysBtnSubmitObject.find('.sys-btn-submit-str').html(loading);
                sysBtnSubmitObject.attr('disabled', 'disabled');
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
 * @return void
 */
function alertNotic(content) {
    var d = dialog({
        title: '提示',
        content: content,
        okValue: '确定',
        width: 250,
        ok: function () {}
    });
    d.showModal();
}

$(document).ready(function(){
    setformSubmitButton();
});