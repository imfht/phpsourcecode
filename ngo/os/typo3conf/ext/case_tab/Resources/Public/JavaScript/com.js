$(function(){
	
	$.extend($.validator.defaults,{ignore:""});
    	
	if ( $("#content_bodytext").length > 0 ) {
		UE.getEditor("content_bodytext");
	}
	
	if(!isMobile()){
		$(".select2").select2({
			placeholder: "   --选择或输入标签--",
            tags: true,//允许手动输入，生成标签
			tokenSeparators: [',', ';', '，', '；', ' '],
			width: "100%",
			//maximumSelectionSize: 5,
			language: { noResults: function (params) { return "没有查询到结果"; } },
			createTag: function(params) {//解决部分浏览器开启 tags: true 后无法输入中文的BUG 
				if (/[,;，； ]/.test(params.term)) {//支持【逗号】【分号】【空格】结尾生成tags
					var str = params.term.trim().replace(/[,;，；]*$/, '');
					return { id: str, text: str }
				} else {
					return null;
				}
			}
		});
		$(".selectpro").select2({
			placeholder: "   --选择产品分类--",
            tags: false,//不允许手动输入，生成标签
			tokenSeparators: [',', ';', '，', '；', ' '],
			width: "100%",
			//maximumSelectionSize: 5,
			language: { noResults: function (params) { return "没有查询到结果"; } },
		});
	}
	
	$('#audaCaseInfo').click(function(){
		var bRet = UE.getEditor("content_bodytext").hasContents();
		if(bRet){
			$("#ckRichText").val("ok");
		}else{
			$("#ckRichText").val("");
		}
		return true;  
	});
	
	$("#NewEditCasetypeValid").validate({
		errorElement: "span", 
		rules: {
			'tx_casetab_case[casetype][name]':{
				required: true,
			},
			'tx_casetab_case[casetype][description]':{
				required: true,
			},
			'tx_casetab_case[casetype][sort]':{
				required: true,
				digits:true
			},
		},
		messages: {
			'tx_casetab_case[casetype][name]':{
				required: "请输入类型名称！"
			},
			'tx_casetab_case[casetype][description]':{
				required: "请输入类型描述！"
			},
			'tx_casetab_case[casetype][sort]':{
				required: "请输入排序序号！",
				digits:"请输入正确的数字！"
			},
		}   
    });
	
	//新增案例内容
	$("#NewEditCasetabValid").validate({
		errorElement: "span", 
		rules: {
			'tx_casetab_casetab[casetab][title]':{
				required: true
			},
			'tx_casetab_casetab[casetab][industry]':{
				required: true,
			},
			'tx_casetab_casetab[product][]':{
				required: true,
			},
			'tx_casetab_casetab[labels][]':{
				required: true,
			},
			'tx_casetab_casetab[casetab][datetime]':{
				required: true,
			},
			'tx_casetab_casetab[imgpath][]':{
				imageCheck: true,
			},
			'tx_casetab_casetab[casetab][spare3]':{
				required: true,
			},
			'tx_casetab_casetab[casetab][spare4]':{
				required: true,
			},
			'tx_casetab_casetab[casetab][spare5]':{
				required: true,
			},
			'tx_casetab_casetab[casetab][spare6]':{
				required: true,
			},
			'tx_casetab_casetab[ckRichText]': {
				required: true
			},
		},
		messages: {
			'tx_casetab_casetab[casetab][title]':{
				required:  "请输入标题！"
			},
			'tx_casetab_casetab[casetab][industry]':{
				required: "请选择行业分类！",
			},
			'tx_casetab_casetab[product][]':{
				required: "请选择产品分类！",
			},
			'tx_casetab_casetab[labels][]':{
				required: "请选择项目标签！",
			},
			'tx_casetab_casetab[casetab][datetime]':{
				required: "请输入选择时间！",
			},
			'tx_casetab_casetab[imgpath][]':{
				imageCheck: "请选择图片文件！"
			},
			'tx_casetab_casetab[casetab][spare3]':{
				required: "请输入项目背景！",
			},
			'tx_casetab_casetab[casetab][spare4]':{
				required: "请输入应用场景！",
			},
			'tx_casetab_casetab[casetab][spare5]':{
				required: "请输入应用效果！",
			},
			'tx_casetab_casetab[casetab][spare6]':{
				required: "请输入技术要点！",
			},
			'tx_casetab_casetab[ckRichText]': {
				required: "请输入详细内容!"
			},
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

// 删除图片
function deleteimg(i){
	var url = $("#ajaxurls").val();
	var uid = $('#uid').val();
	var imgname = $(i).attr('data-img');
	var a = confirm('你确定要删除吗？');
	if(a == true){
		$.post(url, 'act=delimage&uid='+uid+'&imgname='+imgname, function(data,status) {
			if(data.stat==1){
				alert('删除成功');
				window.location.reload();
			}else{
				alert(data.msg);
			}
		},"json");
	}
}

function toggle(i){
	var hasClass = $(i).hasClass('glyphicon-chevron-down');
	if(hasClass == true){
		 $(i).addClass('glyphicon glyphicon-chevron-up').removeClass('glyphicon-chevron-down');
		 $(i).parent().next('ul').show('slow');
	}else{
		$(i).addClass('glyphicon glyphicon-chevron-down').removeClass('glyphicon-chevron-up');
		 $(i).parent().next('ul').hide('slow');
	}
}

//获得上传图片URL地址
function getObjectURL(file) {
    var url = null ;
    if (window.createObjectURL!=undefined) { // basic
        url = window.createObjectURL(file) ;
    } else if (window.URL!=undefined) { // mozilla(firefox)
        url = window.URL.createObjectURL(file) ;
    } else if (window.webkitURL!=undefined) { // webkit or chrome
        url = window.webkitURL.createObjectURL(file) ;
    }
    return url ;
}

function isMobile() {
    return /(iPhone|iPad|iPod|iOS|android|MicroMessenger)/i.test(navigator.userAgent);
}

//解决输入中文后无法回车结束的问题。
$(document).on('keyup', '.select2-search__field', function(event){
	if(event.keyCode == 13){
		var $this = $(this);
		var optionText = $this.val();
		//如果没有就添加
		if(optionText != "" && $this.find("option[value='" + optionText + "']").length === 0){
			//我还不知道怎么优雅的定位到input对应的select
			var $select = $this.parents('.select2-container').prev("select");
			var newOption = new Option(optionText, optionText, true, true);
			$select.append(newOption).trigger('change');
			$this.val('');
		}
	}
});

