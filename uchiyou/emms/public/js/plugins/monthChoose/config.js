$(function(){
	$('.single-slider').jRange({
		from: 1,
		to: 48,
		step: 1,
		scale: [1,12,24,36,48],
		format: '%s个月',
		width: 700,
		showLabels: true,
		showScale: true
	});
});