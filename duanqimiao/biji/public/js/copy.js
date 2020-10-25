/**
 * Created by Administrator on 2016/11/23 0023.
 */
jQuery( document ).ready(function( $ ) {
    //点击文本框复制其内容到剪贴板上方法
    $('.copy-biji-link').zclip({
        path: 'js/ZeroClipboard.swf',
        copy: function(){
            return $('input[name=biji-link]').val();
        },
        afterCopy: function(){//复制成功
            var d = dialog({
                title: '提示',
                content: '已复制到剪贴板！',
                width: 220
            });
            d.show();
            setTimeout(function () {
                d.close().remove();
            }, 3000);
        }
    });
});
