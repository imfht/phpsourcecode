$(function(){
	
	$.extend($.validator.defaults,{ignore:""});

	$('.btn-del').click(function(){
			if(confirm('您确认要删除该条记录吗？')) return true;
			return false;
		});
		
		//全选&取消全选
		$('input[type="checkbox"].selectall').bind('click', function() { 
			$(this).closest('table').find('.sel').prop("checked", this.checked);
		}); 
		
		//删除选中
		$('.btn-delete-all').bind('click',function(){
			var items='';
			$('input[type="checkbox"].sel').each(function(){
				if($(this).prop("checked")){
					items += $(this).val()+',';
				}
			});
			
			if(items == ''){
				alert('请您选择需要删除的记录！');
			}else{
				$('#multidelete-items').val(items);
				//alert($('#multidelete-items').val());
				return true;
			}
			return false;
		});
	
	if ( $("#activity_feront_edit_bodytext").length > 0 ) {
		//CKEDITOR.replace( 'news_feront_edit_bodytext');
		UE.getEditor("activity_feront_edit_bodytext");
	}
	
	$('#addActivity').click(function(){
		var bRet = UE.getEditor("activity_feront_edit_bodytext").hasContents();
		if(bRet){
			$("#ckRichText").val("ok");
		}else{
			$("#ckRichText").val("");
		}
		return true;  
	});
	
	//志愿者活动
	$("#NewEditActivityValid").validate({
		errorElement: "span", 
		rules: {
			'tx_activity_activity[activity][way]':{
				required: true,
			},
			'tx_activity_activity[activity][name]':{
				required: true,
			},
			'tx_activity_activity[activity][address]':{
				required: true,
			},
			'tx_activity_activity[activity][types]':{
				required: true,
			},
			'tx_activity_activity[activity][tag]':{
				required: true,
			},
			'tx_activity_activity[week]':{
				dateCheck: true,
			},
			'tx_activity_activity[hour]':{
				dateCheck: true,
			},
			'tx_activity_activity[sttime]':{
				dateCheck: true,
			},
			'tx_activity_activity[overtime]':{
				dateCheck: true,
			},
			/*'tx_activity_activity[pictures]': {
				imageCheck: true
			},*/
			'tx_activity_activity[activity][introduce]': {
				required: true
			},
			'tx_activity_activity[ckRichText]': {
				required: true
			},
		},
		messages: {
			'tx_activity_activity[activity][way]':{
				required: "请选择活动方式！"
			},
			'tx_activity_activity[activity][name]':{
				required: "请输入活动名称！"
			},
			'tx_activity_activity[activity][address]':{
				required: "请输入活动地址！",
			},
			'tx_activity_activity[activity][types]':{
				required: "请选择活动类别！"
			},
			'tx_activity_activity[activity][tag]':{
				required: "请选择活动标签！"
			},
			'tx_activity_activity[sttime]':{
				dateCheck: "请选择活动开始时间！"
			},
			'tx_activity_activity[overtime]':{
				dateCheck: "请选择活动结束时间！"
			},
			'tx_activity_activity[week]':{
				dateCheck: "请选择周期！"
			},
			'tx_activity_activity[hour]':{
				dateCheck: "请选择时间！"
			},
			/*'tx_activity_activity[pictures]': {
				imageCheck: "请上传封面图片!"
			},*/
			'tx_activity_activity[activity][introduce]': {
				required: "请输入活动简介！"
			},			
			'tx_activity_activity[ckRichText]': {
				required: "请输入活动详细内容!"
			},
		}   
    });

    //志愿者活动签到与报名
	$("#signValidate").validate({
		errorElement: "span", 
		rules: {
			'tx_activity_activity[name]':{
				required: true,
			},
			'tx_activity_activity[telephone]':{
				required: true,
			},
		},
		messages: {
			'tx_activity_activity[name]':{
				required: "请输入姓名！"
			},
			'tx_activity_activity[telephone]':{
				required: "请输入手机号！"
			},
		}   
    });

	//志愿者登记校验
    $("#bm_info").validate({
			errorElement: "span", 
			errorPlacement: function(error, element) {
				if (element.is(":checkbox")||element.is(":radio")){
					error.appendTo(element.parent());
				}else{
					error.insertAfter(element);
				}
			},
			rules: {
				'tx_activity_activity[volunteer][name]': {
					required: true
				},
				'tx_activity_activity[volunteer][sex]': {
					required: true
				},
				'tx_activity_activity[volunteer][birthday]': {
					required: true
				},
				/*'tx_activity_activity[volunteer][province]': {
					required: true
				},
				'tx_activity_activity[volunteer][community]': {
					required: true
				},*/
				'tx_activity_activity[volunteer][telephone]': {
					required: true,
					isTelphone:true,
					remote:{
						url: $("#urlbase").val(),
			            type: 'post',
				        data: {
				        	'act':function(){
				            	return $("#act").val();
				            },
				            'telephone': function(){
				            	return $("#stelephone").val();
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            }
			            },
			            /*dataFilter:function(data,status){
								console.log(data);//str
						},*/
				    } 
		　　     
				},
				'tx_activity_activity[volunteer][email]': {
					required: true,
					email:true,
					remote:{
						url: $("#urlbase").val(),
			            type: 'post',
				        data: {
				        	'act':function(){
				            	return $("#act").val();
				            },
				            'email': function(){
				            	return $("#semail").val();
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            }
			            },
			            /*dataFilter:function(data,status){
								console.log(data);//str
						},*/
				    } 
				},
				'tx_activity_activity[volunteer][identity]': {
					required: true
				}
			},
			messages: {
				'tx_activity_activity[volunteer][name]': {
					required: "请输入姓名!"
				},
				'tx_activity_activity[volunteer][sex]': {
					required: "请选择性别!"
				},
				'tx_activity_activity[volunteer][birthday]': {
					required: "请选择出生日期!"
				},
				/*'tx_activity_activity[volunteer][province]': {
					required: "请选择省份!"
				},
				'tx_activity_activity[volunteer][community]': {
					required: "请选择所在地!"
				},*/
				'tx_activity_activity[volunteer][telephone]': {
					required: "请输入联系电话!",
					isTelphone:'请输入正确的手机号！',
					remote:'手机号已存在！'
				},
				'tx_activity_activity[volunteer][email]': {
					required: "请输入邮箱!",
					email:'请输入正确的邮箱！',
					remote:'邮箱已存在！'
				},
				'tx_activity_activity[volunteer][identity]': {
					required: '请选择政治面貌！'
				},
			}   
        });
	
	$("#sttime").datetimepicker({
		format:'yyyy-mm-dd hh:ii',
		language:'zh-CN',
		autoclose:true
	});
	$("#overtime").datetimepicker({
		format: 'yyyy-mm-dd hh:ii',
		language:'zh-CN',
		autoclose:true
	});
	$('#hour').timepicker({
        showMeridian: false,
        maxHours:24,
        minuteStep:5,
    });
	//radio控制显示隐藏
	$("input[name='tx_activity_activity[activity][way]']").change(function() {
		$("#NewEditActivityValid").validate().resetForm();//清除校验
        if (this.value == '0') {
        	$("#actStartDays").removeClass("hide"); 
        	$("#actStartDays").show();
        	$("#actWeekDays").hide();
        }else if (this.value == '1') {
        	$("#actWeekDays").removeClass("hide"); 
            $("#actWeekDays").show();
        	$("#actStartDays").hide();
        }
    });
	
	
	//验证上传文件类型并即时显示
	$("#exampleInputFile").change(function () {
		var message = $("#see_image");
	    var filepath = $("#exampleInputFile").val();
	    var extStart = filepath.lastIndexOf(".");
	    var ext = filepath.substring(extStart, filepath.length).toUpperCase();
	    if (ext == ".PNG" || ext == ".JPG" || ext == ".JPEG") {
	    	message.empty();
	    	message.html("<img src='"+getObjectURL($(this)[0].files[0])+"' id='preview' class='img-responsive' height='125' width='200' />");
	    } else {
	      	message.empty();
	      	message.css("color","red");
	      	message.html("仅支持<br/>JPG、PNG、JPEG格式的图片");
	      	return false;
	    }
	    return true;
	});
	
	//通过不同的方式进行校验
	jQuery.validator.addMethod("dateCheck", function(value, element) {
	    var tel = true;
	    var way = $("input[name='tx_activity_activity[activity][way]']:checked").val();
    	if(way==0){
    		if($(element).attr("id")=="sttimeval" && value==""){
    			tel = false;
    		}else if($(element).attr("id")=="overtimeval" && value==""){
        		tel = false;
        	}
        }else if(way==1){
        	if($(element).attr("id")=="week" && value==""){
    			tel = false;
    		}else if($(element).attr("id")=="hour" && value==""){
        		tel = false;
        	}
        }
	    return tel;
	}, "请选择");
	
	jQuery.validator.addMethod("imageCheck", function(value, element) {
	    var tel = true;
	    var pt = $("#imgpath").val();
	    if((pt=="" || pt==null)){
	    	if(value=="" || value == null){
	    		tel=false;
		    }
	    }
	    return tel;
	}, "请上传一张照片!");
	
	jQuery.validator.addMethod("isTelphone", function(value, element) { 
	    var tel = /^1[2|3|4|5|6|7|8|9][0-9]\d{4,8}$/;
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
