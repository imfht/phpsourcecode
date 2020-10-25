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
	$(".navbar").mouseover(function() {
        $(".navbar").removeClass("closed");
        return setTimeout((function() {
            return $(".navbar").css({
                overflow: "visible"
            });
        }), 350);
    });

	$('.scrollbar').ClassyScroll({
        sliderOpacity: 1,
        wheelSpeed: 2,
        onscroll: function() {
            return $(this).prev().addClass("shadow");
        }
    });

	$( "form[valid]" ).validVal();

    $('[ajax-confirmation]').confirmation({
        onConfirm:function(event, element){
            event.preventDefault();
            var url =  $(element).attr('href');
            $.get(url,function(){
                window.location.reload();
            });
        }
    });
    $('[ajax-dialog]').on('click',function(event){
        event.preventDefault();
        var url = $(this).attr('href');
        BootstrapDialog.show({
            message: $('<div></div>').load(url),
        });
    })

    

	$("[fancybox]").fancybox({
        maxWidth: 700,
        height: 'auto',
        fitToView: false,
        autoSize: true,
        padding: 15,
    });

    $('.scrollbar').ClassyScroll({
        sliderOpacity: 1,
        wheelSpeed: 2,
        onscroll: function() {
            return $(this).prev().addClass("shadow");
        }
    });

    $('[ajax-submit]').on('click',function(e){
    	e.preventDefault();
    	var $form = $(this).parents('form');
    	var url = $form.attr('action');
    	var form_data = $form.triggerHandler('submitForm');
        console.log(form_data);
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