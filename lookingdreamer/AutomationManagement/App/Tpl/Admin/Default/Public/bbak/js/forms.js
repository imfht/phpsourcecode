$(document).ready(function(){

/* Auto size for text area */
$('textarea').autosize();

  // jQuery UI Datepicker
  var datepickerSelector = '#datepicker';
  $(datepickerSelector).datepicker({
    showOtherMonths: true,
    selectOtherMonths: true,
    dateFormat: "d MM, yy",
    yearRange: '-1:+1'
  }).prev('.btn').on('click', function (e) {
    e && e.preventDefault();
    $(datepickerSelector).focus();
  });

	
	/* ---------- Bootstrap Wysiwig ---------- */
	$('#editor').wysiwyg();

	/* ---------- Bootstrap Colorpicker ---------- */
	$('#colorpicker').colorpicker();
	
});

/* Input tags */

$(function() {

	$('#tags_1').tagsInput({width:'auto'});
	$('#tags_2').tagsInput({
		width: 'auto',
		onChange: function(elem, elem_tags)
		{
			var languages = ['php','ruby','javascript'];
			$('.tag', elem_tags).each(function()
			{
				if($(this).text().search(new RegExp('\\b(' + languages.join('|') + ')\\b')) >= 0)
					$(this).css('background-color', '#5BC0DE');
			});
		}
	});
	
});
