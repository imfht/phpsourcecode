
/**
 * 点击按钮切换当前按钮状态
 */
$('.btn-loading').click(function () {
    var $btn = $(this)
    $btn.button('loading');
    setTimeout(function () {
        $btn.button('reset');
    }, 5000);
});
/**
 * 返回顶部
 */
$('#amz-toolbar').click(function () {
    $('html, body').animate({scrollTop: 0}, '500');
    return false;
});

/**
 * 文件上传
 */
$(function () {
    $('#picture').on('change', function () {
        var fileNames = '';
        $.each(this.files, function () {
            fileNames += '<span class="am-badge">' + this.name + '</span> ';
        });
        $('#file-list').html(fileNames);
    });
});