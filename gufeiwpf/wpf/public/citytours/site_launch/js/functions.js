<!-- Count down -->		
	setInterval(function() {
		var target = new Date("June 15 2015 13:30:00 GMT+0100"); //replace with YOUR DATE
		var now = new Date();
		var difference = Math.floor((target.getTime() - now.getTime()) / 1000);

		var seconds = fixIntegers(difference % 60);
		difference = Math.floor(difference / 60);

		var minutes = fixIntegers(difference % 60);
		difference = Math.floor(difference / 60);

		var hours = fixIntegers(difference % 24);
		difference = Math.floor(difference / 24);

		var days = difference;
	
		$(".countdown #seconds").html(seconds);
		$(".countdown #minutes").html(minutes);
		$(".countdown #hours").html(hours);
		$(".countdown #days").html(days);

		
	}, 1000); 
	function fixIntegers(integer) {
		if (integer < 0)
			integer = 0;
		if (integer < 10)
			return "0" + integer;
		return "" + integer;
	}
	
<!-- Quantity input -->     
jQuery(document).ready(function(){
    // This button will increment the value
    $('.qtyplus').click(function(e){
        // Stop acting like a button
        e.preventDefault();
        // Get the field name
        fieldName = $(this).attr('name');
        // Get its current value
        var currentVal = parseInt($('input[name='+fieldName+']').val());
        // If is not undefined
        if (!isNaN(currentVal)) {
            // Increment
            $('input[name='+fieldName+']').val(currentVal + 1);
        } else {
            // Otherwise put a 0 there
            $('input[name='+fieldName+']').val(1);
        }
    });
    // This button will decrement the value till 0
    $(".qtyminus").click(function(e) {
        // Stop acting like a button
        e.preventDefault();
        // Get the field name
        fieldName = $(this).attr('name');
        // Get its current value
        var currentVal = parseInt($('input[name='+fieldName+']').val());
        // If it isn't undefined or its greater than 0
        if (!isNaN(currentVal) && currentVal > 0) {
            // Decrement one
            $('input[name='+fieldName+']').val(currentVal - 1);
        } else {
            // Otherwise put a 0 there
            $('input[name='+fieldName+']').val(0);
        }
    });

});
<!-- DATEPICKER -->        
var nowTemp = new Date();
var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
   	$('#check_in, #check_out').datepicker({
    autoclose: true,
	todayHighlight: true
});

// Open modal window on click  
jQuery(document).ready(function(){
		$('#modal-offers-open').on('click', function(e) {
			var mod = $('#main'),
				modal = $('#modal-offers');
				mod.animate({ opacity: 0 }, 400, function(){
				$('html,body').scrollTop(0);
				modal.addClass('modal-active').fadeIn(400);
			});
			e.preventDefault();

			$('.modal-close').on('click', function(e) {
				modal.removeClass('modal-active').fadeOut(400, function(){
					mod.animate({ opacity: 1 }, 400);
				});
				e.preventDefault();
			});
		});
		
		$('#modal-weather-open').on('click', function(e) {
			var mod = $('#main'),
				modal = $('#modal-weather');
				mod.animate({ opacity: 0 }, 400, function(){
				$('html,body').scrollTop(0);
				modal.addClass('modal-active').fadeIn(400);
			});
			e.preventDefault();

			$('.modal-close').on('click', function(e) {
				modal.removeClass('modal-active').fadeOut(400, function(){
					mod.animate({ opacity: 1 }, 400);
				});
				e.preventDefault();
			});
		});
		
		$('#modal-notified-open').on('click', function(e) {
			var mod = $('#main'),
				modal = $('#modal-notified');
				mod.animate({ opacity: 0 }, 400, function(){
				$('html,body').scrollTop(0);
				modal.addClass('modal-active').fadeIn(400);
			});
			e.preventDefault();

			$('.modal-close').on('click', function(e) {
				modal.removeClass('modal-active').fadeOut(400, function(){
					mod.animate({ opacity: 1 }, 400);
				});
				e.preventDefault();
			});
		});
		
		$('#modal-contacts-open').on('click', function(e) {
			var mod = $('#main'),
				modal = $('#modal-contacts');
 				mod.animate({ opacity: 0 }, 400, function(){
				$('html,body').scrollTop(0);
				modal.addClass('modal-active').fadeIn(400);
					//set up markers 
		var myMarkers = {"markers": [
				{"latitude": "51.511732", "longitude":"-0.123270", "icon": "img/map-marker2.png"}
			]
		};
		
		//set up map options
		$("#map").mapmarker({
			zoom	: 14,
			center	: 'Covent Garden London',
			markers	: myMarkers
		});
			});
			e.preventDefault();
	
			$('.modal-close').on('click', function(e) {
				modal.removeClass('modal-active').fadeOut(400, function(){
					mod.animate({ opacity: 1 }, 400);
				});
				e.preventDefault();
			});
		});
		
		});

/*--------------------------------------------------------
Weather
--------------------------------------------------------*/	  
$('#weather').weatherfeed(['SPXX0047'], {
		forecast: true
	});
//Pace holder
$('input, textarea').placeholder();	