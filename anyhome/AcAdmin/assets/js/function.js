function U(arg,arg_val,url){ 
    if (!url) url = window.location.href;
    var pattern=arg+'=([^&]*)'; 
    var replaceText=arg+'='+arg_val; 
    if(url.match(pattern)){ 
        var tmp='/('+ arg+'=)([^&]*)/gi'; 
        tmp=url.replace(eval(tmp),replaceText); 
        return tmp; 
    }else{ 
        if(url.match('[\?]')){ 
            return url+'&'+replaceText; 
        }else{ 
            return url+'?'+replaceText; 
        } 
    } 
    return url+'\n'+arg+'\n'+arg_val; 
}

$(document).ajaxStart(function(){
    NProgress.start();
}).ajaxComplete(function(){
    NProgress.done();
}).ajaxError(function(){
    // $.bootstrapGrowl('未知的系统错误', {
    //     type: 'danger',
    //     align: 'center',
    // });
})
$.pjax.defaults.timeout = 3200;

$(function(){

    $(document).on('submit','form',function(){
        var f = $(this);
        var url  = f.attr('action');
        if(typeof(f.attr("submit-ajax"))!="undefined")
        {
            NProgress.start();
            var data = f.serialize();
            $.post(url,data,function(req){
                $.bootstrapGrowl(req.info);
                NProgress.done();
                if (req.status == 1) {
                    if (req.url == 'goback') {
                        history.go(-1);
                        return false;
                    };
                    if (req.url != '') {
                        var pageContentBody = $('.page-content-body');
                        if (pageContentBody.length > 0) {
                            $.pjax({url: req.url, container: pageContentBody});
                        }else{
                            window.location.href = req.url
                        }
                    };
                    if (f.parents('.bootstrap-dialog-body').length > 0) {
                        BootstrapDialog.closeAll();
                    };
                };

            })
            return false;
        }
    })

    $('select[data-value]').each(function(i,d){
        var v = $(this).data('value');
        if(v) $(this).val(v);
    })

    $(document).on('click','[data-ajax]',function(event){
        event.preventDefault();
        NProgress.start();
        var url = $(this).attr('href');
        $.get(url,function(req){
            $.bootstrapGrowl(req.info);
            NProgress.done();
        })
    })

    $('[data-refresh]').on('click',function(e){
        e.preventDefault();
        var container = $('.page-container');
        $.pjax.reload(container, {});
    })

    $(document).on('click','[data-dialog]',function(event){
        event.preventDefault();
        var box = $('<div style="max-height:500px;overflow-y: auto;" class="row"></div>');
        var size = $(this).attr('dialog-size');
        if (!size) size = 'size-wide';
        var url = $(this).attr('href');
        var title = $(this).attr('dialog-title');
        box.load(url);
        BootstrapDialog.show({
            message: box,
            size:size,
            title:title,
            buttons: [{
                label: '确定',
                action: function(dialogRef) {
                    var form = dialogRef.getModalBody().find('form');
                    if (form.length > 0) {
                        var url = form.attr('action');
                        var data = form.serialize();
                        $.post(url,data,function(req){
                            $.bootstrapGrowl(req.info);
                            if (req.status == 1) {
                                dialogRef.close();
                            }
                            if (req.url == 'reload') {
                                var container = $('.page-container');
                                $.pjax.reload(container, {});
                            };
                        })
                    }else{
                        dialogRef.close();
                    }
                }
            },{
                label: '取消',
                action: function(dialogRef){
                    dialogRef.close();
                }
            }]
        });
    })


    $('[data-pjax]').on('click',function(e){
        e.preventDefault();
        //animated bounceInDown
        $('.page').addClass('animated bounceOutUp');
        var url = $(this).attr('href');
        console.log(url);
        var obj = $(this);
        $('.page').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
            $(document).click();
            load(url);
        });
    })

    function load(url){
        var container = $('.page-container');
        $.pjax({url:url,container: container})
    }
})