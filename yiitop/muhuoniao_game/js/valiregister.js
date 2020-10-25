/**
 * 
 */

$(function(){
	
	//验证用户名是否合法 是否已存在 用户名id为Member_mname
	$("#Member_mname").bind("blur",function(){
		valiMname();
	});
	
	//验证密码时调用 密码一的id为Member_password
	$("#Member_password").bind("blur",function(){
		valiPassword();
	});
	
	//验证确认密码时调用 密码二的id为cpassword
	$("#cpassword").bind("blur",function(){
		valiCpassword();
	});
	
	//验证邮箱时调用 邮箱id为Member_email
	$("#Member_email").bind("blur",function(){
		valiEmail();
	});
	
	//验证真实姓名时调用 id为Member_real_name
	$("#Member_real_name").bind("blur",function(){
		valiRealname();
	});
	
	//验证身份证号时调用 id为Member_id_card
	$("#Member_id_card").bind("blur",function(){
		valiIdcard();
	});
	
	//验证用户验证码输入 id为 code
	$("#code").bind("blur",function(){
		valiCode();
	});
	
})
//用户提交表带时调用


function valiMname()
{
	var flag;
	var $mname = $("#Member_mname").val();
	var Re = /^[0-9a-z]{5,11}$/;
	if($mname==""){
		$("#Member_mname").siblings('span').removeClass().addClass('cuowu').text('用户名不能为空');
		return false;
	}else{
		if(!Re.test($mname)){
			$("#Member_mname").siblings('span').removeClass().addClass('cuowu').text('用户名必须是5到11位的字母和数字的组合');
			return false;
		}else{
			$.ajax({
				url:'/site/ajax',
				type:'POST',
				async:false,
				data:{mname:$mname},
				success:function(data)
				{
					if(data=='mnamefalse'){
						$("#Member_mname").siblings('span').removeClass().addClass('cuowu').text('用户名已存在');
						flag = false;
					}else{
						$("#Member_mname").siblings('span').removeClass().addClass('dui').text('');
						flag = true;
					}
				}
			})
			return flag;
		}
	}
}
function valiPassword()
{
	var $password = $("#Member_password").val();
	var Re = /^[-`=\\\[\];',\.~\/!@#$%^&*()_+|{}:"<>?0-9a-zA-Z]{6,15}$/; 
	if($password==""){
		$("#Member_password").siblings('span').removeClass().addClass('cuowu').text('密码不能为空');
		return false;
	}else{
		if(!Re.test($password)){
			$("#Member_password").siblings('span').removeClass().addClass('cuowu').text('密码必须是6到15位的字符串');
			return false;
		}else{
			$("#Member_password").siblings('span').removeClass().addClass('dui').text('');
			return true;
		}
	}
}
function valiCpassword()
{
	var $password = $("#Member_password").val();
	var $cpassword = $("#cpassword").val();
	if($password!=$cpassword){
		$("#cpassword").siblings('span').removeClass().addClass('cuowu').text('二次输入密码不一致');
		return false;
	}else{
		$("#cpassword").siblings('span').removeClass().addClass('dui').text('');
		return true;
	}
}
function valiEmail()
{
	var $email = $("#Member_email").val();
	var Re = /^[a-zA-Z0-9]+[^\s]*@[a-zA-Z0-9]+\.[a-zA-Z]{2,5}$/;
	if($email==""){
		$("#Member_email").siblings('span').removeClass().addClass('cuowu').text('邮箱不能为空');
		return false;
	}else{
		if(!Re.test($email)){
			$("#Member_email").siblings('span').removeClass().addClass('cuowu').text('邮箱格式不正确');
			return false;
		}else{
			$("#Member_email").siblings('span').removeClass().addClass('dui').text('');
			return true;
		}
	}
}
function valiRealname()
{
	var $real_name = $("#Member_real_name").val();
	var Re = /^[\u4e00-\u9fa5]{2,4}$/;
	if($real_name!=""){
		if(!Re.test($real_name)){
			$("#Member_real_name").siblings('span').removeClass().addClass('cuowu').text('真实姓名必须是合法汉字');
			return false;
		}else{
			$("#Member_real_name").siblings('span').removeClass().addClass('dui').text('');
			return true;
		}
	}else{
		$("#Member_real_name").siblings('span').removeClass().text('');
		return true;
	}
}
function valiIdcard()
{
	var idcard = $("#Member_id_card").val();
		if(idcard!=""){
			var area={11:"北京",12:"天津",13:"河北",14:"山西",15:"内蒙古",21:"辽宁",22:"吉林",23:"黑龙江",31:"上海",32:"江苏",33:"浙江",34:"安徽",35:"福建",36:"江西",37:"山东",41:"河南",42:"湖北",43:"湖南",44:"广东",45:"广西",46:"海南",50:"重庆",51:"四川",52:"贵州",53:"云南",54:"西藏",61:"陕西",62:"甘肃",63:"青海",64:"宁夏",65:"新疆",71:"台湾",81:"香港",82:"澳门",91:"国外"};
			switch(idcard.length){
			case 18:
				if(area[parseInt(idcard.substr(0,2))]==null){
					$("#Member_id_card").siblings('span').removeClass().addClass('cuowu').text('身份证号地区输入错误');
					return false;
				}
				if ( parseInt(idcard.substr(6,4)) % 4 == 0 || (parseInt(idcard.substr(6,4)) % 100 == 0 && parseInt(idcard.substr(6,4))%4 == 0 ))
				{
					ereg=/^[1-9][0-9]{5}(19|20|21)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|[1-2][0-9]))[0-9]{3}[0-9Xx]$/;//闰年出生日期的合法性正则表达式
				} 
				else 
				{
					ereg=/^[1-9][0-9]{5}(19|20|21)[0-9]{2}((01|03|05|07|08|10|12)(0[1-9]|[1-2][0-9]|3[0-1])|(04|06|09|11)(0[1-9]|[1-2][0-9]|30)|02(0[1-9]|1[0-9]|2[0-8]))[0-9]{3}[0-9Xx]$/;//平年出生日期的合法性正则表达式
				}
				if(!ereg.test(idcard))
				{
					$("#Member_id_card").siblings('span').removeClass().addClass('cuowu').text('身份证号出生日期不正确');
					return false;
				}
				else
				{
					var idcard,Y,JYM;
					var S,M;
					var idcard_array = new Array();
					idcard_array = idcard.split("");
					S = (parseInt(idcard_array[0]) + parseInt(idcard_array[10])) * 7+ (parseInt(idcard_array[1]) + parseInt(idcard_array[11])) * 9+ (parseInt(idcard_array[2]) + parseInt(idcard_array[12])) * 10+ (parseInt(idcard_array[3]) + parseInt(idcard_array[13])) * 5+ (parseInt(idcard_array[4]) + parseInt(idcard_array[14])) * 8+ (parseInt(idcard_array[5]) + parseInt(idcard_array[15])) * 4+ (parseInt(idcard_array[6]) + parseInt(idcard_array[16])) * 2+ parseInt(idcard_array[7]) * 1 + parseInt(idcard_array[8]) * 6+ parseInt(idcard_array[9]) * 3 ;
					Y = S % 11;
					M = "F";
					JYM = "10X98765432";
					M = JYM.substr(Y,1);
					if(M != idcard_array[17])
					{
						$("#Member_id_card").siblings('span').removeClass().addClass('cuowu').text('身份证号效验码有误');
						return false;
					}else{
						$("#Member_id_card").siblings('span').removeClass().addClass('dui').text('');
						return true;
					}
				}
				break;
			default:
				$("#Member_id_card").siblings('span').removeClass().addClass('cuowu').text('身份证号长度不合法');
				return false;
		}
		}else{
			$("#Member_id_card").siblings('span').removeClass().text('');
			return true;
		}
}
function valiTreaty()
{
	if($("#Member_treaty").attr("checked")=="checked"){
		return true;
	}else{
		alert("请同意服务协议");
		return false;
	}
}

function valiCode()
{
	var flag;
	$.ajax({
		url:'/site/ajax',
		async:false,
		type:'POST',
		data:{code:$("#code").val()},
		success:function(data){
			if(data=='codefalse'){
				$("#code").siblings('span').removeClass().addClass('cuowu').text('验证码输入有误');
				flag = false;
			}else{
				$("#code").siblings('span').removeClass().addClass('dui').text('');
				flag = true;
			}
		}
	})
	return flag;
}















