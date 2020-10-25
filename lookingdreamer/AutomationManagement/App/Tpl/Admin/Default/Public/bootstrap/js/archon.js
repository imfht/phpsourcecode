/*
*	 Author: Vijay Kumar
*	 Template: Archon - Flat & Responsive Bootstrap Admin Template
*	 Version: 1.0
*	 Bootstrap version: 3.0.0
*	 Copyright 2013 bootstrapguru
*	 www: http://bootstrapguru.com
*	 mail: support@bootstrapguru.com
*	 You can find our other themes on: https://bootstrapguru.com/themes/
---------------------------------------------------------------------------------------------- */


// jQuery $('document').ready(); function 
$('document').ready(function(){

	$(".settings-modal").click(function() {
	    $(this).modal();
	});    

	
	$('.sidebar .nav > li > ul > li.active').parent().css('display','block')
	
	//data table
	$('#example').dataTable({
	 	"sPaginationType": "bootstrap"
	});
	
	//closes dashboard alert after 5 seconds
	setTimeout(function() { $(".alert-dashboard").alert('close')  }, 10000);

	$('.finish-task').change(function(){
		if($(this).is(':checked'))
		{
			$(this).parent().addClass('finish');
		}
		else
		{
			$(this).parent().removeClass('finish');
		}
	});
	
	
	var $template = $(".template");

	var hash = 2;
	$(".btn-add-panel").on("click", function () {
	    var $newPanel = $template.clone();
	    $newPanel.find(".collapse").removeClass("in");
	    $newPanel.find(".accordion-toggle").attr("href",  "#" + (++hash))
	             .text("Dynamic panel #" + hash);
	    $newPanel.find(".panel-collapse").attr("id", hash).addClass("collapse").removeClass("in");
	    $("#accordion").append($newPanel.fadeIn());
	});
	
}); 	// jQuery $('document').ready(); function 


$(function() {
			$('.sortable').sortable();
		});

//SIDEBAR TOGGLE

$(function() {
	$(".toggle-sidebar").click(function () {
	  $('body').toggleClass("show-sidebar");
	});

	var contentMinHeight 	= $('.sidebar .wrapper').height();
	var frameMinHeight 		= $( document ).height();

	$('.frame').css('min-height', frameMinHeight);
	$('.content').css('min-height', contentMinHeight);
});

// Sidebar dropdown
$('.sidebar .nav > li > a.dropdown ').click(function(e){
		e.preventDefault();
		$(this).next('ul').slideToggle();
})


// PANELS

// panel close
$('.panel-close').click(function(e){
	e.preventDefault();
	$(this).parent().parent().parent().parent().fadeOut();
});

$('.panel-minimize').click(function(e){
	e.preventDefault();
	var $target = $(this).parent().parent().parent().next('.panel-body');
	if($target.is(':visible')) $('i',$(this)).removeClass('icon-chevron-up').addClass('icon-chevron-down');
	else 					   $('i',$(this)).removeClass('icon-chevron-down').addClass('icon-chevron-up');
	$target.slideToggle();
});
$('.panel-settings').click(function(e){
	e.preventDefault();
	$('#myModal').modal('show');
});

