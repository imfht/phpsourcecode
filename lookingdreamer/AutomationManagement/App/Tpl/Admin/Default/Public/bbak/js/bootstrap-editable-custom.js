$(document).ready(function() {
	//toggle `popup` / `inline` mode
	$.fn.editable.defaults.mode = 'popup';     

	//make username editable
	$('#username').editable();

	//make status editable
	$('#status').editable({
		type: 'select',
		title: 'Select status',
		placement: 'right',
		value: 2,
		source: [
		{value: 1, text: 'status 1'},
		{value: 2, text: 'status 2'},
		{value: 3, text: 'status 3'}
		]
		/*
		//uncomment these lines to send data on server
		,pk: 1
		,url: '/post'
		*/
	});

	//make textarea editable
	$('#comments').editable({
		title: 'Enter comments',
		rows: 10
	});

	//make input date
	$('#dob').editable({
		format: 'yyyy-mm-dd',    
		viewformat: 'dd/mm/yyyy',    
		datepicker: {
			weekStart: 1
		}
	});

	//make datetime picker
	$('#last_seen').editable({
		format: 'yyyy-mm-dd hh:ii',    
		placement: 'bottom',
		viewformat: 'dd/mm/yyyy hh:ii',    
		datetimepicker: {
			weekStart: 1
		}
	});
	
	//make combodate
	$('#dob').editable({
        format: 'YYYY-MM-DD',    
        viewformat: 'DD.MM.YYYY',    
        template: 'D / MMMM / YYYY',    
        combodate: {
                minYear: 2000,
                maxYear: 2015,
                minuteStep: 1
           }
    		});

});