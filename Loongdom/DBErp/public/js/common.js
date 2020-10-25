/*$('.select-all-checkbox').on('ifChecked', function(event){
    $("input[name='select_id[]']").iCheck('check');
});
$('.select-all-checkbox').on('ifUnchecked', function(event){
    $("input[name='select_id[]']").iCheck('uncheck');
});*/

$(function () {
    /*$('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue'
    });*/

    $(".select-all-checkbox").click(function () {
        var checkState = this.checked;
        if(!checkState) {
            $("input[name='select_id[]']").prop("checked", false);
            $(".select-all-checkbox").prop("checked", false);
        } else {
            $("input[name='select_id[]']").prop("checked", true);
        }
    });
});

//通用获取列表方法
function dberpAjaxList(listUrl,showDivDd) {
    $.get(listUrl,{showDivDd:showDivDd}, function(html){
        $("#"+showDivDd).html(html);
    });
}

/**
 * 删除问询
 * @param msg
 * @param url
 * @param toUrlState
 */
function deleteConfirm(msg, url, toUrlState) {
    layer.confirm(msg, {}, function () {
        if(toUrlState == 'false') {
            $.get(url, {}, function (data) {
                if(data.state == 'ok') window.location.reload();
                else {
                    if(data.hasOwnProperty("msg")) {
                        layer.msg(data.msg);
                    } else window.location.reload();
                }
            });
        } else window.location.href = url;
    })
}