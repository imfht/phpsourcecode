/**
 * @author ZhangHuihua@msn.com
 */
(function($){
	// jQuery validate
	$.extend($.validator.messages, {
		required: "Required",
		remote: "Already exist, please fix this field",
		email: "Please enter a properly formatted email",
		url: "Please enter a valid URL",
		date: "Please enter a valid date",
		dateISO: "Please enter a valid date (ISO).",
		number: "Please enter a valid number",
		digits: "Enter only integer",
		creditcard: "Please enter a valid credit card number",
		equalTo: "Please re-enter the same value",
		accept: "Please enter the string has a legitimate extension",
		maxlength: $.validator.format("Length at most {0}"),
		minlength: $.validator.format("Minimum length is {0}"),
		rangelength: $.validator.format("Length between {0} and {1}"),
		range: $.validator.format("Please enter a value between {0} and {1}"),
		max: $.validator.format("Please enter a maximum of {0}"),
		min: $.validator.format("Please enter a minimum of {0}"),
		
		alphanumeric: "Letters, numbers, underscores",
		lettersonly: "Must be a letter",
		phone: "Numbers, spaces, parentheses"
	});
	
	// DWZ regional
	$.setRegional("datepicker", {
		dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
		monthNames: ['Jan', 'February', 'March', 'April', 'May', 'June', 'No', 'August', 'September', 'October', 'November', 'Dec']
	});
	$.setRegional("alertMsg", {
		title:{error:"Error", info:"Prompt", warn:"Warning", correct:"Success", confirm:"Confirmation"},
		butMsg:{ok:"Determine", yes:"Yes", no:"No", cancel:"Cancel"}
	});
})(jQuery);