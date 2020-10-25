$(document).ready(function() {
	//Fill text
	$('.text-fill  .progress-bar').progressbar({display_text: 'fill'});

	//fill text but not in percentage
	$('.text-fill-no-percent  .progress-bar').progressbar({
		display_text: 'fill',
		use_percentage: false
	});

	//fix text with center of the progress bar
	$('.text-fill-center  .progress-bar').progressbar({
		display_text: 'center',
		use_percentage: false,

	});

	//Transition delay
	$('#transition-delay').click(function() {
		$('.transition-delay .progress-bar').progressbar({transition_delay: 3000});
	});

	$('.progress .progress-bar').progressbar();

});