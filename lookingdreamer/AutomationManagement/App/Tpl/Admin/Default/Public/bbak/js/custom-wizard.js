$(document).ready(function() {
  	var $validator = $("#commentForm").validate({
		  rules: {
		    email: {
		      required: true,
		      email: true,
		      minlength: 3
		    },
		    username: {
		      required: true,
		      minlength: 3
		    },
		    urlfield: {
		      required: true,
		      minlength: 3,
		      url: true
		    }
		  },
		errorClass: "text-danger",
		});
 
	  	$('#rootwizard').bootstrapWizard({
	  		'tabClass': 'nav nav-pills',
	  		'onNext': function(tab, navigation, index) {
	  			var $valid = $("#commentForm").valid();
	  			if(!$valid) {
	  				$validator.focusInvalid();
	  				return false;
	  			}
	  		}
	  	});
	
	

});