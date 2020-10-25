(function($){
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
		//账号信息校验
		$("#EditOrgUserValid").validate({
			errorElement: "span", 
			errorPlacement: function(error, element) {
				if (element.is(":checkbox")||element.is(":radio")){
					error.appendTo(element.parent());
				}else{
					error.insertAfter(element);
				}
			},
			rules: {
				'tx_user_user[user][username]': {
					required: true,
					remote:{
			            url: $("#ajaxdata").val(),
			            type: 'post',
			            dataType: 'json',
				        data: {
				        	'act': function(){
				            	return "checkUserName";
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            },
				            'username':function(){
				            	return $("#susername").val();
				            },
			            }
				    } 
				},
				'tx_user_user[user][name]': {
					required: true
				},
				'tx_user_user[user][email]': {
					required: true,
					email:true,
					remote:{
			            url: $("#ajaxdata").val(),
			            type: 'post',
			            dataType: 'json',
				        data: {
				        	'act': function(){
				            	return "checkEmail";
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            },
				            'email':function(){
				            	return $("#semail").val();
				            },
			            }
				    } 
				},
				'tx_user_user[user][telephone]': {
					required: true,
					isTelphone:true,
					remote:{
			            url:$("#ajaxdata").val(),
			            type: 'post',
			            dataType: 'json',
				        data: {
				        	'act': function(){
				            	return "checkTelephone";
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            },
				            'telephone':function(){
				            	return $("#stelephone").val();
				            },
			            }
				    } 
				}
			},
			messages: {
				'tx_user_user[user][username]': {
					required: "请输入用户名!",
					remote:"用户名重复！"
				},
				'tx_user_user[user][name]': {
					required: "请输入名字!"
				},
				'tx_user_user[user][email]': {
					required: "请输入邮箱!",
					email:'请输入正确的邮箱！',
					remote:"邮箱重复！"
				},
				'tx_user_user[user][telephone]': {
					required: "请输入联系电话!",
					isTelphone:'请输入正确的手机号！',
					remote:"手机号重复！"
				}
			}   
        });
		

		//密码校验
		$("#EditPasswordValid").validate({
			errorElement: "span", 
			errorPlacement: function(error, element) {
				if (element.is(":checkbox")||element.is(":radio")){
					error.appendTo(element.parent());
				}else{
					error.insertAfter(element);
				}
			},
			rules: {
				'tx_user_user[passwordconfirmation]': {
					required: true,
					remote:{
			            url: $("#ajaxdata").val(),
			            type: 'post',
			            dataType: 'json',
				        data: {
				        	'act': function(){
				            	return "checkPassword";
				            },
				            'uid':function(){
				            	return $("#uid").val();
				            },
				            'oldpassword':function(){
				            	return $("#inputPassword1").val();
				            },
			            }
				    } 
				},
				'tx_user_user[passwords]': {
					required: true,
					minlength:6
				},
				'tx_user_user[password_rep]': {
					required: true,
					equalTo:"#inputPassword2"
				}
			},
			messages: {
				'tx_user_user[passwordconfirmation]': {
					required: "请输入原密码!",
					remote:"原密码错误！"
				},
				'tx_user_user[passwords]': {
					required: "请输入密码!",
					minlength:"密码不能小于6位！"
				},
				'tx_user_user[password_rep]': {
					required: "请输入确认密码!",
					equalTo:"两次输入密码不一致"
				}
			}   
        });
	    jQuery.validator.addMethod("isTelphone", function(value, element) {
	    	var tel = /^1[3|4|5|6|7|8|9][0-9]\d{8}$/;
			return this.optional(element) || (tel.test(value));
		}, "手机号输入错误");
			
		jQuery.validator.addMethod("isPostcode", function(value, element) { 
		    var tel = /^[1-9]\d{5}(?!\d)$/;
		    return this.optional(element) || (tel.test(value));
		}, "邮编错误");
    });
})(jQuery);

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