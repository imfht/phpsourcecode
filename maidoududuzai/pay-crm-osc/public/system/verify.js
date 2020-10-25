/* verify */

function get_rates(trade_rates) {
	if(0 == parseFloat(trade_rates)) {
		return "0.00";
	}
	var s = trade_rates.toString().split(".");
	if(s.length == 1) {
		trade_rates = trade_rates.toString() + ".00";
	} else {
		if(s[1].length < 2) {
			trade_rates = trade_rates.toString() + "0";
		}
	}
	return trade_rates;
}

function checkAdmin(the_form) {
	if(the_form) {
		obj = $(the_form);
	} else {
		obj = $(document);
	}
	if(obj.find("#username").val() == "") {
		showTips("#username", $("#username").attr("placeholder"));
		return false;
	}
	if(obj.find("#pname").val() == "") {
		showTips("#pname", $("#pname").attr("placeholder"));
		return false;
	}
	if(obj.find("#phone").val() == "") {
		showTips("#phone", $("#phone").attr("placeholder"));
		return false;
	}
	if(obj.find("#email").val() == "") {
		showTips("#email", $("#email").attr("placeholder"));
		return false;
	}
}

function checkAgent(the_form) {
	if(the_form) {
		obj = $(the_form);
	} else {
		obj = $(document);
	}
	if(obj.find("#agent_name").val() == "") {
		showTips("#agent_name", $("#agent_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_name").val() == "") {
		showTips("#per_name", $("#per_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_phone").val() == "") {
		showTips("#per_phone", $("#per_phone").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_email").val() == "") {
		showTips("#per_email", $("#per_email").attr("placeholder"));
		return false;
	}
	if(obj.find("#address").val() == "") {
		showTips("#address", $("#address").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_bank").val() == "") {
		showTips("#account_bank", $("#account_bank").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_name").val() == "") {
		showTips("#account_name", $("#account_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_cardno").val() == "") {
		showTips("#account_cardno", $("#account_cardno").attr("placeholder"));
		return false;
	}
}

function checkMerchant(the_form) {
	if(the_form) {
		obj = $(the_form);
	} else {
		obj = $(document);
	}
	if(obj.find("#merchant_name").val() == "") {
		showTips("#merchant_name", $("#merchant_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#merchant_type").val() == "") {
		showTips("#merchant_type", $("#merchant_type").attr("placeholder"));
		return false;
	}
	if(obj.find("#merchant_industry").val() == "") {
		showTips("#merchant_industry", $("#merchant_industry").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_name").val() == "") {
		showTips("#per_name", $("#per_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_phone").val() == "") {
		showTips("#per_phone", $("#per_phone").attr("placeholder"));
		return false;
	}
	if(obj.find("#per_email").val() == "") {
		showTips("#per_email", $("#per_email").attr("placeholder"));
		return false;
	}
	if(obj.find("#address").val() == "") {
		showTips("#address", $("#address").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_bank").val() == "") {
		showTips("#account_bank", $("#account_bank").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_name").val() == "") {
		showTips("#account_name", $("#account_name").attr("placeholder"));
		return false;
	}
	if(obj.find("#account_cardno").val() == "") {
		showTips("#account_cardno", $("#account_cardno").attr("placeholder"));
		return false;
	}
}

