$('document').ready(function(){
	$( ".slider-default" ).slider({
		animate: true,
		range: "min",
		value: 50,
		min: 10,
		max: 100,
		step: 10,	         
	});

});



function hexFromRGB(r, g, b) {
	var hex = [
	r.toString( 16 ),
	g.toString( 16 ),
	b.toString( 16 )
	];
	$.each( hex, function( nr, val ) {
		if ( val.length === 1 ) {
			hex[ nr ] = "0" + val;
		}
	});
	return hex.join( "" ).toUpperCase();
}
function refreshSwatch() {
	var red = $( "#red" ).slider( "value" ),
	green = $( "#green" ).slider( "value" ),
	blue = $( "#blue" ).slider( "value" ),
	hex = hexFromRGB( red, green, blue );
	$( "#swatch" ).css( "background-color", "#" + hex );
}
$(function() {
	$( "#red, #green, #blue" ).slider({
		orientation: "horizontal",
		range: "min",
		max: 255,
		animate: true,
		value: 127,
		slide: refreshSwatch,
		change: refreshSwatch
	});
	$( "#red" ).slider( "value", 255 );
	$( "#green" ).slider( "value", 140 );
	$( "#blue" ).slider( "value", 60 );


	$( "#master" ).slider({
		value: 60,
		orientation: "horizontal",
		range: "min",
		animate: true
	});
	// setup graphic EQ
	$( "#eq > span" ).each(function() {
		// read initial values from markup and remove that
		var value = parseInt( $( this ).text(), 10 );
		$( this ).empty().slider({
			value: value,
			range: "min",
			animate: true,
			orientation: "vertical"
		});
	});



	//Rang Slider
	$( "#slider-range" ).slider({
		range: true,
		min: 0,
		animate: true,
		max: 500,
		values: [ 75, 300 ],
		slide: function( event, ui ) {
			$( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
		}
	});
	$( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
	" - $" + $( "#slider-range" ).slider( "values", 1 ) );
	$( "#slider-range-max" ).slider({
		range: "max",
		min: 1,
		max: 10,
		animate:true,
		value: 2,
		slide: function( event, ui ) {
			$( "#amount-max" ).val( ui.value );
		}
	});
	$( "#amount-max" ).val( $( "#slider-range-max" ).slider( "value" ) );

	$( "#slider-range-min" ).slider({
		range: "min",
		value: 37,
		animate:true,
		min: 1,
		max: 700,
		slide: function( event, ui ) {
			$( "#amount-min" ).val( "$" + ui.value );
		}
	});
	$( "#amount-min" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );

	var select = $( "#minbeds" );
	var slider = $( "<div id='slider'></div>" ).insertAfter( select ).slider({
		min: 1,
		max: 6,
		animate:true,
		range: "min",
		value: select[ 0 ].selectedIndex + 1,
		slide: function( event, ui ) {
			select[ 0 ].selectedIndex = ui.value - 1;
		}
	});
	$( "#minbeds" ).change(function() {
		slider.slider( "value", this.selectedIndex + 1 );
	});
	
	$( "#slider-donate" ).slider({
    value:100,
    min: 0,
    max: 500,
    step: 50,
    slide: function( event, ui ) {
      $( "#amount-donate" ).val( "$" + ui.value );
    }
  });
  $( "#amount-donate" ).val( "$" + $( "#slider-donate" ).slider( "value" ) );
});