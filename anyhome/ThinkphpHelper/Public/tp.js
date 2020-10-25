$(document).on('page:fetch',   function() { NProgress.start(); });
$(document).on('page:change',  function() { NProgress.done(); });
$(document).on('page:restore', function() { NProgress.remove(); });

$(document).ajaxStart(function(){
	NProgress.start();
}).ajaxComplete(function(){
	NProgress.done();
}).ajaxError(function(){
	$.bootstrapGrowl('未知的系统错误', {
        type: 'danger',
        align: 'center',
    });
})




$(function(){

    $('[ajax-confirmation]').confirmation({
        onConfirm:function(event, element){
            event.preventDefault();
            var url =  $(element).attr('href');
            $.get(url,function(){
                window.location.reload();
            });
        }
    });

	$( "form[valid]" ).validVal();

    $('[ajax-dialog]').on('click',function(event){
        event.preventDefault();
        var url = $(this).attr('href');
        BootstrapDialog.show({
            message: $('<div></div>').load(url),
        });
    })

    $('table.table-selected').find('tbody').find('tr').on('click',function(){
        if ($(this).hasClass('info')) {
            $(this).removeClass('info');
            $('.btn-edit').attr('disabled','disabled');
            $('.btn-delete').attr('disabled','disabled');
            return;
        };

        var pk = $(this).data('pk');

        $('table.table-selected').find('tr.info').removeClass('info');
        $(this).addClass('info');
        var editUrl = $('.btn-edit').attr('href');
        var deleteUrl = $('.btn-delete').attr('href');

        editUrl = urlParas(editUrl).set({"id":pk});
        deleteUrl = urlParas(deleteUrl).set({"id":pk});

        $('.btn-edit').attr('disabled',null);
        $('.btn-edit').attr('href',editUrl);
        $('.btn-delete').attr('disabled',null);
        $('.btn-delete').attr('href',deleteUrl);
    })

    $('[datetime]').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        forceParse: 0,
        showMeridian: 1
    });

    $('[date]').datetimepicker({
        language:  'zh-CN',
        weekStart: 1,
        todayBtn:  1,
        autoclose: 1,
        todayHighlight: 1,
        startView: 2,
        minView: 2,
        forceParse: 0
    });

    $('[ajax-submit]').on('click',function(e){
    	e.preventDefault();
    	var $form = $(this).parents('form');
    	var url = $form.attr('action');
    	var form_data = $form.triggerHandler('submitForm');
    	if ( form_data ) {
            var post_data = $form.serialize();
    		$.post(url,post_data,function(req){
	    		$.bootstrapGrowl(req['info'], {
	                type: 'danger',
	                align: 'center',
	            });
	    	})
    	}
    })
})