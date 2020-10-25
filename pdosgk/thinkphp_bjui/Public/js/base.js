
function checkstatus()
{
	if($('.glyphicon-remove').length>0)
	{
		alert('请检查权限和目录');
		return false;
	}
}

$(function(){
	$('form').validate({

		dblink:{
			rule:{
				required:true
			},
			error:{
				required:"数据库链接地址必须填写"
			},
			message:'请输入数据库链接地址'

		},
		dbname:{
			rule:{
				required:true
			},
			error:{
				required:"数据库名称必须填写"
			},
			message:'请输入数据库名称'

		},
		dbport:{
			rule:{
				required:true
			},
			error:{
				required:"数据库端口必须填写"
			},
			message:'请输入数据库端口'

		},
		dbprefix:{
			rule:{
				required:true
			},
			error:{
				required:"数据库表前缀必须填写"
			},
			message:'请输入数据库表前缀'

		},
		dbuser:{
			rule:{
				required:true
			},
			error:{
				required:"数据库用户名必须填写"
			},
			message:'请输入数据库用户名'
		},
/*		dbpassword:{
			rule:{
				ajax:{
					url:dbUrl,
					field:["dblink","dbuser"]
				}
			},
			error:{
				ajax:'数据库密码错误'
			},
			message:'请输入数据库链接密码'

		},*/
		username:{
			rule:{
				required:true
			},
			error:{
				required:"用户名必须填写"
			},
			message:'请输入用户名'

		},
		nickname:{
			rule:{
				required:true
			},
			error:{
				required:"昵称必须填写"
			},
			message:'请输入昵称'

		},
		password:{
			rule:{
				required:true
			},
			error:{
				required:"密码必须填写"
			},
			message:'请输入密码'

		},
		email:{
			rule:{
				required:true,
				email:true,
			},
			error:{
				required:"邮箱必须填写",
				email:'邮箱格式不对'
			},
			message:'请输入邮箱'

		},
	})



	$('#install').click(function(){


		

		// 验证数据库
		$.ajax({

			url:dbUrl,
			type:'post',
			dataType:'json',
			data:{
				dblink:$('#dblink').val(),
				dbuser:$('#dbuser').val(),
				dbpassword:$('#dbpassword').val(),
				dbname:$('#dbname').val(),
				dbport:$('#dbport').val(),
			},
			success:function(data){

				if(data==0)
				{
					 hd_alert({
			            message: '数据库链接错误',//显示内容
			            timeout: 3,//显示时间
			           
			        })

				}
				else
				{
					$('form').submit();
				}
			}

		})
   		return false;

	})

})
