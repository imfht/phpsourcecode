
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






