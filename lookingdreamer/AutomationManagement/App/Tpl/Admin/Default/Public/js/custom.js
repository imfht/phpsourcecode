$(document).ready(function() {

	//===== File uploader =====//
	
	$("#uploader").pluploadQueue({
		runtimes : 'html5,html4',
		url : 'php/upload.php',
		max_file_size : '1kb',
		unique_names : true,
		filters : [
			{title : "Image files", extensions : "jpg,gif,png"}
		]
	});

	//===== Fancybox =====//
	
	$(".lightbox").fancybox({
		'padding': 2
	});



	//===== Wizards =====//
	
	$("#wizard1").formwizard({
		formPluginEnabled: true, 
		validationEnabled: false,
		focusFirstInput : false,
		disableUIStyles : true,
	
		formOptions :{
			success: function(data){$("#status1").fadeTo(500,1,function(){ $(this).html("<span>Form was submitted!</span>").fadeTo(5000, 0); })},
			beforeSubmit: function(data){$("#w1").html("<span>Form was submitted with ajax. Data sent to the server: " + $.param(data) + "</span>");},
			resetForm: true
		}
	});
	
	$("#wizard2").formwizard({ 
		formPluginEnabled: true,
		validationEnabled: true,
		focusFirstInput : false,
		disableUIStyles : true,
	
		formOptions :{
			success: function(data){$("#status2").fadeTo(500,1,function(){ $(this).html("<span>Form was submitted!</span>").fadeTo(5000, 0); })},
			beforeSubmit: function(data){$("#w2").html("<span>Form was submitted with ajax. Data sent to the server: " + $.param(data) + "</span>");},
			dataType: 'json',
			resetForm: true
		},
		validationOptions : {
			rules: {
				bazinga: "required",
				email: { required: true, email: true }
			},
			messages: {
				bazinga: "Bazinga. This note is editable",
				email: { required: "Please specify your email", email: "Correct format is name@domain.com" }
			},
		    highlight: function(label) {
		    	$(label).closest('.control-group').addClass('error');
		    },
		    success: function(label) {
		    	label
		    		.text('Success!').addClass('valid')
		    		.closest('.control-group').addClass('success');
		    }
		}
	});
	
	$("#wizard3").formwizard({
		formPluginEnabled: false, 
		validationEnabled: false,
		focusFirstInput : false,
		disableUIStyles : true
	});



	//===== File manager =====//	
	
	var elf = $('#file-manager').elfinder({
		url : 'php/connector.php',  // connector URL (REQUIRED)
		uiOptions : {
			// toolbar configuration
			toolbar : [
				['back', 'forward'],
				['info'],
				['quicklook'],
				['search']
			]
		},
		contextmenu : {
		  // Commands that can be executed for current directory
		  cwd : ['reload', 'delim', 'info'], 
		  // Commands for only one selected file
		  files : ['select', 'open']
		}
	}).elfinder('instance');	
	

		
	//===== Calendar =====//
	
	var date = new Date();
	var d = date.getDate();
	var m = date.getMonth();
	var y = date.getFullYear();
	
	$('#calendar').fullCalendar({
		header: {
			left: 'prev,next',
			center: 'title',
			right: 'month,agendaWeek,agendaDay'
		},
		editable: true,
		events: [
			{
				title: 'All Day Event',
				start: new Date(y, m, 1)
			},
			{
				title: 'Long Event',
				start: new Date(y, m, d-5),
				end: new Date(y, m, d-2)
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d-3, 16, 0),
				allDay: false
			},
			{
				id: 999,
				title: 'Repeating Event',
				start: new Date(y, m, d+4, 16, 0),
				allDay: false
			},
			{
				title: 'Meeting',
				start: new Date(y, m, d, 10, 30),
				allDay: false
			},
			{
				title: 'Lunch',
				start: new Date(y, m, d, 12, 0),
				end: new Date(y, m, d, 14, 0),
				allDay: false
			},
			{
				title: 'Birthday Party',
				start: new Date(y, m, d+1, 19, 0),
				end: new Date(y, m, d+1, 22, 30),
				allDay: false
			},
			{
				title: 'Click for Google',
				start: new Date(y, m, 28),
				end: new Date(y, m, 29),
				url: 'http://google.com/'
			}
		]
	});


	
	//===== WYSIWYG editor =====//
	
	$("#editor").cleditor({
		width:"100%", 
		height:"250px",
		bodyStyle: "margin: 10px; font: 12px Arial,Verdana; cursor:text",
		docType: '<!DOCTYPE html>',
		useCSS:true
	});
	


	 //===== Tabbed page layout tabs =====//

	$('.page-tabs a').click(function (e) {
	  e.preventDefault();
	  $(this).tab('show');
	  $('#editor').cleditor()[0].refresh(); // Refreshing Cleditor 
	})
	

	
	//===== Make code pretty =====//

    window.prettyPrint && prettyPrint();



    //===== Table checkboxes =====//

    $('.table-checks tbody tr td:first-child input[type=checkbox]').change(function() {
        $(this).closest('tr').toggleClass("row-checked", this.checked);
	});

	
	
	//===== Pie charts =====//

	$('.piechart > li > div').easyPieChart({
       animate: 2000,
	   trackColor:	"#ddd",
	   scaleColor:	"#ddd",
	   lineWidth: 3,
	   barColor: "#d07571",
	   size: 94
    });
	

	
	//===== Datatable =====//

	oTable = $('#data-table').dataTable({
		"bJQueryUI": false,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"datatable-header"fl>t<"datatable-footer"ip>',
		"oLanguage": {
			"sLengthMenu": "<span>Show entries:</span> _MENU_"
		}
    });
	


	//===== Autocomplete =====//
	
	var availableTags = [ "ActionScript", "AppleScript", "Asp", "BASIC", "C", "C++", "Clojure", "COBOL", "ColdFusion", "Erlang", "Fortran", "Groovy", "Haskell", "Java", "JavaScript", "Lisp", "Perl", "PHP", "Python", "Ruby", "Scala", "Scheme" ];
		$( ".jquery-autocomplete" ).autocomplete({
		source: availableTags
	});	

	

	//===== Date pickers =====//

	$( ".datepicker" ).datepicker({
				defaultDate: +7,
		showOtherMonths:true,
		autoSize: true,
		appendText: '(dd-mm-yyyy)',
		dateFormat: 'dd-mm-yy'
		});
		
	$('.inlinepicker').datepicker({
        inline: true,
		showOtherMonths:true
    });

	var dates = $( "#fromDate, #toDate" ).datepicker({
		defaultDate: "+1w",
		changeMonth: false,
		showOtherMonths:true,
		numberOfMonths: 3,
		onSelect: function( selectedDate ) {
			var option = this.id == "fromDate" ? "minDate" : "maxDate",
				instance = $( this ).data( "datepicker" ),
				date = $.datepicker.parseDate(
					instance.settings.dateFormat ||
					$.datepicker._defaults.dateFormat,
					selectedDate, instance.settings );
			dates.not( this ).datepicker( "option", option, date );
		}
	});
	
	$( "#datepicker-icon" ).datepicker({
		showOn: "button",
		buttonImage: "images/icons/calendar.png",
		buttonImageOnly: true
	});

	

	//===== jQuery UI sliders =====//

	$( "#slider" ).slider();
	$( "#slider-range" ).slider({
            range: true,
            min: 0,
            max: 500,
            values: [ 75, 300 ],
            slide: function( event, ui ) {
                $( "#amount" ).val( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
            }
        });
        $( "#amount" ).val( "$" + $( "#slider-range" ).slider( "values", 0 ) +
            " - $" + $( "#slider-range" ).slider( "values", 1 ) );
			
	$( "#slider-range-min" ).slider({
            range: "min",
            value: 37,
            min: 1,
            max: 700,
            slide: function( event, ui ) {
                $( "#amount2" ).val( "$" + ui.value );
            }
        });
        $( "#amount2" ).val( "$" + $( "#slider-range-min" ).slider( "value" ) );
	
	$( "#slider-range-max" ).slider({
            range: "max",
            min: 1,
            max: 10,
            value: 2,
            slide: function( event, ui ) {
                $( "#amount3" ).val( ui.value );
            }
        });
        $( "#amount3" ).val( $( "#slider-range-max" ).slider( "value" ) );
		
	$( "#eq > span" ).each(function() {
		var value = parseInt( $( this ).text(), 10 );
		$( this ).empty().slider({
			value: value,
			range: "min",
			animate: true,
			orientation: "vertical"
		});
	});
		
		
		
	//===== Modals and dialogs =====//

	$("a.bs-alert").click(function(e) {
		e.preventDefault();
		bootbox.alert("Hello world!", function() {
			console.log("Alert Callback");
		});
	});
	
	$("a.confirm").click(function(e) {
		e.preventDefault();
		bootbox.confirm("Are you sure?", function(confirmed) {
			console.log("Confirmed: "+confirmed);
		});
	});
	
	$("a.bs-prompt").click(function(e) {
		e.preventDefault();
		bootbox.prompt("What is your name?", function(result) {
			console.log("Result: "+result);
		});
	});
	
	$("a.dialog").click(function(e) {
		e.preventDefault();
		bootbox.dialog("I am a custom dialog", [{
			"label" : "Success!",
			"class" : "btn-success",
			"callback": function() {
				console.log("great success");
			}
		}, {
			"label" : "Danger!",
			"class" : "btn-danger",
			"callback": function() {
				console.log("uh oh, look out!");
			}
		}, {
			"label" : "Click ME!",
			"class" : "btn-primary",
			"callback": function() {
				console.log("Primary button");
			}
		}, {
			"label" : "Just a button..."
		}, {
			"Condensed format": function() {
				console.log("condensed");
			}
		}]);
	});
	
	$("a.multiple-dialogs").click(function(e) {
		e.preventDefault();

		bootbox.alert("Prepare for multiboxes...", "Argh!");

		setTimeout(function() {
			bootbox.confirm("Are you having fun?", "No :(", "Yeah!", function(result) {
				if (result) {
					bootbox.alert("Glad to hear it!");
				} else {
					bootbox.alert("Aww boo. Click the button below to get rid of all these popups", function() {
						bootbox.hideAll();
					});
				}
			});
		}, 1000);
	});
	
	$("a.dialog-close").click(function(e) {
		e.preventDefault();
		var box = bootbox.alert("This dialog will close in two seconds");
		setTimeout(function() {
			box.modal('hide');
		}, 2000);
	});
	
	$("a.generic-modal").click(function(e) {
		e.preventDefault();
		bootbox.modal('<img src="http://dummyimage.com/600x400/000/fff" alt=""/>', 'Modal popup!');
	});
	
	$("a.dynamic").click(function(e) {
		e.preventDefault();
		var str = $("<p>This content is actually a jQuery object, which will change in 3 seconds...</p>");
		bootbox.alert(str);
		setTimeout(function() {
			str.html("See?");
		}, 3000);
	});
	
	$("a.prompt-default").click(function(e) {
		e.preventDefault();
		bootbox.prompt("What is your favourite JS library?", "Cancel", "OK", function(result) {
			console.log("Result: "+result);
		}, "Bootbox.js");
	});
	
	$("a.onescape").click(function(e) {
		e.preventDefault();
		bootbox.dialog("Dismiss this dialog with the escape key...", {
			"label" : "Press Escape!",
			"class" : "btn-danger",
			"callback": function() {
				console.log("Oi! Press escape!");
			}
		}, {
			"onEscape": function() {
				bootbox.alert("This alert was triggered by the onEscape callback of the previous dialog", "Dismiss");
			}
		});
	});

	$("a.nofade").click(function(e) {
		e.preventDefault();
		bootbox.dialog("This dialog does not fade in or out, and thus does not depend on <strong>bootstrap-transitions.js</strong>.",
		{
			"OK": function() {}
		}, {
			"animate": false
		});
	});

	$("a.nobackdrop").click(function(e) {
		e.preventDefault();
		bootbox.dialog("This dialog does not have a backdrop element",
		{
			"OK": function() {}
		}, {
			"backdrop": false
		});
	});

	$("a.icons-explicit").click(function(e) {
		e.preventDefault();
		bootbox.dialog("Custom dialog with icons being passed explicitly into <b>bootbox.dialog</b>.", [{
			"label" : "Success!",
			"class" : "btn-success",
			"icon"  : "icon-ok-sign icon-white"
		}, {
			"label" : "Danger!",
			"class" : "btn-danger",
			"icon"  : "icon-warning-sign icon-white"
		}, {
			"label" : "Click ME!",
			"class" : "btn-primary",
			"icon"  : "icon-ok icon-white"
		}, {
			"label" : "Just a button...",
			"icon"  : "icon-picture"
		}]);
	});

	$("a.icons-override").click(function(e) {
		e.preventDefault();
		bootbox.setIcons({
			"OK"      : "icon-ok icon-white",
			"CANCEL"  : "icon-ban-circle",
			"CONFIRM" : "icon-ok-sign icon-white"
		});

		bootbox.confirm("This dialog invokes <b>bootbox.setIcons()</b> to set icons for the standard three labels of OK, CANCEL and CONFIRM, before calling a normal <b>bootbox.confirm</b>", function(result) {
			bootbox.alert("This dialog is just a standard <b>bootbox.alert()</b>. <b>bootbox.setIcons()</b> only needs to be set once to affect all subsequent calls", function() {
				bootbox.setIcons(null);
			});
		});
	});

	$("a.no-close-button").click(function(e) {
		e.preventDefault();
		bootbox.dialog("If a button's handler now explicitly returns <b>false</b>, the dialog will not be closed. Note that if anything <b>!== false</b> - e.g. nothing, true, null etc - is returned, the dialog will close.", [{
			"I'll close on click": function() {
				console.log("close on click");
				return true;
			},
		}, {
			"I won't!": function() {
				console.log("returning false...");
				return false;
			}
		}]);
	});
	
	
	
	//===== Time pickers =====//

	$('#defaultValueExample, #time').timepicker({ 'scrollDefaultNow': true });
	
	$('#durationExample').timepicker({
		'minTime': '2:00pm',
		'maxTime': '11:30pm',
		'showDuration': true
	});
	
	$('#onselectExample').timepicker();
	$('#onselectExample').on('changeTime', function() {
		$('#onselectTarget').text($(this).val());
	});
	
	$('#timeformatExample1').timepicker({ 'timeFormat': 'H:i:s' });
	$('#timeformatExample2').timepicker({ 'timeFormat': 'h:i A' });

	
	
	//===== Autogrowing textarea =====//
	
	$('.auto').autosize();



	//===== Bootstrap functions =====//

	// Loading button
    $('#loading').click(function () {
        var btn = $(this)
        btn.button('loading')
        setTimeout(function () {
          btn.button('reset')
        }, 3000);
	});


    // Typeahead
	$('.typeahead').typeahead();


	// Popover
	$('.popover-test').popover({
		placement: 'left'
	})
	.click(function(e) {
		e.preventDefault()
	});

	$("a[rel=popover]")
		.popover()
	.click(function(e) {
		e.preventDefault()
	})


	// Tooltips
	$('.focustip').tooltip({'trigger':'focus'});
	$('.hovertip').tooltip();
	$('.tooltips, .table, .icons').tooltip({
		selector: "a[rel=tooltip]"
	});


	// Progress bars
	$('.progress .bar.filled-text').progressbar({
        display_text: 1
    });
	
	$('.slim .bar').progressbar();
	
	$('.delay .bar').progressbar({
		display_text: 1,
        transition_delay: 2000
    });
	
	$('.value .bar').progressbar({
		display_text: 1,
        use_percentage: false
    });
	
	$('.progress .bar.centered-text').progressbar({
        display_text: 2
    });
	
	$('.progress .no-text').progressbar();




	//===== Validation =====//

	$("#usualValidate").validate({
		rules: {
			firstname: "required",
			minChars: {
				required: true,
				minlength: 3
			},
			maxChars: {
				required: true,
				maxlength: 6
			},
			mini: {
				required: true,
				min: 3
			},
			maxi: {
				required: true,
				max: 6
			},
			range: {
				required: true,
				range: [6, 16]
			},
			emailField: {
				required: true,
				email: true
			},
			urlField: {
				required: true,
				url: true
			},
			dateField: {
				required: true,
				date: true
			},
			digitsOnly: {
				required: true,
				digits: true
			},
			enterPass: {
				required: true,
				minlength: 5
			},
			repeatPass: {
				required: true,
				minlength: 5,
				equalTo: "#enterPass"
			},
			customMessage: "required",
			topic: {
				required: "#newsletter:checked",
				minlength: 2
			},
			agree: "required"
		},
		messages: {
			customMessage: {
				required: "Bazinga! This message is editable",
			},
			agree: "Please accept our policy"
		},
	    highlight: function(label) {
	    	$(label).closest('.control-group').addClass('error');
	    },
	    success: function(label) {
	    	label
	    		.text('Success!').addClass('valid')
	    		.closest('.control-group').addClass('success');
	    }
	});



	//===== Append right sidebar to the left =====//

	$(window).resize(function () {
	  var width = $(this).width();
		if (width < 1367) {
			$('.three-columns .appendable').appendTo('#left-sidebar');
			$('.three-columns .content').css('marginRight', '0');
			$.sparkline_display_visible();
		}
		else { 
			$('.three-columns .appendable').appendTo('#right-sidebar');
			$('.three-columns .content').css('marginRight', '240px')
			$.sparkline_display_visible();
		 }
	}).resize();



	//===== Top nav and responsive functions =====//

	$('.topnav > li.search > a').click(function () {
		$('.top-search').fadeToggle(50);
	});

	$('.sidebar-button > a').toggle(function () {
		$('.sidebar').addClass('show-sidebar').removeClass('hide-sidebar');
	},
	function () {
		$('.sidebar').removeClass('show-sidebar').addClass('hide-sidebar');
	}
	);



	//===== Sparklines =====//
	
	$('#balance').sparkline(
		'html', {type: 'bar', barColor: '#db6464', height: '35px', barWidth: "5px", barSpacing: "2px", zeroAxis: "false"}
	);
	$('#clicks').sparkline(
		'html', {type: 'bar', barColor: '#a6c659', height: '35px', barWidth: "5px", barSpacing: "2px", zeroAxis: "false"}
	);
	$('#support').sparkline(
		'html', {type: 'bar', barColor: '#4fb9f0', height: '35px', barWidth: "5px", barSpacing: "2px", zeroAxis: "false"}
	);	



	//===== Tags =====//	
		
	$('.tags').tagsInput({width:'100%'});



	//===== Dual select boxes =====//
	
	$.configureBoxes();



	//===== Collapsible plugin for main nav =====//
	
	$('.expand').collapsible({
		defaultOpen: 'current',
		cookieName: 'navAct',
		cssOpen: 'subOpened',
		cssClose: 'subClosed',
		speed: 200
	});



	//===== Input limiter =====//
	
	$('.lim').inputlimiter({
		limit: 100,
		boxId: 'limitingtext',
		boxAttach: false
	});
	
	

	//===== Masked input =====//
	
	$.mask.definitions['~'] = "[+-]";
	$(".maskDate").mask("99/99/9999",{completed:function(){alert("Callback when completed");}});
	$(".maskPhone").mask("(999) 999-9999");
	$(".maskPhoneExt").mask("(999) 999-9999? x99999");
	$(".maskIntPhone").mask("+33 999 999 999");
	$(".maskTin").mask("99-9999999");
	$(".maskSsn").mask("999-99-9999");
	$(".maskProd").mask("a*-999-a999", { placeholder: " " });
	$(".maskEye").mask("~9.99 ~9.99 999");
	$(".maskPo").mask("PO: aaa-999-***");
	$(".maskPct").mask("99%");



	//===== Select2 dropdowns =====//

	$(".select").select2();
		
	$(".selectMultiple").select2();
		
	$("#loadingdata").select2({
		placeholder: "Enter at least 1 character",
        allowClear: true,
        minimumInputLength: 1,
        query: function (query) {
            var data = {results: []}, i, j, s;
            for (i = 1; i < 5; i++) {
                s = "";
                for (j = 0; j < i; j++) {s = s + query.term;}
                data.results.push({id: query.term + i, text: s});
            }
            query.callback(data);
        }
    });		
		
	$("#maxselect").select2({ maximumSelectionSize: 3 });
		
	$("#minselect").select2({
        minimumInputLength: 2,
		width: 'element'
    });
	
	$("#minselect2").select2({
        minimumInputLength: 2
    });
	
	$("#disableselect, #disableselect2").select2(
        "disable"
    );
		
		

	//===== iButtons =====//
	
	$('.on_off :checkbox, .on_off :radio').iButton({
		labelOn: "",
		labelOff: "",
		enableDrag: false 
	});
	
	$('.on_off2 :checkbox, .on_off2 :radio').iButton({
		labelOn: "Yeah",
		labelOff: "Nope",
		enableDrag: false 
	});
	
	$('.yes_no :checkbox, .yes_no :radio').iButton({
		labelOn: "On",
		labelOff: "Off",
		enableDrag: false
	});
	
	$('.enabled_disabled :checkbox, .enabled_disabled :radio').iButton({
		labelOn: "Enabled",
		labelOff: "Disabled",
		enableDrag: false
	});



	//===== Top notification bars =====//
	
	$(".notice .close").click(function() {
		$(this).parent().parent('.notice').fadeTo(200, 0.00, function(){ //fade
			$(this).slideUp(200, function() { //slide up
				$(this).remove(); //then remove from the DOM
			});
		});
	});	
	
	window.setTimeout(function() {
	    $(".closing").fadeTo(200, 0).slideUp(200, function(){
	        $(this).remove(); 
	    });
	}, 5000);

	
	
	//===== Color picker =====//

	$('#cp1').colorpicker({
		format: 'hex'
	});
	$('#cp2').colorpicker();
	$('#cp3').colorpicker();
		var bodyStyle = $('body')[0].style;
	$('#cp4').colorpicker().on('changeColor', function(ev){
		bodyStyle.background = ev.color.toHex();
	});




	//===== Spinner options =====//
	
	$( "#spinner-default" ).spinner();
	
	$( "#spinner-decimal" ).spinner({
		step: 0.01,
		numberFormat: "n"
	});
	
	$( "#culture" ).change(function() {
		var current = $( "#spinner-decimal" ).spinner( "value" );
		Globalize.culture( $(this).val() );
		$( "#spinner-decimal" ).spinner( "value", current );
	});
	
	$( "#currency" ).change(function() {
		$( "#spinner-currency" ).spinner( "option", "culture", $( this ).val() );
	});
	
	$( "#spinner-currency" ).spinner({
		min: 5,
		max: 2500,
		step: 25,
		start: 1000,
		numberFormat: "C"
	});
		
	$( "#spinner-overflow" ).spinner({
		spin: function( event, ui ) {
			if ( ui.value > 10 ) {
				$( this ).spinner( "value", -10 );
				return false;
			} else if ( ui.value < -10 ) {
				$( this ).spinner( "value", 10 );
				return false;
			}
		}
	});
	
	$.widget( "ui.timespinner", $.ui.spinner, {
		options: {
			// seconds
			step: 60 * 1000,
			// hours
			page: 60
		},

		_parse: function( value ) {
			if ( typeof value === "string" ) {
				// already a timestamp
				if ( Number( value ) == value ) {
					return Number( value );
				}
				return +Globalize.parseDate( value );
			}
			return value;
		},

		_format: function( value ) {
			return Globalize.format( new Date(value), "t" );
		}
	});

	$( "#spinner-time" ).timespinner();
	$( "#culture-time" ).change(function() {
		var current = $( "#spinner-time" ).timespinner( "value" );
		Globalize.culture( $(this).val() );
		$( "#spinner-time" ).timespinner( "value", current );
	});



	//===== Form elements styling =====//
	
	$(".ui-datepicker-month, .style, .dataTables_length select").uniform({ radioClass: 'choice' });
		
});