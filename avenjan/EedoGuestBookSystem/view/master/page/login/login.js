/*
登录脚本
BY avenjan
2018年7月26日
*/
layui.use(['layer','jquery'],function(){
	var layer = layui.layer,
	$ = layui.jquery;
	$('#verify').codeVerify({
		type : 1,
		width : '',
		height : '50px',
		fontSize : '30px',
		codeLength : 4,
		btnId : 'btn',
		ready : function() {
		},
		success : function() {
		    	//alert('验证匹配！');
			   //取用户名和密码
		    var u = $("#uname").val();//取输入的用户名
		    var p = $("#pwd").val();//取输入的密码
		    //调ajax
		    $.ajax({            
			    	url:"login.php",
			    	data:{u:u,p:p},//传递参数
			    	type:"POST",
			    	dataType:"TEXT",
			    	success: function(data){
			            if(data.trim()=="OK")//返回成功事件
			            {
			            	top.layer.msg('登录成功',{icon: 16,time:false,shade:0.8});
			            	window.location.href = "/?go=master";
			            }
			            else
			            {
			            	top.layer.msg(data.trim(), function(){});//返回错误信息
			            }
			        }
			    });
		},
		error : function() {
			top.layer.msg('验证码不匹配！请重新输入', function(){});
		}
	});
	//回车键绑定登录事件
	$(".varify-input-code").bind("keydown",function(e){
			　　// 兼容FF和IE和Opera
			　　var theEvent = e || window.event;
			　　var code = theEvent.keyCode || theEvent.which || theEvent.charCode;
			　　 if (code == 13) {
			　　//绑定事件
			　　$("#btn").click();
		　　}
	});	
})