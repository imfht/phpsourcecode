//dom加载完成后执行的js
$(function() {
    window.ajaxJumpUrl = '';
    // ajaxForm
    $(".ajaxForm").submit(function() {
        var d = dialog({
            modal: true,
            fixed: true,
            width: '400px',
            title: "正在提交...",
        });

        d.addEventListener('close', function() {
            if ('' != window.ajaxJumpUrl) {
                location.href = window.ajaxJumpUrl;
            }
        });
        // submit the form 
        $(this).ajaxSubmit({
            beforeSend: function() {
                d.show();
            },
            success: function(data) {
                if (data && data.code) {
                    var msg = data.msg || '操作完成';
                    d.title('完成 - 3秒自动关闭').content(msg);
                    window.ajaxJumpUrl = data.url || false;
                } else {
                    var msg = data.msg || '操作失败，请稍后再试！';
                    d.title('操作失败').content(msg);
                }
                setTimeout(function() {
                    d.close().remove();
                }, 3000);
            },
            error: function() {
                d.title('链接失败').content('链接失败！');
            },
            complete:function(event,xhr,options){
                // setTimeout(function() {
                //     d.close(false).remove();
                // }, 3000);
            }
        });
        // return false to prevent normal browser submit and page navigation 
        return false;
    });

    // ajaxGet
    $('.ajaxGet, .ajax-get').bind('click', function(event){
        if($(this).hasClass('confirm')){
            if(!confirm('确认要执行该操作吗?')){
                return false;
            }
        }
        var url = $(this).attr('href');
        var d = dialog({
            modal: true,
            fixed: true,
            width: '400px',
            title: "正在提交...",
        });
        d.addEventListener('close', function() {
            if ('' != window.ajaxJumpUrl) {
                location.href = window.ajaxJumpUrl;
            }
        });
        d.show();
        $.get(url, function(data, status){
            if(data.code){
                d.title('完成 - 3秒自动关闭').content(data.msg);
                window.ajaxJumpUrl = data.url;
            }else{
                d.title('操作失败').content(data.msg);
            }
            setTimeout(function() {
                d.close().remove();
            }, 3000);
        })
        return false;
    })
    // ajaxPost
    $('.ajaxPost').bind('click', function(event){
        event.preventDefault();
        var url = $(this).attr('href');
        $.post(url,{}, function(data, status){
        })
    })
})