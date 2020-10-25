$(function(){
		$("#add_new_invoice").validate({
			errorElement: "span", 
			rules: {
				'tx_invoice_fpgl[invoice][donatetime]':{
					required: true,
				},
				'tx_invoice_fpgl[invoice][channelid]':{
					required: true,
				},
				'tx_invoice_fpgl[invoice][spare1]':{
					required: true
				},
				'tx_invoice_fpgl[invoice][money]':{
					required: true,
					isFloat:true
				},
				'tx_invoice_fpgl[invoice][header]': {
					required: true
			　　     },
			　　     'tx_invoice_fpgl[invoice][address]': {
			　　    	required: true
				},
				'tx_invoice_fpgl[invoice][postcode]': {
					required: true,
					isPostcode:true
			　　     },
				'tx_invoice_fpgl[invoice][people]': {
					required: true
				},
				'tx_invoice_fpgl[invoice][telphone]': {
					required: true,
					isTelphone:true
				},
				'tx_invoice_fpgl[invoice][mail]': {
					required: true,
					email:true
				}
			},
			messages: {
				'tx_invoice_fpgl[invoice][donatetime]':{
					required: "请选择捐赠时间！"
				},
				'tx_invoice_fpgl[invoice][channelid]':{
					required: "请选择捐赠渠道！"
				},
				'tx_invoice_fpgl[invoice][spare1]':{
					required: "请输入税号！"
				},
				'tx_invoice_fpgl[invoice][money]': {
					required: "请输入捐款金额！",
					isFloat:"金额输入错误！"
			　　     },
				'tx_invoice_fpgl[invoice][header]': {
					required: "请输入票据抬头!"
				},
				'tx_invoice_fpgl[invoice][address]': {
					required: "请输入邮寄地址!",
					isPostcode:"邮编错误！"
				},
				'tx_invoice_fpgl[invoice][postcode]': {
					required: "请输入邮编!"
				},
				'tx_invoice_fpgl[invoice][people]': {
					required: "请输入联系人!"
				},
				'tx_invoice_fpgl[invoice][telphone]': {
					required: "请输入联系电话!",
					isTelphone:"手机号码错误"
				},
				'tx_invoice_fpgl[invoice][mail]': {
					required: "请输入邮箱！",
					email:"邮箱格式错误！"
				}
			}   
	    });
		
		jQuery.validator.addMethod("isTelphone", function(value, element) { 
		    var tel = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
		    return this.optional(element) || (tel.test(value));
		}, "手机号输入错误");
		
		jQuery.validator.addMethod("isPostcode", function(value, element) { 
		    var tel = /^[0-9]\d{5}(?!\d)$/;
		    return this.optional(element) || (tel.test(value));
		}, "邮编错误");
		
		jQuery.validator.addMethod("isFloat", function(value, element) { 
		    var tel = /^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/;
		    return this.optional(element) || (tel.test(value));
		}, "浮点数据输入错误");
	});

