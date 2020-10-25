$(function(){
	//大类校验
	$("#NewEditDicttypeValid").validate({
		errorElement: "span", 
		rules: {
			'tx_dicts_dicttype[dicttype][name]':{
				required: true,
			},
			'tx_dicts_dicttype[dicttype][remarks]':{
				required: true,
			},
			'tx_dicts_dicttype[dicttype][sort]':{
				required: true,
				digits:true
			}
		},
		messages: {
			'tx_dicts_dicttype[dicttype][name]':{
				required: "请输入类别名称！"
			},
			'tx_dicts_dicttype[dicttype][remarks]':{
				required: "请输入类别描述！"
			},
			'tx_dicts_dicttype[dicttype][sort]':{
				required: "请输入排序！",
				digits:"请输入正确的数字！"
			}
		}   
    });
	$("#NewEditDictitemValid").validate({
		errorElement: "span", 
		rules: {
			'tx_dicts_dictitem[dictitem][name]':{
				required: true,
			},
			'tx_dicts_dictitem[dictitem][sort]':{
				required: true,
				digits:true
			}
		},
		messages: {
			'tx_dicts_dictitem[dictitem][name]':{
				required: "请输入小类名称！"
			},
			'tx_dicts_dictitem[dictitem][sort]':{
				required: "请输入排序序号！",
				digits:"请输入正确的数字！"
			}
		}   
    });
		
	jQuery.validator.addMethod("isTelphone", function(value, element) { 
	    var tel = /^1[3|4|5|7|8][0-9]\d{4,8}$/;
	    return this.optional(element) || (tel.test(value));
	}, "手机号输入错误");
	
	jQuery.validator.addMethod("isPostcode", function(value, element) { 
	    var tel = /^[1-9]\d{5}(?!\d)$/;
	    return this.optional(element) || (tel.test(value));
	}, "邮编错误");
	
	jQuery.validator.addMethod("isFloat", function(value, element) { 
	    var tel = /^(([0-9]+\.[0-9]*[1-9][0-9]*)|([0-9]*[1-9][0-9]*\.[0-9]+)|([0-9]*[1-9][0-9]*))$/;
	    return this.optional(element) || (tel.test(value));
	}, "浮点数据输入错误");
});

